<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToSchool;

class BankReconciliationMatch extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'bank_deposit_id',
        'cashbook_entry_id',
        'amount',
        'matched_by',
        'matched_at',
    ];

    protected $casts = [
        'amount'     => 'decimal:2',
        'matched_at' => 'datetime',
    ];

    public function bankDeposit()
    {
        return $this->belongsTo(BankDeposit::class);
    }

    public function cashbookEntry()
    {
        return $this->belongsTo(CashbookEntry::class);
    }

    public function matchedBy()
    {
        return $this->belongsTo(User::class, 'matched_by');
    }
}
