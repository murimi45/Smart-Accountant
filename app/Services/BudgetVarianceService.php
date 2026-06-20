<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Term;

class BudgetVarianceService
{
    /**
     * @return array{
     *     rows: list<array<string, mixed>>,
     *     totals: array{budget: float, actual: float, variance: float},
     *     term: Term
     * }
     */
    public function build(int $schoolId, int $termId): array
    {
        $term = Term::forSchool($schoolId)->with('academicYear')->findOrFail($termId);

        $categories = ExpenseCategory::where('school_id', $schoolId)->orderBy('name')->get();

        $budgets = Budget::where('school_id', $schoolId)
            ->where('term_id', $termId)
            ->get()
            ->keyBy('expense_category_id');

        $actuals = Expense::where('school_id', $schoolId)
            ->where('term_id', $termId)
            ->selectRaw('expense_category_id, COALESCE(SUM(amount), 0) as total')
            ->groupBy('expense_category_id')
            ->pluck('total', 'expense_category_id');

        $rows = [];
        $totalBudget = 0.0;
        $totalActual = 0.0;

        foreach ($categories as $category) {
            $budgetAmount = (float) ($budgets->get($category->id)?->amount ?? 0);
            $actualAmount = (float) ($actuals->get($category->id) ?? 0);

            if ($budgetAmount <= 0 && $actualAmount <= 0) {
                continue;
            }

            $variance = $budgetAmount - $actualAmount;
            $utilization = $budgetAmount > 0 ? round(($actualAmount / $budgetAmount) * 100, 1) : null;

            $rows[] = [
                'category'      => $category,
                'budget'        => round($budgetAmount, 2),
                'actual'        => round($actualAmount, 2),
                'variance'      => round($variance, 2),
                'utilization'   => $utilization,
                'over_budget'   => $budgetAmount > 0 && $actualAmount > $budgetAmount,
            ];

            $totalBudget += $budgetAmount;
            $totalActual += $actualAmount;
        }

        return [
            'rows'   => $rows,
            'totals' => [
                'budget'   => round($totalBudget, 2),
                'actual'   => round($totalActual, 2),
                'variance' => round($totalBudget - $totalActual, 2),
            ],
            'term'   => $term,
        ];
    }
}
