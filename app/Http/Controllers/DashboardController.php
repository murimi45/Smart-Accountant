<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Term;
use App\Models\AcademicYear;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\OtherIncome;
use App\Models\Expense;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function showDashboard(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $viewType = $request->get('view', 'term'); // 'term' or 'annual'

        // ── Shared data ──────────────────────────────────────────────────────
        // Load academic years with their terms — avoids N+1 later
        $academicYears = AcademicYear::whereHas('terms', function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            })
            ->with(['terms' => fn($q) => $q->where('school_id', $schoolId)
                                           ->orderBy('term_number')])
            ->orderByDesc('start_date')
            ->get();

        $terms = $academicYears->flatMap->terms; // flat collection of all terms

        // Recent payments — eager load student via invoice
        $recentPayments = InvoicePayment::with('invoice.student')
            ->whereHas('invoice', fn($q) => $q->whereHas('term', fn($q2) =>
                $q2->where('school_id', $schoolId)
            ))
            ->latest()
            ->take(5)
            ->get();

        // ── Empty state helper ───────────────────────────────────────────────
        $emptyMetrics = [
            'totalFeesBilled'    => 0,
            'totalFeesCollected' => 0,
            'outstandingBalances'=> 0,
            'otherIncome'        => 0,
            'totalExpenses'      => 0,
            'netPosition'        => 0,
            'expensesByCategory' => collect(),
            'monthlyNet'         => [],
        ];

        // ── TERM VIEW ────────────────────────────────────────────────────────
        if ($viewType === 'term') {
            $termId      = $request->get('term_id');
            $selectedTerm = $termId
                ? Term::with('academicYear')->find($termId)
                : Term::with('academicYear')->where('school_id', $schoolId)->where('active', true)->first();

            if (! $selectedTerm) {
                return view('dashboard', array_merge($emptyMetrics, compact(
                    'viewType', 'selectedTerm', 'terms', 'academicYears', 'recentPayments'
                )));
            }

            $metrics = $this->termMetrics($selectedTerm->id);

            return view('dashboard', array_merge($metrics, compact(
                'viewType', 'selectedTerm', 'terms', 'academicYears', 'recentPayments'
            )));
        }

        // ── ANNUAL VIEW ──────────────────────────────────────────────────────
        $selectedYearId  = $request->get('academic_year_id');
        $selectedYear    = $selectedYearId
            ? AcademicYear::find($selectedYearId)
            : AcademicYear::whereHas('terms', fn($q) => $q->where('school_id', $schoolId))
                          ->orderByDesc('start_date')
                          ->first();

        if (! $selectedYear) {
            return view('dashboard', array_merge($emptyMetrics, compact(
                'viewType', 'selectedYear', 'terms', 'academicYears', 'recentPayments'
            )));
        }

        $termIds = Term::where('academic_year_id', $selectedYear->id)
            ->where('school_id', $schoolId)
            ->pluck('id');

        if ($termIds->isEmpty()) {
            return view('dashboard', array_merge($emptyMetrics, compact(
                'viewType', 'selectedYear', 'terms', 'academicYears', 'recentPayments'
            )));
        }

        $metrics = $this->annualMetrics($termIds, $selectedYear);

        return view('dashboard', array_merge($metrics, compact(
            'viewType', 'selectedYear', 'terms', 'academicYears', 'recentPayments'
        )));
    }

    // ── Private: term-scoped metrics ─────────────────────────────────────────
    private function termMetrics(int $termId): array
    {
        // Single aggregated query instead of multiple separate queries
        $invoiceTotals = Invoice::where('term_id', $termId)
            ->selectRaw('SUM(total_amount) as billed')
            ->first();

        $totalFeesBilled    = $invoiceTotals->billed ?? 0;
        $totalFeesCollected = InvoicePayment::whereHas('invoice', fn($q) =>
            $q->where('term_id', $termId)
        )->sum('amount');

        $otherIncome   = OtherIncome::where('term_id', $termId)->sum('amount');
        $totalExpenses = Expense::where('term_id', $termId)->sum('amount');

        $expensesByCategory = Expense::selectRaw('SUM(amount) as total, expense_category_id')
            ->where('term_id', $termId)
            ->groupBy('expense_category_id')
            ->with('category')
            ->get();

        // Monthly net within term range
        $term       = Term::find($termId);
        $monthlyNet = [];

        if ($term->start_date && $term->end_date) {
            $range = CarbonPeriod::create($term->start_date, '1 month', $term->end_date);

            foreach ($range as $month) {
                $m = $month->month;
                $y = $month->year;

                $fees = InvoicePayment::whereMonth('created_at', $m)
                    ->whereYear('created_at', $y)
                    ->whereHas('invoice', fn($q) => $q->where('term_id', $termId))
                    ->sum('amount');

                $expenses = Expense::whereMonth('created_at', $m)
                    ->whereYear('created_at', $y)
                    ->where('term_id', $termId)
                    ->sum('amount');

                $income = OtherIncome::whereMonth('created_at', $m)
                    ->whereYear('created_at', $y)
                    ->where('term_id', $termId)
                    ->sum('amount');

                $monthlyNet[] = ($fees + $income) - $expenses;
            }
        }

        return [
            'totalFeesBilled'     => $totalFeesBilled,
            'totalFeesCollected'  => $totalFeesCollected,
            'outstandingBalances' => $totalFeesBilled - $totalFeesCollected,
            'otherIncome'         => $otherIncome,
            'totalExpenses'       => $totalExpenses,
            'netPosition'         => ($totalFeesCollected + $otherIncome) - $totalExpenses,
            'expensesByCategory'  => $expensesByCategory,
            'monthlyNet'          => $monthlyNet,
        ];
    }

    // ── Private: annual-scoped metrics ───────────────────────────────────────
    private function annualMetrics($termIds, AcademicYear $selectedYear): array
    {
        $totalFeesBilled    = Invoice::whereIn('term_id', $termIds)->sum('total_amount');
        $totalFeesCollected = InvoicePayment::whereHas('invoice', fn($q) =>
            $q->whereIn('term_id', $termIds)
        )->sum('amount');

        $otherIncome   = OtherIncome::whereIn('term_id', $termIds)->sum('amount');
        $totalExpenses = Expense::whereIn('term_id', $termIds)->sum('amount');

        $expensesByCategory = Expense::selectRaw('SUM(amount) as total, expense_category_id')
            ->whereIn('term_id', $termIds)
            ->groupBy('expense_category_id')
            ->with('category')
            ->get();

        // Monthly net across full academic year (Jan–Dec or actual range)
        $monthlyNet = [];
        $start      = $selectedYear->start_date ?? now()->startOfYear();
        $end        = $selectedYear->end_date   ?? now()->endOfYear();
        $range      = CarbonPeriod::create($start, '1 month', $end);

        foreach ($range as $month) {
            $m = $month->month;
            $y = $month->year;

            $fees = InvoicePayment::whereMonth('created_at', $m)
                ->whereYear('created_at', $y)
                ->whereHas('invoice', fn($q) => $q->whereIn('term_id', $termIds))
                ->sum('amount');

            $expenses = Expense::whereMonth('created_at', $m)
                ->whereYear('created_at', $y)
                ->whereIn('term_id', $termIds)
                ->sum('amount');

            $income = OtherIncome::whereMonth('created_at', $m)
                ->whereYear('created_at', $y)
                ->whereIn('term_id', $termIds)
                ->sum('amount');

            $monthlyNet[] = ($fees + $income) - $expenses;
        }

        return [
            'totalFeesBilled'     => $totalFeesBilled,
            'totalFeesCollected'  => $totalFeesCollected,
            'outstandingBalances' => $totalFeesBilled - $totalFeesCollected,
            'otherIncome'         => $otherIncome,
            'totalExpenses'       => $totalExpenses,
            'netPosition'         => ($totalFeesCollected + $otherIncome) - $totalExpenses,
            'expensesByCategory'  => $expensesByCategory,
            'monthlyNet'          => $monthlyNet,
        ];
    }
}