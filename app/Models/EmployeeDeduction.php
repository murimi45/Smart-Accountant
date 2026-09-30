<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class EmployeeDeduction extends Model
{
    use BelongsToSchool;

    public const KIND_RECURRING = 'recurring';

    public const KIND_BALANCE = 'balance';

    protected $fillable = [
        'school_id',
        'employee_id',
        'name',
        'kind',
        'amount',
        'balance_remaining',
        'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_remaining' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}