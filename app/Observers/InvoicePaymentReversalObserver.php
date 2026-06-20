<?php

namespace App\Observers;

use App\Models\CashbookEntry;
use App\Models\InvoicePayment;
use App\Models\InvoicePaymentReversal;
use App\Models\User;
use App\Support\FinanceAudit;
use Illuminate\Support\Facades\DB;

class InvoicePaymentReversalObserver
{
    public function created(InvoicePaymentReversal $reversal): void
    {
        DB::transaction(function () use ($reversal) {
            $payment = $reversal->invoicePayment()->withoutGlobalScopes()->first();
            if (! $payment) {
                return;
            }

            $invoice = $payment->invoice()->withoutGlobalScopes()->first();
            $student = $invoice?->student;
            $schoolId = (int) ($student?->school_id ?? $invoice?->school_id);

            if (! $schoolId) {
                return;
            }

            $original = CashbookEntry::where('school_id', $schoolId)
                ->where('source_type', InvoicePayment::class)
                ->where('source_id', $payment->id)
                ->where('entry_type', 'original')
                ->first();

            CashbookEntry::createForSchool($schoolId, [
                'transaction_type' => 'outflow',
                'entry_type' => 'reversal',
                'source_id' => $reversal->id,
                'source_type' => InvoicePaymentReversal::class,
                'amount' => $reversal->amount,
                'payment_method' => $payment->method,
                'transaction_date' => now()->toDateString(),
                'description' => sprintf(
                    'Reversal of fee payment #%d — Reason: %s',
                    $payment->id,
                    $reversal->reason
                ),
                'related_entry_id' => $original?->id,
            ]);

            $studentLabel = $student
                ? "{$student->full_name} ({$student->admission})"
                : 'student #'.($invoice?->student_id ?? '?');

            $reverser = $reversal->reversed_by
                ? User::query()->find($reversal->reversed_by)
                : null;

            FinanceAudit::log(
                'payment.reversed',
                sprintf(
                    'Payment reversal of KES %s for %s — %s (invoice #%d)',
                    number_format((float) $reversal->amount, 2),
                    $studentLabel,
                    $reversal->reason,
                    $invoice?->id
                ),
                $reversal,
                [
                    'reversal_id' => $reversal->id,
                    'payment_id'  => $payment->id,
                    'invoice_id'  => $invoice?->id,
                    'amount'      => (float) $reversal->amount,
                    'reason'      => $reversal->reason,
                ],
                $schoolId,
                $reverser
            );
        });
    }
}
