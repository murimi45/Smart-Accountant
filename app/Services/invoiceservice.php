<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\ClassFee;
use App\Models\StudentExtraFee;
use App\Models\InvoicePayment;
use Auth;
use Illuminate\Support\Facades\DB;
use App\Jobs\SendPaymentNotification;
use App\Notifications\IncomeRecordedNotification;

class InvoiceService
{
    /**
     * Ensure a student has an invoice for the given term.
     */
    public function createOrUpdateInvoice($student, $termId)
{
    $invoice = Invoice::firstOrCreate(
        ['student_id' => $student->id, 'term_id' => $termId],
        ['invoice_date' => now()]
    );

    // Clear old invoice items
    $invoice->items()->delete();

    $total = 0;

    // 1. Add Class Fee (only for this term)
    $classFee = ClassFee::where('class_id', $student->class_id)
        ->where('term_id', $termId)
        ->first();

    if ($classFee) {
        $invoice->items()->create([
            'term_id'     => $termId,
            'description' => "Class Fee: {$student->class->name}",
            'amount'      => $classFee->amount,
        ]);
        $invoice->base_fee = $classFee->amount;   // ✅ new field
        $total += $classFee->amount;
    }

    // 2. Add Extra Fees (scoped to this term)
    $extraFees = StudentExtraFee::where('student_id', $student->id)
        ->whereHas('extraFee', fn($q) => $q->where('term_id', $termId))
        ->get();

    foreach ($extraFees as $extra) {
        $amount = $extra->amount;

        $invoice->items()->create([
            'term_id'     => $termId,
            'description' => "Extra Fee: {$extra->extraFee->name}",
            'amount'      => $amount,
        ]);
        $total += $amount;
    }

    // 3. Add Previous Balance (carry over automatically)
    $previousInvoice = Invoice::where('student_id', $student->id)
        ->where('term_id', '<', $termId)
        ->orderByDesc('term_id')
        ->first();

    if ($previousInvoice) {
        $balance = $previousInvoice->total_amount - $previousInvoice->amount_paid;

        if ($balance > 0) {
            $invoice->items()->create([
                'term_id'     => $termId,
                'description' => "Balance B/F",
                'amount'      => $balance,
            ]);
            $invoice->balance_forward = $balance; // ✅ new field
            $total += $balance;
        } elseif ($balance < 0) {
            $invoice->items()->create([
                'term_id'     => $termId,
                'description' => "Credit B/F",
                'amount'      => $balance,
            ]);
            $invoice->credit_forward = abs($balance); // ✅ new field
            $total += $balance;
        }
    }

    // Update totals
    $invoice->total_amount = $total;
    $invoice->balance = $total - $invoice->amount_paid;
    $invoice->status = $this->calculateStatus($invoice);
    $invoice->save();

    return $invoice->fresh(); // force reload to avoid stale cache
}


     public function paymentMade(Invoice $invoice, float $amount, string $method): Invoice
{
    return DB::transaction(function () use ($invoice, $amount, $method) {

        InvoicePayment::create([
            'invoice_id' => $invoice->id,
            'amount' => $amount,
            'method' => $method,
            'payment_date' => now(),
        ]);

        $invoice->increment('amount_paid', $amount);
        $invoice->update([
            'balance' => $invoice->total_amount - $invoice->amount_paid,
            'status' => $this->calculateStatus($invoice),
        ]);

        // Replace the old synchronous notify call
        SendPaymentNotification::dispatch($invoice, $amount);

        \Log::info('Invoice paid', [
            'invoice_id' => $invoice->id,
            'amount' => $amount,
            'method' => $method,
        ]);

        return $invoice->fresh();
    });
}


    /**
     * Recalculate invoice status.
     */
    private function calculateStatus($invoice)
    {
        if ($invoice->amount_paid >= $invoice->total_amount) {
            return 'paid';
        } elseif ($invoice->amount_paid > 0) {
            return 'partially_paid';
        }
        return 'unpaid';
    }
}
