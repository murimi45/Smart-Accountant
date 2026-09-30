<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\EmployeeDeduction;
use App\Models\SalaryGrade;
use App\Models\SalaryGradeAllowance;
use App\Services\PayrollCalculator;
use Database\Seeders\StatutoryRatesSeeder;
use RuntimeException;
use Tests\Support\TenantFixtureBuilder;
use Tests\TestCase;

class PayrollCalculatorTest extends TestCase
{
    private array $fixtures;

    private PayrollCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixtures = TenantFixtureBuilder::createPair();
        $this->seed(StatutoryRatesSeeder::class);
        $this->calculator = app(PayrollCalculator::class);
    }

    public function test_march_2026_statutory_lines_for_gross_of_50000(): void
    {
        $employee = $this->employee('T001');

        $result = $this->calculator->calculate($employee, 50000, '2026-03-01');

        $this->assertMoney(50000, $result['gross']);
        $this->assertMoney(3000, $result['nssf_employee']);
        $this->assertMoney(3000, $result['nssf_employer']);
        $this->assertMoney(1375, $result['shif']);
        $this->assertMoney(750, $result['housing_employee']);
        $this->assertMoney(750, $result['housing_employer']);
        $this->assertMoney(44875, $result['taxable_pay']);
        $this->assertMoney(5845.85, $result['paye']);
        $this->assertMoney(39029.15, $result['net_pay']);
        $this->assertMoney(53750, $result['employer_cost']);
        $this->assertSame([], $result['school_deductions']);
    }

    public function test_nssf_uses_the_limits_in_force_on_the_payroll_date(): void
    {
        $employee = $this->employee('T002');

        $january = $this->calculator->calculate($employee, 200000, '2026-01-15');
        $march = $this->calculator->calculate($employee, 200000, '2026-03-01');

        $this->assertMoney(4320, $january['nssf_employee']);
        $this->assertMoney(6480, $march['nssf_employee']);
        $this->assertMoney(6480, $march['nssf_employer']);
    }

    public function test_shif_uses_the_minimum_when_the_percent_is_lower(): void
    {
        $employee = $this->employee('T003');

        $result = $this->calculator->calculate($employee, 5000, '2026-03-01');

        $this->assertMoney(300, $result['shif']);
    }

    public function test_school_deductions_come_off_net_pay_and_do_not_reduce_the_loan_balance(): void
    {
        $employee = $this->employee('T004');
        $schoolId = (int) $this->fixtures['schoolA']->id;

        EmployeeDeduction::createForSchool($schoolId, [
            'employee_id' => $employee->id,
            'name' => 'SACCO',
            'kind' => EmployeeDeduction::KIND_RECURRING,
            'amount' => 1000,
        ]);

        $loan = EmployeeDeduction::createForSchool($schoolId, [
            'employee_id' => $employee->id,
            'name' => 'Advance',
            'kind' => EmployeeDeduction::KIND_BALANCE,
            'amount' => 2000,
            'balance_remaining' => 500,
        ]);

        EmployeeDeduction::createForSchool($schoolId, [
            'employee_id' => $employee->id,
            'name' => 'Old welfare',
            'kind' => EmployeeDeduction::KIND_RECURRING,
            'amount' => 999,
            'is_active' => false,
        ]);

        $result = $this->calculator->calculate($employee, 50000, '2026-03-01');

        $this->assertMoney(44875, $result['taxable_pay']);
        $this->assertMoney(1500, $result['school_deductions_total']);
        $this->assertMoney(37529.15, $result['net_pay']);
        $this->assertSame(['Advance', 'SACCO'], array_column($result['school_deductions'], 'name'));
        $this->assertMoney(500, (float) $loan->fresh()->balance_remaining);
    }

    public function test_another_schools_deduction_is_not_applied(): void
    {
        $employee = $this->employee('T005');

        EmployeeDeduction::createForSchool((int) $this->fixtures['schoolB']->id, [
            'employee_id' => $this->employeeOnSchoolB()->id,
            'name' => 'Other school SACCO',
            'kind' => EmployeeDeduction::KIND_RECURRING,
            'amount' => 4000,
        ]);

        $result = $this->calculator->calculate($employee, 50000, '2026-03-01');

        $this->assertSame([], $result['school_deductions']);
        $this->assertMoney(39029.15, $result['net_pay']);
    }

    public function test_gross_from_grade_includes_basic_pay_and_allowances(): void
    {
        $schoolId = (int) $this->fixtures['schoolA']->id;
        $grade = SalaryGrade::createForSchool($schoolId, [
            'name' => 'Teacher',
            'basic_pay' => 40000,
        ]);
        SalaryGradeAllowance::createForSchool($schoolId, [
            'salary_grade_id' => $grade->id,
            'name' => 'Commuter',
            'amount' => 5000,
        ]);

        $employee = Employee::createForSchool($schoolId, [
            'staff_number' => 'T006',
            'full_name' => 'Grade Teacher',
            'status' => Employee::STATUS_ACTIVE,
            'salary_grade_id' => $grade->id,
        ]);

        $this->assertMoney(45000, $this->calculator->grossFromGrade($employee));
    }

    public function test_negative_gross_is_rejected(): void
    {
        $employee = $this->employee('T007');

        $this->expectException(RuntimeException::class);

        $this->calculator->calculate($employee, -1, '2026-03-01');
    }

    private function employee(string $staffNumber): Employee
    {
        return Employee::createForSchool((int) $this->fixtures['schoolA']->id, [
            'staff_number' => $staffNumber,
            'full_name' => 'Payroll Staff '.$staffNumber,
            'status' => Employee::STATUS_ACTIVE,
        ]);
    }

    private function employeeOnSchoolB(): Employee
    {
        return Employee::createForSchool((int) $this->fixtures['schoolB']->id, [
            'staff_number' => 'B001',
            'full_name' => 'School B Staff',
            'status' => Employee::STATUS_ACTIVE,
        ]);
    }

    private function assertMoney(float $expected, float $actual): void
    {
        $this->assertEqualsWithDelta($expected, $actual, 0.001);
    }
}
