<?php

namespace Tests\Feature;

use App\Models\ClassFee;
use App\Models\Invoice;
use App\Services\InvoiceService;
use App\Support\FinanceAudit;
use Spatie\Activitylog\Models\Activity;
use Tests\Support\TenantFixtureBuilder;
use Tests\TestCase;

class FinanceAuditLogTest extends TestCase
{
    private array $fixtures;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixtures = TenantFixtureBuilder::createPair();
    }

    public function test_class_fee_update_is_audited(): void
    {
        $tenant = $this->fixtures['tenantA'];
        $schoolId = $this->fixtures['schoolA']->id;

        $classFee = ClassFee::createForSchool($schoolId, [
            'class_id' => $tenant['class']->id,
            'term_id'  => $tenant['term']->id,
            'amount'   => 5000,
        ]);

        $this->actingAs($this->fixtures['adminA']);

        $classFee->update(['amount' => 6000]);

        $entry = Activity::query()
            ->where('log_name', FinanceAudit::LOG_NAME)
            ->where('school_id', $schoolId)
            ->where('event', 'class_fee.updated')
            ->latest('id')
            ->first();

        $this->assertNotNull($entry);
        $this->assertStringContainsString('6,000.00', $entry->description);
    }

    public function test_payment_is_audited(): void
    {
        $tenant = $this->fixtures['tenantA'];
        $schoolId = $this->fixtures['schoolA']->id;

        $this->actingAs($this->fixtures['accountantA']);

        app(InvoiceService::class)->paymentMade($tenant['invoice'], 1500, 'Cash');

        $entry = Activity::query()
            ->where('log_name', FinanceAudit::LOG_NAME)
            ->where('school_id', $schoolId)
            ->where('event', 'payment.recorded')
            ->latest('id')
            ->first();

        $this->assertNotNull($entry);
        $this->assertStringContainsString('1,500.00', $entry->description);
    }

    public function test_invoice_void_is_audited(): void
    {
        $tenant = $this->fixtures['tenantA'];
        $schoolId = $this->fixtures['schoolA']->id;

        $this->actingAs($this->fixtures['adminA']);

        $invoice = Invoice::withoutGlobalScopes()->findOrFail($tenant['invoice']->id);
        $invoice->update([
            'status' => Invoice::STATUS_VOIDED,
            'notes'  => 'Voided for testing',
        ]);

        $entry = Activity::query()
            ->where('log_name', FinanceAudit::LOG_NAME)
            ->where('school_id', $schoolId)
            ->where('event', 'invoice.voided')
            ->latest('id')
            ->first();

        $this->assertNotNull($entry);
        $this->assertStringContainsString('Voided for testing', $entry->description);
    }

    public function test_admin_can_view_audit_log_page(): void
    {
        $this->actingAs($this->fixtures['adminA'])
            ->get(route('audit.index'))
            ->assertOk()
            ->assertSee('Finance audit log');
    }
}
