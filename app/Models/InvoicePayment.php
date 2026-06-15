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




    public function invoice()
{
    return $this->belongsTo(Invoice::class);
}
}
