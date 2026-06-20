<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class LedgerEntry extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'account_id',
        'cashbook_entry_id',
        'debit',
        'credit',
        'description',
        'transaction_date',
    ];

    protected $casts = [
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
        'transaction_date' => 'date',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function cashbookEntry()
    {
        return $this->belongsTo(CashbookEntry::class);
    }
}
