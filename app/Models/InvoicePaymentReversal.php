<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\ScopedViaInvoicePayment;

class InvoicePaymentReversal extends Model
{
    use ScopedViaInvoicePayment;

    protected $fillable = [
        'invoice_payment_id',
        'amount',
        'reason',
        'reversed_by',
        'reversed_at',
    ];

    protected $casts = [
        'reversed_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function invoicePayment()
    {
        return $this->belongsTo(InvoicePayment::class);
    }

    public function reversedBy()
    {
        return $this->belongsTo(User::class, 'reversed_by');
    }

    public function cashbookEntries()
    {
        return $this->morphMany(CashbookEntry::class, 'source');
    }
}
