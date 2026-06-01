<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\ClassFee;
use App\Models\StudentExtraFee;
use App\Models\StudentEnrollment;
use App\Models\InvoicePayment;
use App\Models\Term;
use Illuminate\Support\Facades\DB;
use App\Jobs\SendPaymentNotification;
use InvalidArgumentException;

class InvoiceService
{
    /*
    |--------------------------------------------------------------------------
    | CREATE OR UPDATE INVOICE
    |
    | $student     — Student model
    | $termId      — int
    | $enrollmentId — int|null
    |       Pass this whenever you have it (observer always passes it).
    |       If null, the method resolves it from the active enrollment.
    |       This keeps backward compatibility with any existing direct calls.
    |--------------------------------------------------------------------------
    */
    public function createOrUpdateInvoice($student, $termId, $enrollmentId = null)
    {
        // 1. Resolve the enrollment_id if not passed directly
        if (!$enrollmentId) {
            $enrollment = StudentEnrollment::where('student_id', $student->id)
                ->where('term_id', $termId)
                ->whereIn('status', [
                    StudentEnrollment::STATUS_ACTIVE,
                    StudentEnrollment::STATUS_REPEATING,
                ])
                ->latest()
                ->first();

            if (!$enrollment) {
                // No active enrollment for this student in this term — skip
                return null;
            }

            $enrollmentId = $enrollment->id;
        } else {
            $enrollment = StudentEnrollment::find($enrollmentId);

            if (!$enrollment) return null;
        }

        // 2. Find or create the invoice anchored to enrollment_id
        //    student_id and term_id are kept for reporting/filtering convenience
        $invoice = Invoice::firstOrCreate(
            [
                'enrollment_id' => $enrollmentId,   // primary anchor — new
            ],
            [
                'student_id'   => $student->id,     // kept for easy querying
                'term_id'      => $termId,           // kept for easy querying
                'invoice_date' => now(),
            ]
        );

        // 3. Clear old line items and recalculate fresh
        $invoice->items()->delete();

        $total = 0;

        // 3a. Class fee — look up by class_id from the enrollment (not student)
        //     This is the key change: enrollment.class_id is the source of truth
        $classFee = ClassFee::where('class_id', $enrollment->class_id)
            ->where('term_id', $termId)
            ->first();

        if ($classFee) {
            $invoice->items()->create([
                'term_id'     => $termId,
                'description' => 'Class Fee: ' . ($enrollment->schoolClass->name ?? ''),
                'amount'      => $classFee->amount,
            ]);
            $invoice->base_fee = $classFee->amount;
            $total += $classFee->amount;
        }

        // 3b. Extra fees scoped to this term
        $extraFees = StudentExtraFee::where('student_id', $student->id)
            ->whereHas('extraFee', fn($q) => $q->where('term_id', $termId))
            ->get();

        foreach ($extraFees as $extra) {
            $invoice->items()->create([
                'term_id'     => $termId,
                'description' => 'Extra Fee: ' . ($extra->extraFee->name ?? ''),
                'amount'      => $extra->amount,
            ]);
            $total += $extra->amount;
        }

        // 3c. Balance / credit forward from previous enrollment's invoice
        //     Look up via previous enrollment — not student_id + term_id - 1
        $previousInvoice = $this->getPreviousInvoice($student->id, $enrollmentId);

        if ($previousInvoice) {
            $carried = $previousInvoice->total_amount - $previousInvoice->amount_paid;

            if ($carried > 0) {
                $invoice->items()->create([
                    'term_id'     => $termId,
                    'description' => 'Balance B/F',
                    'amount'      => $carried,
                ]);
                $invoice->balance_forward = $carried;
                $total += $carried;
                $this->transferPreviousInvoice($previousInvoice, $invoice, $carried);

            } elseif ($carried < 0) {
                $invoice->items()->create([
                    'term_id'     => $termId,
                    'description' => 'Credit B/F',
                    'amount'      => $carried,
                ]);
                $invoice->credit_forward = abs($carried);
                $total += $carried;
                $this->transferPreviousInvoice($previousInvoice, $invoice, $carried);
            }
        }

        // 4. Finalise totals and save
        $invoice->total_amount = $total;
        $invoice->balance      = $total - $invoice->amount_paid;
        $invoice->status       = $this->calculateStatus($invoice);
        $invoice->save();

        return $invoice->fresh();
    }

