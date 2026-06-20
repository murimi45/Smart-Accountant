<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\ScopedViaInvoice;

class InvoiceItem extends Model
{
     use HasFactory, ScopedViaInvoice;

    protected $fillable = [
        'invoice_id',
        'invoice_waiver_id',
        'is_opening_balance',
        'description',
        'amount',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function waiver()
    {
        return $this->belongsTo(InvoiceWaiver::class, 'invoice_waiver_id');
    }

    public function isWaiverLine(): bool
    {
        return $this->invoice_waiver_id !== null;
    }
}
