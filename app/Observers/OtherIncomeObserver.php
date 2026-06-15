<?php

namespace App\Observers;

use App\Models\OtherIncome;
use App\Models\CashbookEntry;

class OtherIncomeObserver
{
    public function created(OtherIncome $income)
    {
        CashbookEntry::createForSchool($income->school_id, [
            'transaction_type' => 'inflow',
            'entry_type' => 'original',
            'source_id' => $income->id,
            'source_type' => OtherIncome::class,
            'amount' => $income->amount,
            'payment_method' => $income->payment_method,
            'transaction_date' => $income->income_date,
            'description' => $income->description,
        ]);
    }

    public function updated(OtherIncome $income)
    {
        $entry = CashbookEntry::where('source_type', OtherIncome::class)
            ->where('source_id', $income->id)
            ->first();

        if ($entry) {
            $entry->update([
                'amount' => $income->amount,
                'payment_method' => $income->payment_method,
                'transaction_date' => $income->income_date,
                'description' => $income->description,
            ]);
        }
    }

    public function deleted(OtherIncome $income)
    {
        $entry = CashbookEntry::where('source_type', OtherIncome::class)
            ->where('source_id', $income->id)
            ->first();

        if ($entry) {
            CashbookEntry::createForSchool($entry->school_id, [
                'transaction_type' => 'outflow',
                'entry_type' => 'reversal',
                'source_id' => $income->id,
                'source_type' => OtherIncome::class,
                'related_entry_id' => $entry->id,
                'amount' => $entry->amount,
                'payment_method' => $entry->payment_method,
                'transaction_date' => now(),
                'description' => 'Reversal: ' . $entry->description,
            ]);
        }
    }

    public function restored(OtherIncome $income)
    {
        $this->created($income);
    }
}
