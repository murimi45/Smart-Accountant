<?php

namespace App\Observers;

use App\Models\StudentEnrollment;
use App\Services\InvoiceService;

class EnrollmentObserver
{
    /*
    |--------------------------------------------------------------------------
    | CREATED
    | Fires when a new enrollment is created — during:
    |   1. Manual student add (StudentController::insertStudents)
    |   2. Promotion run (PromotionService)
    |   3. Correction (EnrollmentController::executeCorrection)
    |--------------------------------------------------------------------------
    */
    public function created(StudentEnrollment $enrollment): void
    {
        // Only generate an invoice for active and repeating enrollments.
        // Inactive = skipped. Cancelled = correction artifact, no invoice.
        if (!in_array($enrollment->status, [
            StudentEnrollment::STATUS_ACTIVE,
            StudentEnrollment::STATUS_REPEATING,
        ])) {
            return;
        }

        app(InvoiceService::class)->createOrUpdateInvoice(
            $enrollment->student,
            $enrollment->term_id,
            $enrollment->id        // pass enrollment_id so invoice anchors correctly
        );
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATED
    | Fires when an enrollment row changes — covers two cases:
    |
    |   Case A: Status changed (active → repeating or vice versa)
    |            → regenerate invoice because class may be different
    |
    |   Case B: Correction — status changed to cancelled
    |            → void the invoice, do NOT create a new one here.
    |              The new enrollment's created() event handles the new invoice.
    |--------------------------------------------------------------------------
    */
    public function updated(StudentEnrollment $enrollment): void
    {
        // Case B — cancelled or marked inactive: void invoice, no new one here.
        if ($enrollment->isDirty('status') &&
            in_array($enrollment->status, [
                StudentEnrollment::STATUS_CANCELLED,
                StudentEnrollment::STATUS_INACTIVE,
            ], true))
        {
            $this->voidInvoice($enrollment);
            return;
        }

        // Case A — class or status changed pre-promotion (admin adjusting)
        // Regenerate invoice to reflect the new class fee or status
        if ($enrollment->isDirty(['status', 'class_id', 'stream_id'])) {

            if (in_array($enrollment->status, [
                StudentEnrollment::STATUS_INACTIVE,
                StudentEnrollment::STATUS_CANCELLED,
            ], true)) {
                return;
            }

            app(InvoiceService::class)->createOrUpdateInvoice(
                $enrollment->student,
                $enrollment->term_id,
                $enrollment->id
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | PRIVATE HELPERS
    |--------------------------------------------------------------------------
    */

    private function voidInvoice(StudentEnrollment $enrollment): void
    {
        $invoice = $enrollment->invoice;

        if (!$invoice) return;

        $reason = match ($enrollment->status) {
            StudentEnrollment::STATUS_INACTIVE => 'Voided — enrollment marked inactive.',
            StudentEnrollment::STATUS_CANCELLED => $enrollment->correction_reason
                ? 'Voided — enrollment corrected. Reason: ' . $enrollment->correction_reason
                : 'Voided — enrollment cancelled.',
            default => 'Voided — enrollment status changed.',
        };

        $invoice->update([  

            'status' => 'voided',
            'notes'  => $reason,
        ]);
    }
}