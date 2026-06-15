<?php

namespace App\Observers;

use App\Models\StudentEnrollment;
use App\Services\InvoiceService;

class EnrollmentObserver
{
    public function created(StudentEnrollment $enrollment): void
    {
        if (! in_array($enrollment->status, [
            StudentEnrollment::STATUS_ACTIVE,
            StudentEnrollment::STATUS_REPEATING,
        ])) {
            return;
        }

        $enrollment->loadMissing('student');

        if (! $enrollment->student || ! $enrollment->school_id) {
            return;
        }

        app(InvoiceService::class)->createOrUpdateInvoice(
            (int) $enrollment->school_id,
            $enrollment->student,
            $enrollment->term_id,
            $enrollment->id
        );
    }

    public function updated(StudentEnrollment $enrollment): void
    {
        if ($enrollment->isDirty('status') &&
            in_array($enrollment->status, [
                StudentEnrollment::STATUS_CANCELLED,
                StudentEnrollment::STATUS_INACTIVE,
            ], true))
        {
            $this->voidInvoice($enrollment);
            return;
        }

        if ($enrollment->isDirty(['status', 'class_id', 'stream_id'])) {
            if (in_array($enrollment->status, [
                StudentEnrollment::STATUS_INACTIVE,
                StudentEnrollment::STATUS_CANCELLED,
            ], true)) {
                return;
            }

            $enrollment->loadMissing('student');

            if (! $enrollment->student || ! $enrollment->school_id) {
                return;
            }

            app(InvoiceService::class)->createOrUpdateInvoice(
                (int) $enrollment->school_id,
                $enrollment->student,
                $enrollment->term_id,
                $enrollment->id
            );
        }
    }

    private function voidInvoice(StudentEnrollment $enrollment): void
    {
        $invoice = $enrollment->invoice;

        if (! $invoice) {
            return;
        }

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
