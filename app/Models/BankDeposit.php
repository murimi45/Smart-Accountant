<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToSchool;

class BankDeposit extends Model
{
    use BelongsToSchool;

    public const STATUS_UNMATCHED = 'unmatched';
    public const STATUS_PARTIAL = 'partial';
    public const STATUS_RECONCILED = 'reconciled';

    protected $fillable = [
        'deposit_date',
        'amount',
        'reference',
        'description',
        'status',
        'recorded_by',
    ];

    protected $casts = [
        'deposit_date' => 'date',
        'amount'       => 'decimal:2',
    ];

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function matches()
    {
        return $this->hasMany(BankReconciliationMatch::class);
    }

    public function matchedAmount(): float
    {
        return (float) $this->matches()->sum('amount');
    }

    public function remainingAmount(): float
    {
        return max(0, (float) $this->amount - $this->matchedAmount());
    }

    public function refreshStatus(): void
    {
        $matched = $this->matchedAmount();
        $total = (float) $this->amount;

        if ($matched <= 0) {
            $status = self::STATUS_UNMATCHED;
        } elseif ($matched + 0.001 >= $total) {
            $status = self::STATUS_RECONCILED;
        } else {
            $status = self::STATUS_PARTIAL;
        }

        $this->update(['status' => $status]);
    }
}
