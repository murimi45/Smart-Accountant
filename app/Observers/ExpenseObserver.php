<?php

namespace App\Observers;

use App\Models\Expense;
use App\Models\CashbookEntry;
use Illuminate\Support\Facades\DB;

class ExpenseObserver
{
    private function createCashbookEntry(Expense $expense, array $attributes): CashbookEntry
    {
        return CashbookEntry::createForSchool($expense->school_id, $attributes);
    }

    public function created(Expense $expense)
    {
        DB::transaction(function () use ($expense) {
            $this->createCashbookEntry($expense, [
                'transaction_type' => 'outflow',
                'entry_type' => 'original',
                'amount' => $expense->amount,
                'payment_method' => $expense->payment_method,
                'transaction_date' => $expense->expense_date ?? now()->toDateString(),
                'description' => 'Expense: ' . ($expense->description ?? 'Unspecified') . ' #' . $expense->id,
            ]);
        });
    }

    public function updated(Expense $expense)
    {
        DB::transaction(function () use ($expense) {
            $original = $expense->cashbookEntries()->where('entry_type', 'original')->first();

            if ($original) {
                $original->update([
                    'amount' => $expense->amount,
                    'payment_method' => $expense->payment_method,
                    'transaction_date' => $expense->expense_date ?? $original->transaction_date,
                    'description' => 'Expense: ' . ($expense->description ?? 'Unspecified') . ' #' . $expense->id,
                ]);
            } else {
                $this->createCashbookEntry($expense, [
                    'transaction_type' => 'outflow',
                    'entry_type' => 'original',
                    'amount' => $expense->amount,
                    'payment_method' => $expense->payment_method,
                    'transaction_date' => $expense->expense_date ?? now()->toDateString(),
                    'description' => 'Expense (recreated): ' . ($expense->description ?? '') . ' #' . $expense->id,
                ]);
            }
        });
    }

    public function deleting(Expense $expense)
    {
        if ($expense->isForceDeleting()) {
            return;
        }

        DB::transaction(function () use ($expense) {
            $original = $expense->cashbookEntries()->where('entry_type', 'original')->first();

            $this->createCashbookEntry($expense, [
                'transaction_type' => 'inflow',
                'entry_type' => 'reversal',
                'amount' => $expense->amount,
                'payment_method' => $expense->payment_method,
                'transaction_date' => now()->toDateString(),
                'description' => 'Reversal of expense #' . $expense->id,
                'related_entry_id' => $original ? $original->id : null,
            ]);
        });
    }

    public function restoring(Expense $expense)
    {
        DB::transaction(function () use ($expense) {
            $reversal = $expense->cashbookEntries()
                ->where('entry_type', 'reversal')
                ->orderByDesc('created_at')
                ->first();

            $this->createCashbookEntry($expense, [
                'transaction_type' => 'outflow',
                'entry_type' => 'restored',
                'amount' => $expense->amount,
                'payment_method' => $expense->payment_method,
                'transaction_date' => now()->toDateString(),
                'description' => 'Restored expense #' . $expense->id,
                'related_entry_id' => $reversal ? $reversal->id : null,
            ]);
        });
    }
}
