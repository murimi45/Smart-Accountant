<?php

namespace App\Observers;

use App\Models\ClassFee;
use App\Models\StudentEnrollment;
use App\Services\InvoiceService;
use App\Support\FinanceAudit;

class ClassFeeObserver
{
    public function updated(ClassFee $classFee): void
    {
        if ($classFee->wasChanged(['amount', 'description', 'status'])) {
            $this->auditChange('class_fee.updated', $classFee, 'updated');
        }

        $this->syncInvoicesForClassFee($classFee);
    }

    public function created(ClassFee $classFee): void
    {
        $this->auditChange('class_fee.created', $classFee, 'created');
        $this->syncInvoicesForClassFee($classFee);
    }

    public function deleted(ClassFee $classFee): void
    {
        $this->auditChange('class_fee.deleted', $classFee, 'deleted');
    }

    private function auditChange(string $event, ClassFee $classFee, string $verb): void
    {
        $classFee->loadMissing(['class', 'term']);

        $className = $classFee->class?->name ?? 'class #'.$classFee->class_id;
        $termLabel = $classFee->term
            ? trim($classFee->term->name.' '.($classFee->term->year ?? ''))
            : 'term #'.$classFee->term_id;

        $amountLabel = number_format((float) $classFee->amount, 2);

        if ($verb === 'updated' && $classFee->wasChanged('amount')) {
            $oldAmount = number_format((float) $classFee->getOriginal('amount'), 2);
            $detail = "KES {$oldAmount} → KES {$amountLabel}";
        } else {
            $detail = "KES {$amountLabel}";
        }

        FinanceAudit::log(
            $event,
            sprintf('Class fee %s for %s — %s: %s', $verb, $className, $termLabel, $detail),
            $classFee,
            [
                'class_id' => $classFee->class_id,
                'term_id'  => $classFee->term_id,
                'amount'   => (float) $classFee->amount,
                'old'      => $classFee->getOriginal(),
            ],
            (int) $classFee->school_id
        );
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
