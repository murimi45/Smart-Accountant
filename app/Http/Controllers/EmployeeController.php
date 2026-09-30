<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\SalaryGrade;

use App\Support\TenantRules;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Employee::class);

        $employees = Employee::with('grade')
            ->orderBy('full_name')
            ->paginate(20);

        return view('payroll.employees.index', compact('employees'));
    }

    public function create()
    {
        $this->authorize('create', Employee::class);

        return view('payroll.employees.form', [
            'employee' => new Employee(['status' => Employee::STATUS_ACTIVE]),
            'grades' => SalaryGrade::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Employee::class);

        Employee::create($this->validated($request));

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee saved.');
    }

    public function edit($id)
    {
        $employee = Employee::forSchool()->with('deductions')->findOrFail($id);
        $this->authorize('update', $employee);

        return view('payroll.employees.form', [
            'employee' => $employee,
            'grades' => SalaryGrade::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::forSchool()->findOrFail($id);
        $this->authorize('update', $employee);

        $employee->update($this->validated($request, $employee->id));

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee updated.');
    }

   

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'staff_number' => ['required', 'string', 'max:50', TenantRules::unique('employees', 'staff_number', $ignoreId)],
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'status' => ['required', Rule::in([Employee::STATUS_ACTIVE, Employee::STATUS_LEFT])],
            'start_date' => ['nullable', 'date'],
            'kra_pin' => ['nullable', 'string', 'max:20'],
            'nssf_number' => ['nullable', 'string', 'max:50'],
            'shif_number' => ['nullable', 'string', 'max:50'],
            'payment_method' => ['nullable', Rule::in([Employee::PAY_BANK, Employee::PAY_MPESA, Employee::PAY_CASH])],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:50'],
            'salary_grade_id' => ['nullable', TenantRules::exists('salary_grades')],
            'school_id' => TenantRules::prohibitedSchoolId(),
        ]);

        unset($data['school_id']);

        return $data;
    }
}