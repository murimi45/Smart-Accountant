<?php

namespace App\Services;

use App\Models\Account;
use App\Models\CashbookEntry;
use App\Models\Expense;
use App\Models\InvoicePayment;
use App\Models\InvoicePaymentReversal;
use App\Models\LedgerEntry;
use App\Models\OtherIncome;
use Database\Seeders\AccountsTableSeeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class LedgerService
{
    public function postFromCashbookEntry(CashbookEntry $entry): void
    {
        if ($entry->ledgerEntries()->exists()) {
            return;
        }

        $schoolId = (int) $entry->school_id;
        if (! $schoolId) {
            return;
        }

        $base = $this->baseAccountsFor($entry);
        if ($base === null) {
            return;
        }

        [$debitName, $creditName] = $base;

        if ($entry->entry_type === 'reversal') {
            [$debitName, $creditName] = [$creditName, $debitName];
        }

        $this->ensureAccountsExist($schoolId);

        DB::transaction(function () use ($entry, $schoolId, $debitName, $creditName) {
            $amount = round((float) $entry->amount, 2);
            if ($amount <= 0) {
                return;
            }

            $date = $entry->transaction_date ?? now()->toDateString();
            $description = $entry->description;

            $debitAccount = Account::defaultFor($schoolId, $debitName);
            $creditAccount = Account::defaultFor($schoolId, $creditName);

            LedgerEntry::createForSchool($schoolId, [
                'account_id' => $debitAccount->id,
                'cashbook_entry_id' => $entry->id,
                'debit' => $amount,
                'credit' => 0,
                'description' => $description,
                'transaction_date' => $date,
            ]);

            LedgerEntry::createForSchool($schoolId, [
                'account_id' => $creditAccount->id,
                'cashbook_entry_id' => $entry->id,
                'debit' => 0,
                'credit' => $amount,
                'description' => $description,
                'transaction_date' => $date,
            ]);

            $totalDebits = (float) LedgerEntry::withoutGlobalScopes()
                ->where('cashbook_entry_id', $entry->id)
                ->sum('debit');

            $totalCredits = (float) LedgerEntry::withoutGlobalScopes()
                ->where('cashbook_entry_id', $entry->id)
                ->sum('credit');

            if (abs($totalDebits - $totalCredits) > 0.001) {
                throw new RuntimeException("Unbalanced ledger posting for cashbook entry #{$entry->id}.");
            }
        });
    }

    /** @return array{0: string, 1: string}|null [debit account name, credit account name] */
    private function baseAccountsFor(CashbookEntry $entry): ?array
    {
        $cash = $this->cashAccountName($entry->payment_method);

        return match ($entry->source_type) {
            InvoicePayment::class,
            InvoicePaymentReversal::class => [$cash, 'Tuition Fees Income'],
            Expense::class => ['Miscellaneous Expense', $cash],
            OtherIncome::class => [$cash, 'Other School Income'],
            default => null,
        };
    }

    private function cashAccountName(?string $method): string
    {
        $method = strtolower((string) $method);

        return in_array($method, ['mpesa', 'bank', 'cheque'], true)
            ? 'Cash at Bank'
            : 'Cash at Hand';
    }

    private function ensureAccountsExist(int $schoolId): void
    {
        if (Account::withoutGlobalScopes()->where('school_id', $schoolId)->exists()) {
            return;
        }

        (new AccountsTableSeeder)->run($schoolId);
    }
}
