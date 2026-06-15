<?php

namespace App\Observers;

use App\Models\Student;
use App\Services\InvoiceService;

class StudentObserver
{
    public function created(Student $student): void
    {
        if (! $student->school_id || ! $student->class_id || ! $student->term_id) {
            return;
        }

        app(InvoiceService::class)->createOrUpdateInvoice(
            (int) $student->school_id,
            $student,
            $student->term_id
        );
    }

    public function updated(Student $student): void
    {
        if (! $student->school_id || ! $student->isDirty('class_id') || ! $student->term_id) {
            return;
        }

        app(InvoiceService::class)->createOrUpdateInvoice(
            (int) $student->school_id,
            $student,
            $student->term_id
        );
    }
}
