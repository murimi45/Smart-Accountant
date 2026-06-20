<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToSchool;

class CashbookEntry extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'transaction_type', 'entry_type', 'amount',
        'payment_method', 'transaction_date', 'description',
        'source_id', 'source_type', 'related_entry_id',
    ];

    public static function createForSchool(int $schoolId, array $attributes): self
    {
        $entry = new static($attributes);
        $entry->school_id = $schoolId;
        $entry->save();

        return $entry;
    }

    public function source()
    {
        return $this->morphTo();
    }

    public function relatedEntry()
    {
        return $this->belongsTo(CashbookEntry::class, 'related_entry_id');
    }

    public function reconciliationMatch()
    {
        return $this->hasOne(BankReconciliationMatch::class);
    }

    public function isReconciled(): bool
    {
        return $this->reconciliationMatch()->exists();
    }

    public function ledgerEntries()
    {
        return $this->hasMany(LedgerEntry::class);
    }
}
