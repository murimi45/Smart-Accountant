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
        'description',
        'amount',
    ];

    public function invoice()
{
    return $this->belongsTo(Invoice::class);
}
}
