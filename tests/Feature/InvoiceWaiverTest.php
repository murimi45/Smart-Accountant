<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\InvoiceWaiver;
use App\Services\InvoiceWaiverService;
use Tests\Support\TenantFixtureBuilder;
use Tests\TestCase;

class InvoiceWaiverTest extends TestCase
{
    private array $fixtures;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixtures = TenantFixtureBuilder::createPair();
    }

    public function test_percentage_invoice_waiver_reduces_total_after_approval(): void
    {
        $tenant = $this->fixtures['tenantA'];
        $invoice = $this->invoiceWithItems($tenant['invoice']);
        $admin = $this->fixtures['adminA'];
        $accountant = $this->fixtures['accountantA'];

        $this->actingAs($accountant)
            ->post(route('waivers.store', $invoice), [
                'scope'         => 'invoice',
                'discount_type' => 'percentage',
                'value'         => 10,
                'reason'        => 'Partial bursary',
            ])
            ->assertRedirect();

        $waiver = InvoiceWaiver::where('invoice_id', $invoice->id)->first();
        $this->assertNotNull($waiver);
        $this->assertSame(InvoiceWaiver::STATUS_PENDING, $waiver->status);

        $this->actingAs($admin)
            ->post(route('waivers.approve', $waiver))
            ->assertRedirect();

        $invoice->refresh();
        $waiver->refresh();

        $this->assertSame(InvoiceWaiver::STATUS_APPROVED, $waiver->status);
        $this->assertSame(1000.0, (float) $waiver->computed_amount);
        $this->assertSame(9000.0, (float) $invoice->total_amount);
        $this->assertSame(9000.0, (float) $invoice->balance);
        $this->assertTrue($invoice->items()->whereNotNull('invoice_waiver_id')->exists());
    }

    public function test_fixed_line_waiver_applies_to_selected_fee_line(): void
    {
        $tenant = $this->fixtures['tenantA'];
        $invoice = $this->invoiceWithItems($tenant['invoice'])->fresh(['items']);
        $feeItem = $invoice->items->firstWhere('amount', '>', 0);

        $waiver = app(InvoiceWaiverService::class)->request(
            $invoice,
            $this->fixtures['accountantA'],
            InvoiceWaiver::SCOPE_LINE,
            InvoiceWaiver::TYPE_FIXED,
            2000,
            'Line bursary',
            $feeItem->id
        );

        app(InvoiceWaiverService::class)->approve($waiver, $this->fixtures['adminA']);

        $invoice->refresh();
        $waiver->refresh();

        $this->assertSame(2000.0, (float) $waiver->computed_amount);
        $this->assertSame(8000.0, (float) $invoice->total_amount);
    }

    public function test_rejected_waiver_does_not_change_invoice(): void
    {
        $tenant = $this->fixtures['tenantA'];
        $invoice = $this->invoiceWithItems($tenant['invoice']);

        $waiver = app(InvoiceWaiverService::class)->request(
            $invoice,
            $this->fixtures['accountantA'],
            InvoiceWaiver::SCOPE_INVOICE,
            InvoiceWaiver::TYPE_FIXED,
            1000,
            'Should not apply'
        );

        app(InvoiceWaiverService::class)->reject(
            $waiver,
            $this->fixtures['adminA'],
            'Insufficient documentation'
        );

        $invoice->refresh();

        $this->assertSame(10000.0, (float) $invoice->total_amount);
        $this->assertFalse($invoice->items()->whereNotNull('invoice_waiver_id')->exists());
    }

    public function test_accountant_cannot_approve_waiver(): void
    {
        $tenant = $this->fixtures['tenantA'];
        $invoice = $this->invoiceWithItems($tenant['invoice']);

        $waiver = app(InvoiceWaiverService::class)->request(
            $invoice,
            $this->fixtures['accountantA'],
            InvoiceWaiver::SCOPE_INVOICE,
            InvoiceWaiver::TYPE_FIXED,
            500,
            'Needs admin'
        );

        $this->actingAs($this->fixtures['accountantA'])
            ->post(route('waivers.approve', $waiver))
            ->assertForbidden();
    }

    private function invoiceWithItems(Invoice $invoice): Invoice
    {
        if ($invoice->items()->whereNull('invoice_waiver_id')->exists()) {
            return $invoice;
        }

        $invoice->items()->create([
            'term_id'     => $invoice->term_id,
            'description' => 'Class Fee: Grade 1',
            'amount'      => 10000,
        ]);

        return $invoice->fresh(['items']);
    }
}
