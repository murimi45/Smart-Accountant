<?php

namespace App\Support;

use Illuminate\Validation\Rule;

class TenantRules
{
    public static function schoolId(): int
    {
        return (int) auth()->user()->school_id;
    }

    public static function exists(string $table, string $column = 'id')
    {
        return Rule::exists($table, $column)->where(
            'school_id',
            static::schoolId()
        );
    }

    public static function unique(string $table, string $column, $ignoreId = null)
    {
        $rule = Rule::unique($table, $column)->where('school_id', static::schoolId());

        if ($ignoreId !== null) {
            $rule->ignore($ignoreId);
        }

        return $rule;
    }

    public static function streamInSchool()
    {
        $schoolId = static::schoolId();

        return Rule::exists('streams', 'id')->whereIn('class_id', function ($query) use ($schoolId) {
            $query->select('id')
                ->from('classes')
                ->where('school_id', $schoolId);
        });
    }

    /** @deprecated Use exists('classes') */
    public static function classes(): \Illuminate\Validation\Rules\Exists
    {
        return static::exists('classes');
    }

    public static function terms(): \Illuminate\Validation\Rules\Exists
    {
        return static::exists('terms');
    }

    public static function students(): \Illuminate\Validation\Rules\Exists
    {
        return static::exists('students');
    }

    public static function extraFees(): \Illuminate\Validation\Rules\Exists
    {
        return static::exists('extra_fees');
    }

    public static function enrollments(): \Illuminate\Validation\Rules\Exists
    {
        return static::exists('student_enrollments');
    }

    public static function invoices(): \Illuminate\Validation\Rules\Exists
    {
        return static::exists('invoices');
    }

    public static function expenseCategories(): \Illuminate\Validation\Rules\Exists
    {
        return static::exists('expense_categories');
    }

    public static function incomeCategories(): \Illuminate\Validation\Rules\Exists
    {
        return static::exists('income_categories');
    }

    public static function paymentChannels(): \Illuminate\Validation\Rules\Exists
    {
        return static::exists('payment_channels');
    }

    public static function academicYears(): \Illuminate\Validation\Rules\Exists
    {
        return static::exists('academic_years');
    }

    /** Reject client-supplied tenant key. */
    public static function prohibitedSchoolId(): string
    {
        return 'prohibited';
    }
}
