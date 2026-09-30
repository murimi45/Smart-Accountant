<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollRun extends Model
{
    use BelongsToSchool;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_LOCKED = 'locked';

    protected $fillable = [
        'school_id',
        'period',
        'status',
        'locked_at',
        'locked_by',
    ];

    protected $casts = [
        'period' => 'date',
        'locked_at' => 'datetime',
    ];

    public function payslips(): HasMany
    {
        return $this->hasMany(Payslip::class);
    }

    public function locker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function isLocked(): bool
    {
        return $this->status === self::STATUS_LOCKED;
    }
}