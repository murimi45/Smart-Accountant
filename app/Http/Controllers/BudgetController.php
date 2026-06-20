<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\ExpenseCategory;
use App\Models\Term;
use App\Services\BudgetVarianceService;
use App\Services\InvoiceService;
use App\Support\TenantFilters;
use App\Support\TenantRules;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Budget::class);

        $schoolId = TenantFilters::validate($request);
        $currentTerm = InvoiceService::currentTermForSchool($schoolId);
        $termId = $request->filled('term_id')
            ? (int) $request->term_id
            : ($currentTerm?->id);

        $terms = Term::where('school_id', $schoolId)->with('academicYear')->orderByDesc('start_date')->get();
        $selectedTerm = $termId ? $terms->firstWhere('id', $termId) : null;

        $categories = ExpenseCategory::where('school_id', $schoolId)->orderBy('name')->get();

        $budgets = $termId
            ? Budget::where('school_id', $schoolId)
                ->where('term_id', $termId)
                ->get()
                ->keyBy('expense_category_id')
            : collect();

        return view('budgets.index', compact(
            'categories',
            'budgets',
            'terms',
            'termId',
            'selectedTerm',
            'currentTerm'
        ));
    }

    public function update(Request $request)
    {
        $this->authorize('update', Budget::class);

        $schoolId = TenantFilters::schoolId();

        $validated = $request->validate([
            'term_id' => ['required', TenantRules::terms()],
            'amounts' => 'array',
            'amounts.*' => 'nullable|numeric|min:0',
            'notes' => 'array',
            'notes.*' => 'nullable|string|max:500',
            'school_id' => TenantRules::prohibitedSchoolId(),
        ]);

        $termId = (int) $validated['term_id'];
        $amounts = $validated['amounts'] ?? [];
        $notes = $validated['notes'] ?? [];

        foreach ($amounts as $categoryId => $amount) {
            $categoryId = (int) $categoryId;
            ExpenseCategory::forSchool($schoolId)->findOrFail($categoryId);

            $amount = $amount === null || $amount === '' ? null : round((float) $amount, 2);
            $note = $notes[$categoryId] ?? null;

            if ($amount === null || $amount <= 0) {
                Budget::where('school_id', $schoolId)
                    ->where('term_id', $termId)
                    ->where('expense_category_id', $categoryId)
                    ->delete();

                continue;
            }

            Budget::updateOrCreate(
                [
                    'school_id'           => $schoolId,
                    'expense_category_id' => $categoryId,
                    'term_id'             => $termId,
                ],
                [
                    'amount' => $amount,
                    'notes'  => $note,
                ]
            );
        }

        return redirect()
            ->route('budgets.index', ['term_id' => $termId])
            ->with('success', 'Budgets saved for the selected term.');
    }

    public function variance(Request $request, BudgetVarianceService $varianceService)
    {
        $this->authorize('viewAny', Budget::class);

        $schoolId = TenantFilters::validate($request);
        $currentTerm = InvoiceService::currentTermForSchool($schoolId);
        $termId = $request->filled('term_id')
            ? (int) $request->term_id
            : ($currentTerm?->id);

        $terms = Term::where('school_id', $schoolId)->with('academicYear')->orderByDesc('start_date')->get();

        if (! $termId) {
            return view('reports.budget-variance.index', [
                'report'      => null,
                'terms'       => $terms,
                'termId'      => null,
                'currentTerm' => $currentTerm,
            ]);
        }

        $report = $varianceService->build($schoolId, $termId);

        return view('reports.budget-variance.index', [
            'report'      => $report,
            'terms'       => $terms,
            'termId'      => $termId,
            'currentTerm' => $currentTerm,
        ]);
    }
}
