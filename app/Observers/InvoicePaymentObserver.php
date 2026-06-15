<?php

namespace App\Observers;
use App\Models\InvoicePayment;   // ✅ Correct
use App\Models\CashbookEntry;    // ✅ Correct


class InvoicePaymentObserver
{
    
    public function created(InvoicePayment $payment)
    {
        $invoice = $payment->invoice;
        $student = $invoice->student ?? null;

        $schoolId = $student?->school_id ?? $invoice->school_id;

        CashbookEntry::createForSchool($schoolId, [
            'transaction_type' => 'inflow',
            'entry_type'       => 'original',
            'source_id'        => $payment->id,
            'source_type'      => InvoicePayment::class,
            'amount'           => $payment->amount,
            'payment_method'   => $payment->method,
            'transaction_date' => $payment->payment_date,
            'description'      => "School fees payment for student "
                                    . ($student ? $student->full_name : "ID {$invoice->student_id}"),
        ]);
}
}