    /*
    |--------------------------------------------------------------------------
    | PAYMENT MADE — unchanged from original
    |--------------------------------------------------------------------------
    */
    public function paymentMade(Invoice $invoice, float $amount, string $method): Invoice
    {
        $this->assertPayableInvoice($invoice);

        return DB::transaction(function () use ($invoice, $amount, $method) {

            InvoicePayment::create([
                'invoice_id'   => $invoice->id,
                'amount'       => $amount,
                'method'       => $method,
                'payment_date' => now(),
            ]);

            $invoice->increment('amount_paid', $amount);
            $invoice->update([
                'balance' => $invoice->total_amount - $invoice->amount_paid,
                'status'  => $this->calculateStatus($invoice),
            ]);

            SendPaymentNotification::dispatch($invoice, $amount);

            \Log::info('Invoice paid', [
                'invoice_id' => $invoice->id,
                'amount'     => $amount,
                'method'     => $method,
            ]);

            return $invoice->fresh();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | PRIVATE HELPERS
    |--------------------------------------------------------------------------
    */

    /**
     * Find the previous term's invoice by walking back through enrollment history.
     * Uses promoted_from_enrollment_id chain — not term_id arithmetic.
     */
    private function getPreviousInvoice($studentId, int $currentEnrollmentId): ?Invoice
    {
        $currentEnrollment = StudentEnrollment::find($currentEnrollmentId);

        if (!$currentEnrollment || !$currentEnrollment->promoted_from_enrollment_id) {
            return null;
        }

        return Invoice::where('enrollment_id', $currentEnrollment->promoted_from_enrollment_id)
            ->where('status', '!=', Invoice::STATUS_VOIDED)
            ->first();
    }

    /**
     * Close the prior-term invoice so balances are not double-counted.
     * Historical total_amount / amount_paid are preserved for statements.
     */
    private function transferPreviousInvoice(
        Invoice $previousInvoice,
        Invoice $newInvoice,
        float $carried
    ): void {
        if ($previousInvoice->status === Invoice::STATUS_TRANSFERRED) {
            return;
        }

        $label = $carried > 0 ? 'Balance' : 'Credit';
        $previousInvoice->update([
            'status'  => Invoice::STATUS_TRANSFERRED,
            'balance' => 0,
            'notes'   => trim(($previousInvoice->notes ?? '') . "\n{$label} carried forward to invoice #{$newInvoice->id} (term enrollment #{$newInvoice->enrollment_id})."),
        ]);
    }

    public function assertPayableInvoice(Invoice $invoice): void
    {
        if ($invoice->status === Invoice::STATUS_VOIDED) {
            throw new InvalidArgumentException('Cannot record payment on a voided invoice.');
        }

        if ($invoice->status === Invoice::STATUS_TRANSFERRED) {
            throw new InvalidArgumentException(
                'This invoice was closed. The balance was moved to the current term invoice — record payment there.'
            );
        }

        $schoolId = $invoice->school_id;
        if (auth()->check() && auth()->user()->school_id) {
            $schoolId = auth()->user()->school_id;
        }

        $currentTerm = Term::current1($schoolId);

        if (! $currentTerm) {
            throw new InvalidArgumentException('No active term is set for this school.');
        }

        if ((int) $invoice->term_id !== (int) $currentTerm->id) {
            throw new InvalidArgumentException(
                'Payments can only be recorded on the current term invoice.'
            );
        }
    }

    public static function currentTermForSchool(?int $schoolId): ?Term
    {
        if (! $schoolId) {
            return null;
        }

        return Term::current1($schoolId);
    }

    private function calculateStatus($invoice): string
    {
        if ($invoice->amount_paid >= $invoice->total_amount) {
            return 'paid';
        } elseif ($invoice->amount_paid > 0) {
            return 'partially_paid';
        }
        return 'unpaid';
    }
}