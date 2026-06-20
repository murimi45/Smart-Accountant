<?php

namespace App\Observers;

use App\Models\ExtraFee;
use App\Support\FinanceAudit;

class ExtraFeeObserver
{
    public function created(ExtraFee $extraFee): void
    {
        $extraFee->loadMissing('term');

        FinanceAudit::log(
            'extra_fee.created',
            sprintf(
                'Extra fee "%s" created for %s — KES %s',
                $extraFee->name,
                $this->termLabel($extraFee),
                number_format((float) $extraFee->amount, 2)
            ),
            $extraFee,
            [
                'name'   => $extraFee->name,
                'amount' => (float) $extraFee->amount,
                'term_id'=> $extraFee->term_id,
            ],
            (int) $extraFee->school_id
        );
    }

    public function updated(ExtraFee $extraFee): void
    {
        if (! $extraFee->wasChanged(['name', 'amount', 'status', 'description'])) {
            return;
        }

        $extraFee->loadMissing('term');

        FinanceAudit::log(
            'extra_fee.updated',
            sprintf(
                'Extra fee "%s" updated (%s)',
                $extraFee->name,
                $this->termLabel($extraFee)
            ),
            $extraFee,
            [
                'old' => $extraFee->getOriginal(),
                'new' => $extraFee->only(['name', 'amount', 'status', 'description']),
            ],
            (int) $extraFee->school_id
        );
    }

    public function deleted(ExtraFee $extraFee): void
    {
        $extraFee->loadMissing('term');

        FinanceAudit::log(
            'extra_fee.deleted',
            sprintf(
                'Extra fee "%s" deleted (%s)',
                $extraFee->name,
                $this->termLabel($extraFee)
            ),
            $extraFee,
            ['name' => $extraFee->name, 'amount' => (float) $extraFee->amount],
            (int) $extraFee->school_id
        );
    }

    private function termLabel(ExtraFee $extraFee): string
    {
        $term = $extraFee->term;

        return $term ? trim($term->name.' '.($term->year ?? '')) : 'term #'.$extraFee->term_id;
    }
}
