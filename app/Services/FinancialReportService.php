<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Account;
use App\Models\LedgerEntry;
use App\Models\Term;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class FinancialReportService
{
    /**
     * @return array{
     *     start: Carbon,
     *     end: Carbon,
     *     label: string,
     *     viewType: string,
     *     term: ?Term,
     *     academicYear: ?AcademicYear
     * }
     */
    public function resolvePeriod(
        int $schoolId,
        string $viewType = 'term',
        ?int $termId = null,
        ?int $academicYearId = null
    ): array {
        if ($viewType === 'year') {
            return $this->resolveYearPeriod($schoolId, $academicYearId);
        }

        return $this->resolveTermPeriod($schoolId, $termId);
    }

    /**
     * @return array{
     *     rows: list<array<string, mixed>>,
     *     totalDebits: float,
     *     totalCredits: float,
     *     balanced: bool
     * }
     */
    public function trialBalance(int $schoolId, Carbon $asOf): array
    {
        $accounts = Account::where('school_id', $schoolId)->orderBy('category')->orderBy('name')->get();
        $sums = $this->sumsByAccount($schoolId, null, $asOf)->keyBy('account_id');

        $rows = [];
        $totalDebits = 0.0;
        $totalCredits = 0.0;

        foreach ($accounts as $account) {
            $totals = $sums->get($account->id, ['total_debit' => 0.0, 'total_credit' => 0.0]);
            $debitSum = (float) $totals['total_debit'];
            $creditSum = (float) $totals['total_credit'];
            $signed = $this->signedBalance($account, $debitSum, $creditSum);

            if (abs($signed) < 0.005) {
                continue;
            }

            $debitColumn = 0.0;
            $creditColumn = 0.0;

            if ($account->normal_balance === 'debit') {
                $debitColumn = max(0, $signed);
                $creditColumn = max(0, -$signed);
            } else {
                $creditColumn = max(0, $signed);
                $debitColumn = max(0, -$signed);
            }

            $totalDebits += $debitColumn;
            $totalCredits += $creditColumn;

            $rows[] = [
                'account'       => $account,
                'debit'         => $debitColumn,
                'credit'        => $creditColumn,
                'category'      => $account->category,
            ];
        }

        return [
            'rows'         => $rows,
            'totalDebits'  => round($totalDebits, 2),
            'totalCredits' => round($totalCredits, 2),
            'balanced'     => abs($totalDebits - $totalCredits) < 0.02,
        ];
    }

    /**
     * @return array{
     *     incomeRows: list<array<string, mixed>>,
     *     expenseRows: list<array<string, mixed>>,
     *     totalIncome: float,
     *     totalExpenses: float,
     *     netSurplus: float
     * }
     */
    public function profitAndLoss(int $schoolId, Carbon $from, Carbon $to): array
    {
        $accounts = Account::where('school_id', $schoolId)
            ->whereIn('category', ['income', 'expense'])
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        $sums = $this->sumsByAccount($schoolId, $from, $to)->keyBy('account_id');

        $incomeRows = [];
        $expenseRows = [];
        $totalIncome = 0.0;
        $totalExpenses = 0.0;

        foreach ($accounts as $account) {
            $totals = $sums->get($account->id, ['total_debit' => 0.0, 'total_credit' => 0.0]);
            $debitSum = (float) $totals['total_debit'];
            $creditSum = (float) $totals['total_credit'];
            $signed = $this->signedBalance($account, $debitSum, $creditSum);

            if (abs($signed) < 0.005) {
                continue;
            }

            $row = [
                'account' => $account,
                'amount'  => round(abs($signed), 2),
            ];

            if ($account->category === 'income') {
                $incomeRows[] = $row;
                $totalIncome += $signed;
            } else {
                $expenseRows[] = $row;
                $totalExpenses += $signed;
            }
        }

        return [
            'incomeRows'    => $incomeRows,
            'expenseRows'   => $expenseRows,
            'totalIncome'   => round($totalIncome, 2),
            'totalExpenses' => round($totalExpenses, 2),
            'netSurplus'    => round($totalIncome - $totalExpenses, 2),
        ];
    }

    /**
     * @return array{
     *     assets: list<array<string, mixed>>,
     *     liabilities: list<array<string, mixed>>,
     *     equity: list<array<string, mixed>>,
     *     retainedEarnings: float,
     *     totalAssets: float,
     *     totalLiabilitiesAndEquity: float,
     *     balanced: bool
     * }
     */
    public function balanceSheet(int $schoolId, Carbon $asOf): array
    {
        $accounts = Account::where('school_id', $schoolId)->orderBy('category')->orderBy('name')->get();
        $sums = $this->sumsByAccount($schoolId, null, $asOf)->keyBy('account_id');

        $assets = [];
        $liabilities = [];
        $equity = [];
        $totalAssets = 0.0;
        $totalLiabilities = 0.0;
        $totalEquityAccounts = 0.0;
        $retainedEarnings = 0.0;

        foreach ($accounts as $account) {
            $totals = $sums->get($account->id, ['total_debit' => 0.0, 'total_credit' => 0.0]);
            $signed = $this->signedBalance(
                $account,
                (float) $totals['total_debit'],
                (float) $totals['total_credit']
            );

            if ($account->category === 'income') {
                $retainedEarnings += $signed;
                continue;
            }

            if ($account->category === 'expense') {
                $retainedEarnings -= $signed;
                continue;
            }

            if (abs($signed) < 0.005) {
                continue;
            }

            $row = ['account' => $account, 'amount' => round(abs($signed), 2)];

            if ($account->category === 'asset' && $signed > 0) {
                $assets[] = $row;
                $totalAssets += $signed;
            } elseif ($account->category === 'liability' && $signed > 0) {
                $liabilities[] = $row;
                $totalLiabilities += $signed;
            } elseif ($account->category === 'equity' && $signed > 0) {
                $equity[] = $row;
                $totalEquityAccounts += $signed;
            }
        }

        $retainedEarnings = round($retainedEarnings, 2);
        $totalAssets = round($totalAssets, 2);
        $totalLiabilities = round($totalLiabilities, 2);
        $totalEquityAccounts = round($totalEquityAccounts, 2);
        $totalLiabilitiesAndEquity = round($totalLiabilities + $totalEquityAccounts + $retainedEarnings, 2);

        return [
            'assets'                    => $assets,
            'liabilities'               => $liabilities,
            'equity'                    => $equity,
            'retainedEarnings'          => $retainedEarnings,
            'totalAssets'               => $totalAssets,
            'totalLiabilitiesAndEquity' => $totalLiabilitiesAndEquity,
            'balanced'                  => abs($totalAssets - $totalLiabilitiesAndEquity) < 0.02,
        ];
    }

    /** @return Collection<int, array{account_id: int, total_debit: float, total_credit: float}> */
    private function sumsByAccount(int $schoolId, ?Carbon $from, Carbon $to): Collection
    {
        return LedgerEntry::query()
            ->where('school_id', $schoolId)
            ->whereDate('transaction_date', '<=', $to->toDateString())
            ->when($from, fn ($q) => $q->whereDate('transaction_date', '>=', $from->toDateString()))
            ->selectRaw('account_id, COALESCE(SUM(debit), 0) as total_debit, COALESCE(SUM(credit), 0) as total_credit')
            ->groupBy('account_id')
            ->get()
            ->map(fn ($row) => [
                'account_id'   => (int) $row->account_id,
                'total_debit'  => (float) $row->total_debit,
                'total_credit' => (float) $row->total_credit,
            ]);
    }

    private function signedBalance(Account $account, float $debits, float $credits): float
    {
        return $account->normal_balance === 'debit'
            ? $debits - $credits
            : $credits - $debits;
    }

    /** @return array{start: Carbon, end: Carbon, label: string, viewType: string, term: ?Term, academicYear: ?AcademicYear} */
    private function resolveTermPeriod(int $schoolId, ?int $termId): array
    {
        $term = $termId
            ? Term::forSchool($schoolId)->with('academicYear')->findOrFail($termId)
            : Term::with('academicYear')->where('school_id', $schoolId)->where('active', true)->first()
                ?? Term::where('school_id', $schoolId)->with('academicYear')->orderByDesc('start_date')->first();

        if (! $term) {
            throw new InvalidArgumentException('No term found for this school.');
        }

        $start = $term->start_date
            ? Carbon::parse($term->start_date)->startOfDay()
            : Carbon::parse($term->year.'-01-01')->startOfDay();

        $end = $term->end_date
            ? Carbon::parse($term->end_date)->endOfDay()
            : Carbon::now()->endOfDay();

        $yearLabel = $term->year ?? $term->academicYear?->name ?? '';

        return [
            'start'        => $start,
            'end'          => $end,
            'label'        => trim($term->name.' '.$yearLabel),
            'viewType'     => 'term',
            'term'         => $term,
            'academicYear' => $term->academicYear,
        ];
    }

    /** @return array{start: Carbon, end: Carbon, label: string, viewType: string, term: ?Term, academicYear: ?AcademicYear} */
    private function resolveYearPeriod(int $schoolId, ?int $academicYearId): array
    {
        $year = $academicYearId
            ? AcademicYear::forSchool($schoolId)->findOrFail($academicYearId)
            : AcademicYear::whereHas('terms', fn ($q) => $q->where('school_id', $schoolId))
                ->orderByDesc('start_date')
                ->first();

        if (! $year) {
            throw new InvalidArgumentException('No academic year found for this school.');
        }

        $terms = Term::where('school_id', $schoolId)
            ->where('academic_year_id', $year->id)
            ->orderBy('start_date')
            ->get();

        $start = $year->start_date
            ? Carbon::parse($year->start_date)->startOfDay()
            : ($terms->first()?->start_date
                ? Carbon::parse($terms->first()->start_date)->startOfDay()
                : Carbon::now()->startOfYear());

        $end = $year->end_date
            ? Carbon::parse($year->end_date)->endOfDay()
            : ($terms->last()?->end_date
                ? Carbon::parse($terms->last()->end_date)->endOfDay()
                : Carbon::now()->endOfDay());

        return [
            'start'        => $start,
            'end'          => $end,
            'label'        => $year->name,
            'viewType'     => 'year',
            'term'         => null,
            'academicYear' => $year,
        ];
    }
}
