<?php

namespace Tests\Feature;

use App\Services\FinancialReportService;
use Tests\Support\TenantFixtureBuilder;
use Tests\TestCase;

class FinancialReportTest extends TestCase
{
    private array $fixtures;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixtures = TenantFixtureBuilder::createPair();
    }

    public function test_trial_balance_ties_after_fee_payment(): void
    {
        $tenant = $this->fixtures['tenantA'];
        $invoice = $tenant['invoice'];
        $accountant = $this->fixtures['accountantA'];

        $this->actingAs($accountant)
            ->post(route('payments.store', $invoice), [
                'amount' => 8000,
                'method' => 'Cash',
            ]);

        $this->syncLedgerDatesToTerm($tenant['term']);

        $service = app(FinancialReportService::class);
        $period = $service->resolvePeriod((int) $this->fixtures['schoolA']->id, 'term', $tenant['term']->id);
        $report = $service->trialBalance((int) $this->fixtures['schoolA']->id, $period['end']);

        $this->assertTrue($report['balanced']);
        $this->assertSame(8000.0, $report['totalDebits']);
        $this->assertSame(8000.0, $report['totalCredits']);
    }

    public function test_profit_and_loss_reflects_fee_income_in_term(): void
    {
        $tenant = $this->fixtures['tenantA'];
        $invoice = $tenant['invoice'];
        $accountant = $this->fixtures['accountantA'];
        $schoolId = (int) $this->fixtures['schoolA']->id;

        $this->actingAs($accountant)
            ->post(route('payments.store', $invoice), [
                'amount' => 4500,
                'method' => 'Cash',
            ]);

        $this->syncLedgerDatesToTerm($tenant['term']);

        $service = app(FinancialReportService::class);
        $period = $service->resolvePeriod($schoolId, 'term', $tenant['term']->id);
        $pl = $service->profitAndLoss($schoolId, $period['start'], $period['end']);

        $this->assertSame(4500.0, $pl['totalIncome']);
        $this->assertSame(0.0, $pl['totalExpenses']);
        $this->assertSame(4500.0, $pl['netSurplus']);
    }

    public function test_balance_sheet_balances_with_retained_earnings(): void
    {
        $tenant = $this->fixtures['tenantA'];
        $invoice = $tenant['invoice'];
        $accountant = $this->fixtures['accountantA'];
        $schoolId = (int) $this->fixtures['schoolA']->id;

        $this->actingAs($accountant)
            ->post(route('payments.store', $invoice), [
                'amount' => 6000,
                'method' => 'Cash',
            ]);

        $this->syncLedgerDatesToTerm($tenant['term']);

        $service = app(FinancialReportService::class);
        $period = $service->resolvePeriod($schoolId, 'term', $tenant['term']->id);
        $bs = $service->balanceSheet($schoolId, $period['end']);

        $this->assertTrue($bs['balanced']);
        $this->assertSame(6000.0, $bs['totalAssets']);
        $this->assertSame(6000.0, $bs['retainedEarnings']);
        $this->assertSame(6000.0, $bs['totalLiabilitiesAndEquity']);
    }

    public function test_financial_reports_page_loads_for_accountant(): void
    {
        $tenant = $this->fixtures['tenantA'];
        $accountant = $this->fixtures['accountantA'];

        $this->actingAs($accountant)
            ->get(route('reports.financial', [
                'report' => 'trial-balance',
                'view' => 'term',
                'term_id' => $tenant['term']->id,
            ]))
            ->assertOk()
            ->assertSee('Trial Balance');
    }

    private function syncLedgerDatesToTerm($term): void
    {
        $date = $term->start_date;

        \App\Models\InvoicePayment::query()->update(['payment_date' => $date]);
        \App\Models\LedgerEntry::withoutGlobalScopes()->update(['transaction_date' => $date]);
    }
}
