<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\EmployeeDeduction;
use App\Models\PayrollRun;
use App\Models\Payslip;
use App\Models\SalaryGrade;
use App\Services\PayrollRunService;
use Database\Seeders\StatutoryRatesSeeder;
use RuntimeException;
use Tests\Support\TenantFixtureBuilder;
use Tests\TestCase;

class PayrollRunTest extends TestCase
{
    private array $fixtures;

    private PayrollRunService $runs;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixtures = TenantFixtureBuilder::createPair();
        $this->seed(StatutoryRatesSeeder::class);
        $this->runs = app(PayrollRunService::class);
    }

    public function test_open_month_pays_active_employees_from_their_grade(): void
    {
        $schoolId = (int) $this->fixtures['schoolA']->id;
        $this->employee($schoolId, 'T001', 'Active Teacher', 50000);
        $this->employee($schoolId, 'T002', 'Former Teacher', 50000, Employee::STATUS_LEFT);
        $this->employee((int) $this->fixtures['schoolB']->id, 'B001', 'Other School', 50000);

        $run = $this->runs->open($schoolId, '2026-03-01');
        $slip = $this->slip($run, 'T001');

        $this->assertSame(PayrollRun::STATUS_DRAFT, $run->status);
        $this->assertCount(1, $run->payslips);
        $this->assertMoney(50000, (float) $slip->gross);
        $this->assertMoney(39029.15, (float) $slip->net_pay);
        $this->assertTrue($slip->lines->contains('code', 'paye'));
    }

    public function test_second_month_matches_the_locked_month_when_nothing_changed(): void
    {
        $schoolId = (int) $this->fixtures['schoolA']->id;
        $this->employee($schoolId, 'T001', 'Active Teacher', 50000);

        $march = $this->runs->lock($this->runs->open($schoolId, '2026-03'), (int) $this->fixtures['adminA']->id);
        $april = $this->runs->open($schoolId, '2026-04');

        $this->assertMoney(
            (float) $this->slip($march, 'T001')->net_pay,
            (float) $this->slip($april, 'T001')->net_pay
        );
    }

    public function test_lock_reduces_advance_and_keeps_the_old_net_pay_after_a_grade_change(): void
    {
        $schoolId = (int) $this->fixtures['schoolA']->id;
        $employee = $this->employee($schoolId, 'T001', 'Active Teacher', 50000);

        $advance = EmployeeDeduction::createForSchool($schoolId, [
            'employee_id' => $employee->id,
            'name' => 'Advance',
            'kind' => EmployeeDeduction::KIND_BALANCE,
            'amount' => 2000,
            'balance_remaining' => 5000,
        ]);

        $march = $this->runs->lock($this->runs->open($schoolId, '2026-03'), (int) $this->fixtures['adminA']->id);
        $lockedNet = (float) $this->slip($march, 'T001')->net_pay;

        $this->assertMoney(37029.15, $lockedNet);
        $this->assertMoney(3000, (float) $advance->fresh()->balance_remaining);

        $employee->grade->update(['basic_pay' => 80000]);

        $this->assertMoney(37029.15, (float) $this->slip($march->fresh(), 'T001')->net_pay);
        $this->assertMoney(3000, (float) $advance->fresh()->balance_remaining);

        $april = $this->runs->open($schoolId, '2026-04');
        $this->assertMoney(2000, (float) $this->slip($april, 'T001')->school_deductions_total);
    }

    public function test_a_second_draft_month_is_rejected(): void
    {
        $schoolId = (int) $this->fixtures['schoolA']->id;
        $this->employee($schoolId, 'T001', 'Active Teacher', 50000);
        $this->runs->open($schoolId, '2026-03');

        $this->expectException(RuntimeException::class);

        $this->runs->open($schoolId, '2026-04');
    }

    public function test_accountant_can_prepare_a_month_from_the_screen(): void
    {
        $schoolId = (int) $this->fixtures['schoolA']->id;
        $this->employee($schoolId, 'T001', 'Active Teacher', 50000);

        $this->actingAs($this->fixtures['accountantA'])
            ->post(route('payroll.runs.store'), ['period' => '2026-03'])
            ->assertRedirect();

        $run = PayrollRun::withoutGlobalScopes()->where('school_id', $schoolId)->first();

        $this->actingAs($this->fixtures['accountantA'])
            ->get(route('payroll.runs.show', $run->id))
            ->assertOk()
            ->assertSee('Active Teacher')
            ->assertSee('39,029.15');
    }

    private function employee(int $schoolId, string $staffNumber, string $name, float $basic, string $status = Employee::STATUS_ACTIVE): Employee
    {
        $grade = SalaryGrade::createForSchool($schoolId, [
            'name' => 'Grade '.$staffNumber,
            'basic_pay' => $basic,
        ]);

        return Employee::createForSchool($schoolId, [
            'staff_number' => $staffNumber,
            'full_name' => $name,
            'status' => $status,
            'salary_grade_id' => $grade->id,
        ]);
    }

    private function slip(PayrollRun $run, string $staffNumber): Payslip
    {
        return Payslip::withoutGlobalScopes()
            ->where('payroll_run_id', $run->id)
            ->where('staff_number', $staffNumber)
            ->with('lines')
            ->firstOrFail();
    }

    private function assertMoney(float $expected, float $actual): void
    {
        $this->assertEqualsWithDelta($expected, $actual, 0.001);
    }
}
