<?php

namespace App\Support;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\ExpenseCategory;
use App\Models\IncomeCategory;
use App\Models\Term;
use Illuminate\Http\Request;

class TenantFilters
{
    public static function schoolId(): int
    {
        return (int) auth()->user()->school_id;
    }

    /**
     * Validate common list-filter foreign keys belong to the current school.
     * Returns the current school id for convenience.
     */
    public static function validate(Request $request, array $keys = ['term_id', 'class_id']): int
    {
        $schoolId = self::schoolId();

        if (in_array('term_id', $keys, true) && $request->filled('term_id')) {
            Term::forSchool($schoolId)->findOrFail($request->term_id);
        }

        if (in_array('class_id', $keys, true) && $request->filled('class_id')) {
            Classes::forSchool($schoolId)->findOrFail($request->class_id);
        }

        if (in_array('academic_year_id', $keys, true) && $request->filled('academic_year_id')) {
            AcademicYear::forSchool($schoolId)->findOrFail($request->academic_year_id);
        }

        if (in_array('category_id', $keys, true) && $request->filled('category_id')) {
            ExpenseCategory::forSchool($schoolId)->findOrFail($request->category_id);
        }

        if (in_array('income_category_id', $keys, true) && $request->filled('income_category_id')) {
            IncomeCategory::forSchool($schoolId)->findOrFail($request->income_category_id);
        }

        return $schoolId;
    }

    /** Scope an enrollment relation filter to the current school. */
    public static function enrollmentClassFilter(int $schoolId, int $classId): \Closure
    {
        return static function ($query) use ($schoolId, $classId) {
            $query->where('school_id', $schoolId)->where('class_id', $classId);
        };
    }
}
