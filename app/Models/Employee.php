<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use BelongsToSchool;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_LEFT = 'left';

    public const PAY_BANK = 'bank';

    public const PAY_MPESA = 'mpesa';

    public const PAY_CASH = 'cash';

    protected $fillable = [
        'school_id',
        'staff_number',
        'full_name',
        'phone',
        'status',
        'start_date',
        'kra_pin',
        'nssf_number',
        'shif_number',
        'payment_method',
        'bank_name',
        'account_number',
        'salary_grade_id',
        'department_id',
        'user_id',
    ];

    protected $casts = [
        'start_date' => 'date',
    ];

    public function grade(): BelongsTo
    {
        return $this->belongsTo(SalaryGrade::class, 'salary_grade_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function deductions(): HasMany
{
    return $this->hasMany(EmployeeDeduction::class);
}
public function payslips(): HasMany
{
    return $this->hasMany(Payslip::class);
}
}