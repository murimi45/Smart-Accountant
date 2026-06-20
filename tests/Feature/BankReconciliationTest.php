<?php

namespace Tests\Feature;

use App\Models\BankDeposit;
use App\Models\CashbookEntry;
use App\Models\InvoicePayment;
use App\Services\BankReconciliationService;
use Tests\Support\TenantFixtureBuilder;
use Tests\TestCase;

class BankReconciliationTest extends TestCase
{
    private array $fixtures;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixtures = TenantFixtureBuilder::createPair();
    }

    public function test_payment_inflow_can_be_matched_to_bank_deposit(): void
    {
        $tenant = $this->fixtures['tenantA'];
        $schoolId = $this->fixtures['schoolA']->id;
        $payment = $tenant['invoice']->payments()->first();

        if (! $payment) {
            $payment = InvoicePayment::create([
                'invoice_id'   => $tenant['invoice']->id,
                'amount'       => 2500,
                'method'       => 'Bank',
                'payment_date' => now()->toDateString(),
            ]);
        }

        $entry = CashbookEntry::where('source_type', InvoicePayment::class)
            ->where('source_id', $payment->id)
            ->first();

        $this->assertNotNull($entry);

        $service = app(BankReconciliationService::class);

        $deposit = $service->recordDeposit(
            $schoolId,
            $this->fixtures['accountantA'],
            now()->toDateString(),
            2500,
            'FT123',
            'Bank batch'
        );

        $service->match($schoolId, $this->fixtures['accountantA'], $deposit->id, $entry->id);

        $deposit->refresh();

        $this->assertSame(BankDeposit::STATUS_RECONCILED, $deposit->status);
        $this->assertTrue($entry->fresh()->isReconciled());
    }

    public function test_partial_deposit_allows_multiple_payment_matches(): void
    {
        $tenant = $this->fixtures['tenantA'];
        $schoolId = $this->fixtures['schoolA']->id;

        $paymentA = InvoicePayment::create([
            'invoice_id'   => $tenant['invoice']->id,
            'amount'       => 3000,
            'method'       => 'Bank',
            'payment_date' => now()->toDateString(),
        ]);

        $paymentB = InvoicePayment::create([
            'invoice_id'   => $tenant['invoice']->id,
            'amount'       => 2000,
            'method'       => 'Bank',
            'payment_date' => now()->toDateString(),
        ]);

        $entryA = CashbookEntry::where('source_type', InvoicePayment::class)->where('source_id', $paymentA->id)->first();
        $entryB = CashbookEntry::where('source_type', InvoicePayment::class)->where('source_id', $paymentB->id)->first();

        $service = app(BankReconciliationService::class);
        $deposit = $service->recordDeposit($schoolId, $this->fixtures['accountantA'], now()->toDateString(), 5000);

        $service->match($schoolId, $this->fixtures['accountantA'], $deposit->id, $entryA->id);
        $service->match($schoolId, $this->fixtures['accountantA'], $deposit->id, $entryB->id);

        $deposit->refresh();

        $this->assertSame(BankDeposit::STATUS_RECONCILED, $deposit->status);
        $this->assertSame(2, $deposit->matches()->count());
    }

    public function test_reconciliation_page_loads_for_accountant(): void
    {
        $this->actingAs($this->fixtures['accountantA'])
            ->get(route('reconciliation.index'))
            ->assertOk()
            ->assertSee('Bank Reconciliation');
    }

    public function test_wrong_deposit_can_be_edited_or_deleted(): void
    {
        $tenant = $this->fixtures['tenantA'];
        $schoolId = $this->fixtures['schoolA']->id;
        $service = app(BankReconciliationService::class);

        $deposit = $service->recordDeposit($schoolId, $this->fixtures['accountantA'], now()->toDateString(), 9999, 'WRONG');

        $this->actingAs($this->fixtures['accountantA'])
            ->put(route('reconciliation.deposits.update', $deposit), [
                'deposit_date' => now()->toDateString(),
                'amount'       => 5000,
                'reference'    => 'FIXED',
                'description'  => 'Corrected amount',
            ])
            ->assertRedirect();

        $deposit->refresh();
        $this->assertSame(5000.0, (float) $deposit->amount);
        $this->assertSame('FIXED', $deposit->reference);

        $this->actingAs($this->fixtures['accountantA'])
            ->delete(route('reconciliation.deposits.destroy', $deposit))
            ->assertRedirect(route('reconciliation.index'));

        $this->assertNull(BankDeposit::find($deposit->id));
    }
}
