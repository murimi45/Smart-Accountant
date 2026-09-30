<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\EmployeeDeduction;
use App\Models\PayrollRun;
use App\Models\Payslip;
use App\Models\PayslipLine;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PayrollRunService
{
    public function __construct(private PayrollCalculator $calculator) {}

    public function open(int $schoolId, Carbon|string $period): PayrollRun
    {
        $period = Carbon::parse($period)->startOfMonth()->toDateString();

        $existing = PayrollRun::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->whereDate('period', $period)
            ->first();

        if ($existing) {
            if ($existing->isLocked()) {
                return $existing;
            }

            $this->addMissingEmployees($existing);

            return $existing->fresh('payslips');
        }

        $otherDraft = PayrollRun::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('status', PayrollRun::STATUS_DRAFT)
            ->exists();

        if ($otherDraft) {
            throw new RuntimeException('Lock the open payroll month before starting another.');
        }

        return DB::transaction(function () use ($schoolId, $period) {
            $run = PayrollRun::createForSchool($schoolId, [
                'period' => $period,
                'status' => PayrollRun::STATUS_DRAFT,
            ]);

            foreach ($this->activeEmployees($schoolId) as $employee) {
                $this->createPayslip($run, $employee);
            }

            return $run->fresh('payslips');
        });
    }

    public function updatePayslip(Payslip $payslip, bool $included, float $adjustment): Payslip
    {
        $run = $this->draftRun($payslip->payroll_run_id, (int) $payslip->school_id);

        $payslip->update([
            'included' => $included,
            'adjustment' => round($adjustment, 2),
        ]);

        $this->fillPayslip($payslip->fresh(), $run);

        return $payslip->fresh('lines');
    }

    public function recalculate(PayrollRun $run): PayrollRun
    {
        $run = $this->draftRun($run->id, (int) $run->school_id);
        $this->addMissingEmployees($run);

        foreach ($run->payslips()->get() as $payslip) {
            $this->fillPayslip($payslip, $run);
        }

        return $run->fresh('payslips.lines');
    }

    public function lock(PayrollRun $run, int $userId): PayrollRun
    {
        $run = $this->draftRun($run->id, (int) $run->school_id);

        return DB::transaction(function () use ($run, $userId) {
            $this->recalculate($run);
            $run = $run->fresh('payslips.lines');

            foreach ($run->payslips as $payslip) {
                if (! $payslip->included) {
                    continue;
                }

                $this->reduceBalances($payslip);
            }

            $run->update([
                'status' => PayrollRun::STATUS_LOCKED,
                'locked_at' => now(),
                'locked_by' => $userId,
            ]);

            return $run->fresh('payslips.lines');
        });
    }

    /** @return array{headcount: int, gross: float, deductions: float, net_pay: float, employer_cost: float} */
    public function totals(PayrollRun $run): array
    {
        $slips = Payslip::withoutGlobalScopes()
            ->where('payroll_run_id', $run->id)
            ->where('included', true)
            ->get();

        $gross = round($slips->sum(fn (Payslip $slip) => (float) $slip->gross), 2);
        $net = round($slips->sum(fn (Payslip $slip) => (float) $slip->net_pay), 2);

        return [
            'headcount' => $slips->count(),
            'gross' => $gross,
            'deductions' => round($gross - $net, 2),
            'net_pay' => $net,
            'employer_cost' => round($slips->sum(fn (Payslip $slip) => (float) $slip->employer_cost), 2),
        ];
    }

    private function draftRun(int $runId, int $schoolId): PayrollRun
    {
        $run = PayrollRun::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->findOrFail($runId);

        if ($run->isLocked()) {
            throw new RuntimeException('This payroll month is locked.');
        }

        return $run;
    }

    private function addMissingEmployees(PayrollRun $run): void
    {
        $present = Payslip::withoutGlobalScopes()
            ->where('payroll_run_id', $run->id)
            ->pluck('employee_id');

        $missing = $this->activeEmployees((int) $run->school_id)
            ->whereNotIn('id', $present);

        foreach ($missing as $employee) {
            $this->createPayslip($run, $employee);
        }
    }

    /** @return \Illuminate\Support\Collection<int, Employee> */
    private function activeEmployees(int $schoolId)
    {
        return Employee::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('status', Employee::STATUS_ACTIVE)
            ->orderBy('full_name')
            ->get();
    }

    private function createPayslip(PayrollRun $run, Employee $employee): Payslip
    {
        $payslip = Payslip::createForSchool((int) $run->school_id, [
            'payroll_run_id' => $run->id,
            'employee_id' => $employee->id,
            'staff_number' => $employee->staff_number,
            'full_name' => $employee->full_name,
            'included' => true,
            'adjustment' => 0,
        ]);

        $this->fillPayslip($payslip, $run);

        return $payslip;
    }

    private function fillPayslip(Payslip $payslip, PayrollRun $run): void
    {
        $employee = Employee::withoutGlobalScopes()
            ->where('school_id', $run->school_id)
            ->findOrFail($payslip->employee_id);

        PayslipLine::withoutGlobalScopes()
            ->where('payslip_id', $payslip->id)
            ->delete();

        $payslip->update([
            'staff_number' => $employee->staff_number,
            'full_name' => $employee->full_name,
        ]);

        if (! $payslip->included) {
            $payslip->update([
                'gross' => 0,
                'nssf_employee' => 0,
                'nssf_employer' => 0,
                'shif' => 0,
                'housing_employee' => 0,
                'housing_employer' => 0,
                'taxable_pay' => 0,
                'paye' => 0,
                'school_deductions_total' => 0,
                'net_pay' => 0,
                'employer_cost' => 0,
            ]);

            return;
        }

        $gross = round($this->calculator->grossFromGrade($employee) + (float) $payslip->adjustment, 2);
        $result = $this->calculator->calculate($employee, $gross, $run->period);

        $payslip->update([
            'gross' => $result['gross'],
            'nssf_employee' => $result['nssf_employee'],
            'nssf_employer' => $result['nssf_employer'],
            'shif' => $result['shif'],
            'housing_employee' => $result['housing_employee'],
            'housing_employer' => $result['housing_employer'],
            'taxable_pay' => $result['taxable_pay'],
            'paye' => $result['paye'],
            'school_deductions_total' => $result['school_deductions_total'],
            'net_pay' => $result['net_pay'],
            'employer_cost' => $result['employer_cost'],
        ]);

        $this->writeLines($payslip, $result);
    }

    /** @param array<string, mixed> $result */
    private function writeLines(Payslip $payslip, array $result): void
    {
        $lines = [
            [PayslipLine::NSSF_EMPLOYEE, 'NSSF', PayslipLine::SIDE_EMPLOYEE, $result['nssf_employee'], null],
            [PayslipLine::NSSF_EMPLOYER, 'NSSF employer', PayslipLine::SIDE_EMPLOYER, $result['nssf_employer'], null],
            [PayslipLine::SHIF, 'SHIF', PayslipLine::SIDE_EMPLOYEE, $result['shif'], null],
            [PayslipLine::HOUSING_EMPLOYEE, 'Housing levy', PayslipLine::SIDE_EMPLOYEE, $result['housing_employee'], null],
            [PayslipLine::HOUSING_EMPLOYER, 'Housing levy employer', PayslipLine::SIDE_EMPLOYER, $result['housing_employer'], null],
            [PayslipLine::PAYE, 'PAYE', PayslipLine::SIDE_EMPLOYEE, $result['paye'], null],
        ];

        foreach ($result['school_deductions'] as $deduction) {
            $lines[] = [PayslipLine::SCHOOL, $deduction['name'], PayslipLine::SIDE_EMPLOYEE, $deduction['amount'], $deduction['id']];
        }

        foreach ($lines as $order => [$code, $name, $side, $amount, $deductionId]) {
            if ((float) $amount <= 0) {
                continue;
            }

            PayslipLine::createForSchool((int) $payslip->school_id, [
                'payslip_id' => $payslip->id,
                'employee_deduction_id' => $deductionId,
                'name' => $name,
                'code' => $code,
                'side' => $side,
                'amount' => $amount,
                'sort_order' => $order + 1,
            ]);
        }
    }

    private function reduceBalances(Payslip $payslip): void
    {
        $lines = PayslipLine::withoutGlobalScopes()
            ->where('payslip_id', $payslip->id)
            ->where('code', PayslipLine::SCHOOL)
            ->whereNotNull('employee_deduction_id')
            ->get();

        foreach ($lines as $line) {
            $deduction = EmployeeDeduction::withoutGlobalScopes()->find($line->employee_deduction_id);

            if (! $deduction || $deduction->kind !== EmployeeDeduction::KIND_BALANCE) {
                continue;
            }

            $remaining = round((float) $deduction->balance_remaining - (float) $line->amount, 2);

            $deduction->update([
                'balance_remaining' => max(0, $remaining),
                'is_active' => $remaining > 0.009,
            ]);
        }
    }
}