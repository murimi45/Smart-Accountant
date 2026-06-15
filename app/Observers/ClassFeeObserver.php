<?php

namespace App\Observers;

use App\Models\ClassFee;
use App\Models\StudentEnrollment;
use App\Services\InvoiceService;

class ClassFeeObserver
{
    public function updated(ClassFee $classFee): void
    {
        $this->syncInvoicesForClassFee($classFee);
    }

    public function created(ClassFee $classFee): void
    {
        $this->syncInvoicesForClassFee($classFee);
    }

    private function syncInvoicesForClassFee(ClassFee $classFee): void
    {
        if (! $classFee->school_id) {
            return;
        }

        $enrollments = StudentEnrollment::withoutGlobalScopes()
            ->where('school_id', $classFee->school_id)
            ->where('class_id', $classFee->class_id)
            ->where('term_id', $classFee->term_id)
            ->whereIn('status', [
                StudentEnrollment::STATUS_ACTIVE,
                StudentEnrollment::STATUS_REPEATING,
            ])
            ->with('student')
            ->get();

        foreach ($enrollments as $enrollment) {
            if (! $enrollment->student) {
                continue;
            }

            app(InvoiceService::class)->createOrUpdateInvoice(
                (int) $classFee->school_id,
                $enrollment->student,
                $classFee->term_id,
                $enrollment->id
            );
        }
    }
}
