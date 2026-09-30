<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\EmployeeDeduction;
use App\Models\PayeBand;
use App\Models\StatutoryRate;
use Illuminate\Support\Carbon;
use RuntimeException;

class PayrollCalculator
{
    /**
     * @return array{
     *     gross: float,
     *     nssf_employee: float,
     *     nssf_employer: float,
     *     shif: float,
     *     housing_employee: float,
     *     housing_employer: float,
     *     taxable_pay: float,
     *     paye: float,
     *     school_deductions: list<array{id: int, name: string, amount: float}>,
     *     school_deductions_total: float,
     *     net_pay: float,
     *     employer_cost: float
     * }
     */
    public function calculate(Employee $employee, float $gross, Carbon|string $asOf): array
    {
        if ($gross < 0) {
            throw new RuntimeException('Gross pay cannot be negative.');
        }

        $gross = round($gross, 2);
        $asOf = Carbon::parse($asOf);

        $nssfEmployee = 0.0;
        $nssfEmployer = 0.0;

        foreach ([StatutoryRate::NSSF_TIER1, StatutoryRate::NSSF_TIER2] as $code) {
            $rate = $this->rate($code, $asOf);
            $nssfEmployee += $this->tierShare($gross, $rate, 'employee');
            $nssfEmployer += $this->tierShare($gross, $rate, 'employer');
        }

        $nssfEmployee = round($nssfEmployee, 2);
        $nssfEmployer = round($nssfEmployer, 2);

        $shif = $gross > 0 ? $this->percentWithMinimum($gross, $this->rate(StatutoryRate::SHIF, $asOf), 'employee') : 0.0;
        $housing = $this->rate(StatutoryRate::HOUSING_LEVY, $asOf);
        $housingEmployee = $this->percent($gross, $housing->employee_rate);
        $housingEmployer = $this->percent($gross, $housing->employer_rate);

        $taxable = round(max(0, $gross - $nssfEmployee - $shif - $housingEmployee), 2);
        $paye = $this->paye($taxable, $asOf);

        $schoolLines = $this->schoolDeductions($employee);
        $schoolTotal = round(array_sum(array_column($schoolLines, 'amount')), 2);

        $net = round($gross - $nssfEmployee - $shif - $housingEmployee - $paye - $schoolTotal, 2);
        $employerCost = round($gross + $nssfEmployer + $housingEmployer, 2);

        return [
            'gross' => $gross,
            'nssf_employee' => $nssfEmployee,
            'nssf_employer' => $nssfEmployer,
            'shif' => $shif,
            'housing_employee' => $housingEmployee,
            'housing_employer' => $housingEmployer,
            'taxable_pay' => $taxable,
            'paye' => $paye,
            'school_deductions' => $schoolLines,
            'school_deductions_total' => $schoolTotal,
            'net_pay' => $net,
            'employer_cost' => $employerCost,
        ];
    }

    public function grossFromGrade(Employee $employee): float
    {
        $employee->loadMissing('grade.allowances');

        if (! $employee->grade) {
            return 0.0;
        }

        $allowances = $employee->grade->allowances->sum(fn ($allowance) => (float) $allowance->amount);

        return round((float) $employee->grade->basic_pay + $allowances, 2);
    }

    private function rate(string $code, Carbon $asOf): StatutoryRate
    {
        $rate = StatutoryRate::rateForDate($code, $asOf);

        if (! $rate) {
            throw new RuntimeException("No {$code} rate is in force on {$asOf->toDateString()}.");
        }

        return $rate;
    }

    private function tierShare(float $gross, StatutoryRate $rate, string $side): float
    {
        $lower = (float) $rate->lower_limit;
        $upper = (float) $rate->upper_limit;
        $base = max(0, min($gross, $upper) - $lower);
        $percent = $side === 'employee' ? $rate->employee_rate : $rate->employer_rate;

        return $this->percent($base, $percent);
    }

    private function percentWithMinimum(float $gross, StatutoryRate $rate, string $side): float
    {
        $amount = $this->percent($gross, $side === 'employee' ? $rate->employee_rate : $rate->employer_rate);
        $minimum = (float) ($rate->minimum_amount ?? 0);

        return round(max($amount, $minimum), 2);
    }

    private function percent(float $base, mixed $rate): float
    {
        return round($base * (float) $rate / 100, 2);
    }

    private function paye(float $taxable, Carbon $asOf): float
    {
        $bands = PayeBand::bandsForDate($asOf);

        if ($bands->isEmpty()) {
            throw new RuntimeException("No PAYE bands are in force on {$asOf->toDateString()}.");
        }

        $tax = 0.0;

        foreach ($bands as $band) {
            $lower = (float) $band->lower_limit;
            $upper = $band->upper_limit === null ? $taxable : (float) $band->upper_limit;

            if ($taxable <= $lower) {
                continue;
            }

            $slice = min($taxable, $upper) - $lower;
            $tax += $slice * (float) $band->rate / 100;
        }

        $relief = (float) ($this->rate(StatutoryRate::PERSONAL_RELIEF, $asOf)->fixed_amount ?? 0);

        return round(max(0, $tax - $relief), 2);
    }

    /** @return list<array{id: int, name: string, amount: float}> */
    private function schoolDeductions(Employee $employee): array
    {
        $deductions = EmployeeDeduction::withoutGlobalScopes()
            ->where('school_id', $employee->school_id)
            ->where('employee_id', $employee->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $lines = [];

        foreach ($deductions as $deduction) {
            $amount = $this->schoolAmount($deduction);

            if ($amount <= 0) {
                continue;
            }

            $lines[] = [
                'id' => $deduction->id,
                'name' => $deduction->name,
                'amount' => $amount,
            ];
        }

        return $lines;
    }

    private function schoolAmount(EmployeeDeduction $deduction): float
    {
        $installment = round((float) $deduction->amount, 2);

        if ($deduction->kind === EmployeeDeduction::KIND_RECURRING) {
            return $installment;
        }

        $remaining = round((float) $deduction->balance_remaining, 2);

        return round(min($installment, max(0, $remaining)), 2);
    }
}