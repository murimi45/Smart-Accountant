<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeDeduction;
use App\Support\TenantRules;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeDeductionController extends Controller
{
    public function store(Request $request, $employeeId)
    {
        $employee = Employee::forSchool()->findOrFail($employeeId);
        $this->authorize('update', $employee);
        $this->authorize('create', EmployeeDeduction::class);

        EmployeeDeduction::create(array_merge(
            $this->validated($request),
            ['employee_id' => $employee->id]
        ));

        return redirect()
            ->route('employees.edit', $employee->id)
            ->with('success', 'Deduction saved.');
    }

    public function update(Request $request, $employeeId, $deductionId)
    {
        $employee = Employee::forSchool()->findOrFail($employeeId);
        $deduction = $this->findDeduction($employee, $deductionId);
        $this->authorize('update', $deduction);

        $deduction->update($this->validated($request));

        return redirect()
            ->route('employees.edit', $employee->id)
            ->with('success', 'Deduction updated.');
    }

    public function stop($employeeId, $deductionId)
    {
        $employee = Employee::forSchool()->findOrFail($employeeId);
        $deduction = $this->findDeduction($employee, $deductionId);
        $this->authorize('update', $deduction);

        $deduction->update(['is_active' => false]);

        return redirect()
            ->route('employees.edit', $employee->id)
            ->with('success', 'Deduction stopped.');
    }

    private function findDeduction(Employee $employee, $deductionId): EmployeeDeduction
    {
        return EmployeeDeduction::forSchool()
            ->where('employee_id', $employee->id)
            ->findOrFail($deductionId);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'kind' => ['required', Rule::in([
                EmployeeDeduction::KIND_RECURRING,
                EmployeeDeduction::KIND_BALANCE,
            ])],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'balance_remaining' => ['nullable', 'numeric', 'min:0.01'],
            'school_id' => TenantRules::prohibitedSchoolId(),
        ]);

        unset($data['school_id']);

        if ($data['kind'] === EmployeeDeduction::KIND_BALANCE && empty($data['balance_remaining'])) {
            $data['balance_remaining'] = $data['amount'];
        }

        if ($data['kind'] === EmployeeDeduction::KIND_RECURRING) {
            $data['balance_remaining'] = null;
        }

        return $data;
    }
}