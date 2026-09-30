<?php

namespace App\Http\Controllers;

use App\Models\SalaryGrade;
use App\Models\SalaryGradeAllowance;
use App\Support\TenantRules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SalaryGradeController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', SalaryGrade::class);

        $grades = SalaryGrade::with('allowances')
            ->withCount('employees')
            ->orderBy('name')
            ->paginate(20);

        return view('payroll.grades.index', compact('grades'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', SalaryGrade::class);

        $data = $this->validated($request);

        DB::transaction(function () use ($data) {
            $grade = SalaryGrade::create([
                'name' => $data['name'],
                'basic_pay' => $data['basic_pay'],
            ]);

            $this->syncAllowances($grade, $data['allowances']);
        });

        return redirect()
            ->route('salary_grades.index')
            ->with('success', 'Salary grade saved.');
    }

    public function update(Request $request, $id)
    {
        $grade = SalaryGrade::forSchool()->findOrFail($id);
        $this->authorize('update', $grade);

        $data = $this->validated($request, $grade->id);

        DB::transaction(function () use ($grade, $data) {
            $grade->update([
                'name' => $data['name'],
                'basic_pay' => $data['basic_pay'],
            ]);

            $this->syncAllowances($grade, $data['allowances']);
        });

        return redirect()
            ->route('salary_grades.index')
            ->with('success', 'Salary grade updated.');
    }

    public function destroy($id)
    {
        $grade = SalaryGrade::forSchool()->findOrFail($id);
        $this->authorize('delete', $grade);

        if ($grade->employees()->exists()) {
            return redirect()
                ->route('salary_grades.index')
                ->with('error', 'Move employees off this grade before deleting it.');
        }

        $grade->allowances()->delete();
        $grade->delete();

        return redirect()
            ->route('salary_grades.index')
            ->with('success', 'Salary grade deleted.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', TenantRules::unique('salary_grades', 'name', $ignoreId)],
            'basic_pay' => ['required', 'numeric', 'min:0'],
            'allowances' => ['nullable', 'array'],
            'allowances.*.name' => ['nullable', 'string', 'max:255'],
            'allowances.*.amount' => ['nullable', 'numeric', 'min:0'],
            'school_id' => TenantRules::prohibitedSchoolId(),
        ]);

        $data['allowances'] = $this->cleanAllowances($data['allowances'] ?? []);

        return $data;
    }

    private function cleanAllowances(array $rows): array
    {
        $clean = [];
        $names = [];

        foreach ($rows as $row) {
            $name = trim((string) ($row['name'] ?? ''));
            $amount = $row['amount'] ?? null;

            if ($name === '' && ($amount === null || $amount === '')) {
                continue;
            }

            if ($name === '' || $amount === null || $amount === '') {
                throw ValidationException::withMessages([
                    'allowances' => 'Each allowance needs both a name and an amount.',
                ]);
            }

            $key = strtolower($name);
            if (isset($names[$key])) {
                throw ValidationException::withMessages([
                    'allowances' => "Allowance \"{$name}\" is listed more than once.",
                ]);
            }

            $names[$key] = true;
            $clean[] = [
                'name' => $name,
                'amount' => $amount,
            ];
        }

        return $clean;
    }

    private function syncAllowances(SalaryGrade $grade, array $allowances): void
    {
        $grade->allowances()->delete();

        foreach ($allowances as $allowance) {
            SalaryGradeAllowance::create([
                'salary_grade_id' => $grade->id,
                'name' => $allowance['name'],
                'amount' => $allowance['amount'],
            ]);
        }
    }
}
