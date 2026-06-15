<?php

namespace App\Observers;

use App\Models\StudentExtraFee;
use App\Services\InvoiceService;

class ExtraFeeAssignmentObserver
{
    private function shouldSkipBatchInvoice(): bool
    {
        return (app()->bound('batchAssigningExtraFees') && app('batchAssigningExtraFees') === true)
            || (app()->bound('extraFeeBatch') && app('extraFeeBatch') === true);
    }

    public function created(StudentExtraFee $extraFee): void
    {
        if ($this->shouldSkipBatchInvoice()) {
            return;
        }

        $this->refreshInvoiceForAssignment($extraFee);
    }

    public function deleted(StudentExtraFee $extraFee): void
    {
        $this->refreshInvoiceForAssignment($extraFee);
    }

    public function updated(StudentExtraFee $extraFee): void
    {
        $this->refreshInvoiceForAssignment($extraFee);
    }

    private function refreshInvoiceForAssignment(StudentExtraFee $extraFee): void
    {
        $extraFee->loadMissing('extraFee', 'student');

        $student  = $extraFee->student;
        $termId   = $extraFee->extraFee?->term_id;
        $schoolId = (int) ($extraFee->school_id ?: $student?->school_id);

        if (! $student || ! $termId || ! $schoolId) {
            return;
        }

        if ((int) $student->school_id !== $schoolId) {
            return;
        }

        app(InvoiceService::class)->createOrUpdateInvoice($schoolId, $student, $termId);
    }
}
