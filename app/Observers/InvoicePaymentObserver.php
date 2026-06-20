<?php

namespace App\Observers;
use App\Models\InvoicePayment;
use App\Models\CashbookEntry;
use App\Support\FinanceAudit;


class InvoicePaymentObserver
{
    
    public function created(InvoicePayment $payment)
    {
        $payment->loadMissing('invoice.student');
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

        $studentLabel = $student
            ? "{$student->full_name} ({$student->admission})"
            : 'student #'.$invoice->student_id;

        FinanceAudit::log(
            'payment.recorded',
            sprintf(
                'Payment of KES %s recorded for %s via %s (invoice #%d)',
                number_format((float) $payment->amount, 2),
                $studentLabel,
                $payment->method,
                $invoice->id
            ),
            $payment,
            [
                'payment_id' => $payment->id,
                'invoice_id' => $invoice->id,
                'amount'     => (float) $payment->amount,
                'method'     => $payment->method,
                'admission'  => $student?->admission,
            ],
            (int) $schoolId
        );
    }
}