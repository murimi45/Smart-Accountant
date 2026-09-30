<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayslipLine extends Model
{
    use BelongsToSchool;

    public const NSSF_EMPLOYEE = 'nssf_employee';

    public const NSSF_EMPLOYER = 'nssf_employer';

    public const SHIF = 'shif';

    public const HOUSING_EMPLOYEE = 'housing_employee';

    public const HOUSING_EMPLOYER = 'housing_employer';

    public const PAYE = 'paye';

    public const SCHOOL = 'school';

    public const SIDE_EMPLOYEE = 'employee';

    public const SIDE_EMPLOYER = 'employer';

    protected $fillable = [
        'school_id',
        'payslip_id',
        'employee_deduction_id',
        'name',
        'code',
        'side',
        'amount',
        'sort_order',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function payslip(): BelongsTo
    {
        return $this->belongsTo(Payslip::class);
    }

    public function employeeDeduction(): BelongsTo
    {
        return $this->belongsTo(EmployeeDeduction::class);
    }
}