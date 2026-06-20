<?php

namespace Tests\Feature;

use App\Models\Budget;
use App\Models\Expense;
use App\Services\BudgetVarianceService;
use Illuminate\Database\Eloquent\Model;
use Tests\Support\TenantFixtureBuilder;
use Tests\TestCase;

class BudgetTest extends TestCase
{
    private array $fixtures;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixtures = TenantFixtureBuilder::createPair();
    }

    public function test_budgets_can_be_saved_per_category_and_term(): void
    {
        $tenant = $this->fixtures['tenantA'];
        $accountant = $this->fixtures['accountantA'];
        $category = $tenant['expenseCategory'];

        $this->actingAs($accountant)
            ->put(route('budgets.update'), [
                'term_id' => $tenant['term']->id,
                'amounts' => [
                    $category->id => 50000,
                ],
                'notes' => [
                    $category->id => 'Term supplies cap',
                ],
            ])
            ->assertRedirect(route('budgets.index', ['term_id' => $tenant['term']->id]));

        $budget = Budget::where('school_id', $this->fixtures['schoolA']->id)
            ->where('term_id', $tenant['term']->id)
            ->where('expense_category_id', $category->id)
            ->first();

        $this->assertNotNull($budget);
        $this->assertSame(50000.0, (float) $budget->amount);
        $this->assertSame('Term supplies cap', $budget->notes);
    }

    public function test_variance_report_compares_budget_to_actual_expenses(): void
    {
        $tenant = $this->fixtures['tenantA'];
        $accountant = $this->fixtures['accountantA'];
        $schoolId = (int) $this->fixtures['schoolA']->id;
        $category = $tenant['expenseCategory'];
        $term = $tenant['term'];

        Model::withoutEvents(function () use ($schoolId, $category, $term, $accountant) {
            Budget::createForSchool($schoolId, [
                'expense_category_id' => $category->id,
                'term_id'             => $term->id,
                'amount'              => 10000,
            ]);

            Expense::createForSchool($schoolId, [
                'expense_category_id' => $category->id,
                'description'       => 'Stationery',
                'amount'              => 3500,
                'payment_method'      => 'cash',
                'expense_date'        => $term->start_date,
                'term_id'             => $term->id,
                'year'                => 2026,
                'created_by'          => $accountant->id,
            ]);
        });

        $report = app(BudgetVarianceService::class)->build($schoolId, $term->id);

        $this->assertSame(10000.0, $report['totals']['budget']);
        $this->assertSame(3500.0, $report['totals']['actual']);
        $this->assertSame(6500.0, $report['totals']['variance']);
        $this->assertCount(1, $report['rows']);
        $this->assertFalse($report['rows'][0]['over_budget']);
    }

    public function test_variance_page_loads(): void
    {
        $tenant = $this->fixtures['tenantA'];

        $this->actingAs($this->fixtures['accountantA'])
            ->get(route('reports.budget-variance', ['term_id' => $tenant['term']->id]))
            ->assertOk()
            ->assertSee('Budget Variance Report');
    }
}
