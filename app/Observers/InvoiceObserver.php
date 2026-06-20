<?php

namespace App\Observers;

use App\Models\Invoice;
use App\Support\FinanceAudit;

class InvoiceObserver
{
    public function updated(Invoice $invoice): void
    {
        if (! $invoice->wasChanged('status') || $invoice->status !== Invoice::STATUS_VOIDED) {
            return;
        }

        $invoice->loadMissing('student', 'term');

        $studentLabel = $invoice->student
            ? "{$invoice->student->full_name} ({$invoice->student->admission})"
            : 'student #'.$invoice->student_id;

        FinanceAudit::log(
            'invoice.voided',
            sprintf(
                'Invoice #%d voided for %s — %s',
                $invoice->id,
                $studentLabel,
                trim((string) ($invoice->notes ?? '')) ?: 'No reason recorded'
            ),
            $invoice,
            [
                'invoice_id' => $invoice->id,
                'student_id' => $invoice->student_id,
                'admission'  => $invoice->student?->admission,
                'term_id'    => $invoice->term_id,
                'reason'     => $invoice->notes,
            ],
            (int) $invoice->school_id
        );
    }
}
