<?php

namespace App\Services;

use App\Models\BankDeposit;
use App\Models\BankReconciliationMatch;
use App\Models\CashbookEntry;
use App\Models\InvoicePayment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class BankReconciliationService
{
    /** Payment-related inflow sources eligible for bank matching. */
    private const PAYMENT_SOURCES = [
        InvoicePayment::class,
    ];

    public function recordDeposit(
        int $schoolId,
        User $user,
        string $depositDate,
        float $amount,
        ?string $reference = null,
        ?string $description = null
    ): BankDeposit {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Deposit amount must be greater than zero.');
        }

        return BankDeposit::createForSchool($schoolId, [
            'deposit_date' => $depositDate,
            'amount'       => $amount,
            'reference'    => $reference ? trim($reference) : null,
            'description'  => $description ? trim($description) : null,
            'status'       => BankDeposit::STATUS_UNMATCHED,
            'recorded_by'  => $user->id,
        ]);
    }

    public function match(int $schoolId, User $user, int $bankDepositId, int $cashbookEntryId): BankReconciliationMatch
    {
        return DB::transaction(function () use ($schoolId, $user, $bankDepositId, $cashbookEntryId) {
            $deposit = BankDeposit::forSchool($schoolId)->lockForUpdate()->findOrFail($bankDepositId);
            $entry = $this->eligibleEntryQuery($schoolId)->lockForUpdate()->findOrFail($cashbookEntryId);

            if (BankReconciliationMatch::where('cashbook_entry_id', $entry->id)->exists()) {
                throw new InvalidArgumentException('This cashbook entry is already matched to a bank deposit.');
            }

            $entryAmount = (float) $entry->amount;

            if ($entryAmount <= 0) {
                throw new InvalidArgumentException('Only inflow entries can be matched.');
            }

            if ($entryAmount > $deposit->remainingAmount() + 0.001) {
                throw new InvalidArgumentException(
                    'Payment amount (KSh '.number_format($entryAmount, 2).') exceeds the deposit remaining balance (KSh '
                    .number_format($deposit->remainingAmount(), 2).').'
                );
            }

            $match = BankReconciliationMatch::createForSchool($schoolId, [
                'bank_deposit_id'   => $deposit->id,
                'cashbook_entry_id' => $entry->id,
                'amount'            => $entryAmount,
                'matched_by'        => $user->id,
                'matched_at'        => now(),
            ]);

            $deposit->refreshStatus();

            return $match->load(['cashbookEntry.source', 'bankDeposit']);
        });
    }

    public function unmatch(int $schoolId, BankReconciliationMatch $match): void
    {
        DB::transaction(function () use ($schoolId, $match) {
            $match = BankReconciliationMatch::forSchool($schoolId)->lockForUpdate()->findOrFail($match->id);
            $deposit = BankDeposit::forSchool($schoolId)->lockForUpdate()->findOrFail($match->bank_deposit_id);

            $match->delete();
            $deposit->refreshStatus();
        });
    }

    public function updateDeposit(
        int $schoolId,
        BankDeposit $deposit,
        string $depositDate,
        float $amount,
        ?string $reference = null,
        ?string $description = null
    ): BankDeposit {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Deposit amount must be greater than zero.');
        }

        return DB::transaction(function () use ($schoolId, $deposit, $depositDate, $amount, $reference, $description) {
            $deposit = BankDeposit::forSchool($schoolId)->lockForUpdate()->findOrFail($deposit->id);

            $matched = $deposit->matchedAmount();

            if ($amount + 0.001 < $matched) {
                throw new InvalidArgumentException(
                    'Amount cannot be less than already matched total (KSh '.number_format($matched, 2).'). '
                    .'Unmatch payments first, or enter a higher amount.'
                );
            }

            $deposit->update([
                'deposit_date' => $depositDate,
                'amount'       => $amount,
                'reference'    => $reference ? trim($reference) : null,
                'description'  => $description ? trim($description) : null,
            ]);

            $deposit->refreshStatus();

            return $deposit->fresh();
        });
    }

    public function deleteDeposit(int $schoolId, BankDeposit $deposit): void
    {
        DB::transaction(function () use ($schoolId, $deposit) {
            $deposit = BankDeposit::forSchool($schoolId)->lockForUpdate()->findOrFail($deposit->id);
            $deposit->delete();
        });
    }

    /** @return Collection<int, CashbookEntry> */
    public function unmatchedPaymentInflows(int $schoolId, ?string $method = null, ?Carbon $from = null, ?Carbon $to = null): Collection
    {
        $matchedIds = BankReconciliationMatch::where('school_id', $schoolId)->pluck('cashbook_entry_id');

        $query = $this->eligibleEntryQuery($schoolId)
            ->whereNotIn('id', $matchedIds)
            ->with(['source.invoice.student']);

        if ($method) {
            $query->whereRaw('LOWER(payment_method) = ?', [strtolower($method)]);
        }

        if ($from) {
            $query->whereDate('transaction_date', '>=', $from->toDateString());
        }

        if ($to) {
            $query->whereDate('transaction_date', '<=', $to->toDateString());
        }

        return $query->orderByDesc('transaction_date')->get();
    }

    /** @return Collection<int, BankDeposit> */
    public function openDeposits(int $schoolId): Collection
    {
        return BankDeposit::forSchool($schoolId)
            ->whereIn('status', [BankDeposit::STATUS_UNMATCHED, BankDeposit::STATUS_PARTIAL])
            ->with(['matches.cashbookEntry', 'recordedBy'])
            ->orderByDesc('deposit_date')
            ->get();
    }

    /** @return list<array{cashbook_entry_id: int, score: int}> */
    public function suggestMatches(BankDeposit $deposit): array
    {
        $remaining = $deposit->remainingAmount();
        $date = Carbon::parse($deposit->deposit_date);

        $candidates = $this->unmatchedPaymentInflows((int) $deposit->school_id)
            ->filter(fn (CashbookEntry $e) => (float) $e->amount <= $remaining + 0.001);

        return $candidates->map(function (CashbookEntry $entry) use ($date, $remaining) {
            $score = 0;
            $entryDate = Carbon::parse($entry->transaction_date);
            $daysDiff = abs($entryDate->diffInDays($date));

            if ((float) $entry->amount === $remaining) {
                $score += 100;
            } elseif (abs((float) $entry->amount - $remaining) < 0.01) {
                $score += 90;
            }

            if ($daysDiff === 0) {
                $score += 30;
            } elseif ($daysDiff <= 3) {
                $score += 20;
            } elseif ($daysDiff <= 7) {
                $score += 10;
            }

            return ['cashbook_entry_id' => $entry->id, 'score' => $score];
        })
            ->sortByDesc('score')
            ->values()
            ->take(10)
            ->all();
    }

    private function eligibleEntryQuery(int $schoolId)
    {
        return CashbookEntry::forSchool($schoolId)
            ->where('transaction_type', 'inflow')
            ->where('entry_type', 'original')
            ->whereIn('source_type', self::PAYMENT_SOURCES);
    }
}
