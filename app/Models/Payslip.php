<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payslip extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'school_id',
        'payroll_run_id',
        'employee_id',
        'staff_number',
        'full_name',
        'included',
        'adjustment',
        'gross',
        'nssf_employee',
        'nssf_employer',
        'shif',
        'housing_employee',
        'housing_employer',
        'taxable_pay',
        'paye',
        'school_deductions_total',
        'net_pay',
        'employer_cost',
    ];

    protected $casts = [
        'included' => 'boolean',
        'adjustment' => 'decimal:2',
        'gross' => 'decimal:2',
        'nssf_employee' => 'decimal:2',
        'nssf_employer' => 'decimal:2',
        'shif' => 'decimal:2',
        'housing_employee' => 'decimal:2',
        'housing_employer' => 'decimal:2',
        'taxable_pay' => 'decimal:2',
        'paye' => 'decimal:2',
        'school_deductions_total' => 'decimal:2',
        'net_pay' => 'decimal:2',
        'employer_cost' => 'decimal:2',
    ];

    public function run(): BelongsTo
    {
        return $this->belongsTo(PayrollRun::class, 'payroll_run_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(PayslipLine::class)->orderBy('sort_order');
    }
}