<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Services\AgedDebtorsReportService;
use Carbon\Carbon;
use Tests\Support\TenantFixtureBuilder;
use Tests\TestCase;

class AgedDebtorsReportTest extends TestCase
{
    private array $fixtures;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixtures = TenantFixtureBuilder::createPair();
    }

    public function test_report_buckets_outstanding_balances_by_invoice_age(): void
    {
        $tenant = $this->fixtures['tenantA'];
        $schoolId = $this->fixtures['schoolA']->id;
        $asOf = Carbon::parse('2026-06-18');

        Invoice::where('school_id', $schoolId)->update(['balance' => 0]);

        Invoice::create([
            'school_id'      => $schoolId,
            'student_id'     => $tenant['student']->id,
            'term_id'        => $tenant['term']->id,
            'enrollment_id'  => $tenant['enrollment']->id,
            'total_amount'   => 5000,
            'amount_paid'    => 0,
            'balance'        => 5000,
            'invoice_date'   => '2026-06-01',
            'status'         => Invoice::STATUS_UNPAID,
        ]);

        Invoice::create([
            'school_id'      => $schoolId,
            'student_id'     => $tenant['student']->id,
            'term_id'        => $tenant['term']->id,
            'enrollment_id'  => $tenant['enrollment']->id,
            'total_amount'   => 3000,
            'amount_paid'    => 1000,
            'balance'        => 2000,
            'invoice_date'   => '2026-03-01',
            'status'         => Invoice::STATUS_PARTIALLY_PAID,
        ]);

        $report = app(AgedDebtorsReportService::class)->build(
            $schoolId,
            $tenant['term']->id,
            null,
            $asOf
        );

        $this->assertSame(1, $report['debtorCount']);
        $this->assertSame(7000.0, $report['totals']['total_balance']);
        $this->assertSame(5000.0, $report['totals']['current']);
        $this->assertSame(2000.0, $report['totals']['days_91_plus']);
    }

    public function test_report_page_and_exports_are_accessible_to_tenant_users(): void
    {
        $tenant = $this->fixtures['tenantA'];
        $termId = $tenant['term']->id;

        $this->actingAs($this->fixtures['accountantA'])
            ->get(route('reports.aged-debtors', ['term_id' => $termId]))
            ->assertOk()
            ->assertSee('Aged Debtors Report');

        $this->actingAs($this->fixtures['accountantA'])
            ->get(route('reports.aged-debtors.pdf', ['term_id' => $termId]))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->actingAs($this->fixtures['accountantA'])
            ->get(route('reports.aged-debtors.excel', ['term_id' => $termId]))
            ->assertOk();
    }

    public function test_cross_tenant_term_filter_is_blocked(): void
    {
        $termB = $this->fixtures['tenantB']['term'];

        $response = $this->actingAs($this->fixtures['accountantA'])
            ->get(route('reports.aged-debtors', ['term_id' => $termB->id]));

        $response->assertNotFound();
    }
}
