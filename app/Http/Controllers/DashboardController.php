<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Term;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\OtherIncome;
use App\Models\Expense;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function showDashboard(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $viewType = $request->get('view', 'term'); // 'term' or 'annual'

        // Shared data
        $years = Term::where('school_id', $schoolId)
            ->pluck('year')
            ->unique()
            ->sortDesc()
            ->values();

        $terms = Term::where('school_id', $schoolId)
            ->orderBy('year', 'desc')
            ->orderBy('start_date', 'desc')
            ->get();

        // --------------------------
        // 1️⃣ TERM-BASED VIEW
        // --------------------------
        if ($viewType === 'term') {
            $termId = $request->get('term_id') ?? Term::currentId();
            $selectedTerm = Term::find($termId) ?? Term::current();

            // ✅ Handle case where no term exists yet
            if (!$selectedTerm) {
                $totalFeesBilled = 0;
                $totalFeesCollected = 0;
                $outstandingBalances = 0;
                $otherIncome = 0;
                $totalExpenses = 0;
                $netPosition = 0;
                $expensesByCategory = collect();
                $monthlyNet = [];

                return view('dashboard', compact(
                    'viewType',
                    'selectedTerm',
                    'totalFeesBilled',
                    'totalFeesCollected',
                    'outstandingBalances',
                    'otherIncome',
                    'totalExpenses',
                    'netPosition',
                    'expensesByCategory',
                    'monthlyNet',
                    'terms',
                    'years'
                ));
            }

            // ✅ If a term exists, compute normally
            $totalFeesBilled = Invoice::where('term_id', $selectedTerm->id)->sum('total_amount');
            $totalFeesCollected = InvoicePayment::whereHas('invoice', function ($q) use ($selectedTerm) {
                $q->where('term_id', $selectedTerm->id);
            })->sum('amount');
            $outstandingBalances = $totalFeesBilled - $totalFeesCollected;

            $otherIncome = OtherIncome::where('term_id', $selectedTerm->id)->sum('amount');
            $totalExpenses = Expense::where('term_id', $selectedTerm->id)->sum('amount');
            $netPosition = ($totalFeesCollected + $otherIncome) - $totalExpenses;

            $expensesByCategory = Expense::selectRaw('SUM(amount) as total, expense_category_id')
                ->where('term_id', $selectedTerm->id)
                ->groupBy('expense_category_id')
                ->with('category')
                ->get();

            // ✅ Monthly net within term range
            $monthlyNet = [];
            $monthsRange = CarbonPeriod::create($selectedTerm->start_date, '1 month', $selectedTerm->end_date);

            foreach ($monthsRange as $month) {
                $m = $month->month;
                $y = $month->year;

                $fees = InvoicePayment::whereMonth('created_at', $m)
                    ->whereYear('created_at', $y)
                    ->whereHas('invoice', function ($q) use ($selectedTerm) {
                        $q->where('term_id', $selectedTerm->id);
                    })
                    ->sum('amount');

                $expenses = Expense::whereMonth('created_at', $m)
                    ->whereYear('created_at', $y)
                    ->where('term_id', $selectedTerm->id)
                    ->sum('amount');

                $income = OtherIncome::whereMonth('created_at', $m)
                    ->whereYear('created_at', $y)
                    ->where('term_id', $selectedTerm->id)
                    ->sum('amount');

                $monthlyNet[] = ($fees + $income) - $expenses;
            }

            return view('dashboard', compact(
                'viewType',
                'selectedTerm',
                'totalFeesBilled',
                'totalFeesCollected',
                'outstandingBalances',
                'otherIncome',
                'totalExpenses',
                'netPosition',
                'expensesByCategory',
                'monthlyNet',
                'terms',
                'years'
            ));
        }

        // --------------------------
        // 2️⃣ ANNUAL VIEW
        // --------------------------
        $selectedYear = $request->get('year') ?? now()->year;

        $termIds = Term::where('year', $selectedYear)
            ->where('school_id', $schoolId)
            ->pluck('id');

        // ✅ Handle case where no term exists for that year
        if ($termIds->isEmpty()) {
            $totalFeesBilled = 0;
            $totalFeesCollected = 0;
            $outstandingBalances = 0;
            $otherIncome = 0;
            $totalExpenses = 0;
            $netPosition = 0;
            $expensesByCategory = collect();
            $monthlyNet = [];

            return view('dashboard', compact(
                'viewType',
                'selectedYear',
                'totalFeesBilled',
                'totalFeesCollected',
                'outstandingBalances',
                'otherIncome',
                'totalExpenses',
                'netPosition',
                'expensesByCategory',
                'monthlyNet',
                'terms',
                'years'
            ));
        }

        // ✅ Calculate normal yearly data
        $totalFeesBilled = Invoice::whereIn('term_id', $termIds)->sum('total_amount');
        $totalFeesCollected = InvoicePayment::whereHas('invoice', function ($q) use ($termIds) {
            $q->whereIn('term_id', $termIds);
        })->sum('amount');
        $outstandingBalances = $totalFeesBilled - $totalFeesCollected;

        $otherIncome = OtherIncome::whereIn('term_id', $termIds)->sum('amount');
        $totalExpenses = Expense::whereIn('term_id', $termIds)->sum('amount');
        $netPosition = ($totalFeesCollected + $otherIncome) - $totalExpenses;

        $expensesByCategory = Expense::selectRaw('SUM(amount) as total, expense_category_id')
            ->whereIn('term_id', $termIds)
            ->groupBy('expense_category_id')
            ->with('category')
            ->get();

        // ✅ Monthly net for full year
        $monthlyNet = [];
        for ($m = 1; $m <= 12; $m++) {
            $fees = InvoicePayment::whereMonth('created_at', $m)
                ->whereYear('created_at', $selectedYear)
                ->sum('amount');

            $expenses = Expense::whereMonth('created_at', $m)
                ->whereYear('created_at', $selectedYear)
                ->sum('amount');

            $income = OtherIncome::whereMonth('created_at', $m)
                ->whereYear('created_at', $selectedYear)
                ->sum('amount');

            $monthlyNet[] = ($fees + $income) - $expenses;
        }

        return view('dashboard', compact(
            'viewType',
            'selectedYear',
            'totalFeesBilled',
            'totalFeesCollected',
            'outstandingBalances',
            'otherIncome',
            'totalExpenses',
            'netPosition',
            'expensesByCategory',
            'monthlyNet',
            'terms',
            'years'
        ));
    }
}





