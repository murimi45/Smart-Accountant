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
        $enrollments = StudentEnrollment::where('class_id', $classFee->class_id)
            ->where('term_id', $classFee->term_id)
            ->whereIn('status', [
                StudentEnrollment::STATUS_ACTIVE,
                StudentEnrollment::STATUS_REPEATING,
            ])
            ->with('student')
            ->get();

        foreach ($enrollments as $enrollment) {
            if (!$enrollment->student) {
                continue;
            }

            app(InvoiceService::class)->createOrUpdateInvoice(
                $enrollment->student,
                $classFee->term_id,
                $enrollment->id
            );
        }
    }

    public function deleted(ClassFee $classFee): void
    {
        //
    }

    public function restored(ClassFee $classFee): void
    {
        //
    }

    public function forceDeleted(ClassFee $classFee): void
    {
        //
    }
}
