<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\ScopedViaInvoice;

class InvoicePayment extends Model
{
    use ScopedViaInvoice;

    protected $fillable = [
        'invoice_id',
        'amount',
        'method',
        'payment_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function reversals()
    {
        return $this->hasMany(InvoicePaymentReversal::class);
    }

    public function cashbookEntries()
    {
        return $this->morphMany(CashbookEntry::class, 'source');
    }

    public function reversedAmount(): float
    {
        return (float) $this->reversals()->sum('amount');
    }

    public function reversibleAmount(): float
    {
        return max(0, (float) $this->amount - $this->reversedAmount());
    }

    public function isFullyReversed(): bool
    {
        return $this->reversibleAmount() <= 0;
    }
}
