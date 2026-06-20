<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\CashbookEntry;
use App\Models\InvoicePayment;
use App\Models\InvoicePaymentReversal;
use App\Models\LedgerEntry;
use Tests\Support\TenantFixtureBuilder;
use Tests\TestCase;

class LedgerPostingTest extends TestCase
{
    private array $fixtures;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixtures = TenantFixtureBuilder::createPair();
    }

    public function test_fee_payment_creates_balanced_ledger_lines(): void
    {
        $tenant = $this->fixtures['tenantA'];
        $invoice = $tenant['invoice'];
        $accountant = $this->fixtures['accountantA'];
        $schoolId = (int) $this->fixtures['schoolA']->id;

        $this->actingAs($accountant)
            ->post(route('payments.store', $invoice), [
                'amount' => 5000,
                'method' => 'Cash',
            ])
            ->assertRedirect(route('invoices.index'));

        $payment = InvoicePayment::first();
        $this->assertNotNull($payment);

        $cashbook = CashbookEntry::where('source_type', InvoicePayment::class)
            ->where('source_id', $payment->id)
            ->first();
        $this->assertNotNull($cashbook);

        $lines = LedgerEntry::withoutGlobalScopes()
            ->where('cashbook_entry_id', $cashbook->id)
            ->with('account')
            ->get();

        $this->assertCount(2, $lines);
        $this->assertSame(5000.0, (float) $lines->sum('debit'));
        $this->assertSame(5000.0, (float) $lines->sum('credit'));

        $debitAccount = $lines->firstWhere('debit', '>', 0)?->account?->name;
        $creditAccount = $lines->firstWhere('credit', '>', 0)?->account?->name;

        $this->assertSame('Cash at Hand', $debitAccount);
        $this->assertSame('Tuition Fees Income', $creditAccount);

        $this->assertSame(
            25,
            Account::withoutGlobalScopes()->where('school_id', $schoolId)->where('is_default', true)->count()
        );
    }

    public function test_mpesa_payment_posts_to_cash_at_bank(): void
    {
        $tenant = $this->fixtures['tenantA'];
        $invoice = $tenant['invoice'];
        $accountant = $this->fixtures['accountantA'];

        $this->actingAs($accountant)
            ->post(route('payments.store', $invoice), [
                'amount' => 2000,
                'method' => 'Mpesa',
            ]);

        $payment = InvoicePayment::first();
        $cashbook = CashbookEntry::where('source_id', $payment->id)->first();

        $debitLine = LedgerEntry::withoutGlobalScopes()
            ->where('cashbook_entry_id', $cashbook->id)
            ->where('debit', '>', 0)
            ->with('account')
            ->first();

        $this->assertSame('Cash at Bank', $debitLine->account->name);
    }

    public function test_payment_reversal_creates_opposite_ledger_posting(): void
    {
        $tenant = $this->fixtures['tenantA'];
        $invoice = $tenant['invoice'];
        $accountant = $this->fixtures['accountantA'];

        $this->actingAs($accountant)
            ->post(route('payments.store', $invoice), [
                'amount' => 3000,
                'method' => 'Cash',
            ]);

        $payment = InvoicePayment::first();

        $this->actingAs($accountant)
            ->post(route('payments.reverse', [$invoice, $payment]), [
                'reason' => 'Posted in error during data entry',
            ]);

        $reversal = InvoicePaymentReversal::first();
        $cashbook = CashbookEntry::where('source_type', InvoicePaymentReversal::class)
            ->where('source_id', $reversal->id)
            ->first();

        $lines = LedgerEntry::withoutGlobalScopes()
            ->where('cashbook_entry_id', $cashbook->id)
            ->with('account')
            ->get();

        $this->assertCount(2, $lines);
        $this->assertSame(3000.0, (float) $lines->sum('debit'));
        $this->assertSame(3000.0, (float) $lines->sum('credit'));

        $debitAccount = $lines->firstWhere('debit', '>', 0)?->account?->name;
        $creditAccount = $lines->firstWhere('credit', '>', 0)?->account?->name;

        $this->assertSame('Tuition Fees Income', $debitAccount);
        $this->assertSame('Cash at Hand', $creditAccount);
    }
}
