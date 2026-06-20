<?php

namespace Tests\Feature;

use App\Models\CashbookEntry;
use App\Models\ClassFee;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\InvoicePaymentReversal;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Services\InvoiceService;
use Illuminate\Database\Eloquent\Model;
use Tests\Support\TenantFixtureBuilder;
use Tests\TestCase;

class PaymentReversalTest extends TestCase
{
    private array $fixtures;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixtures = TenantFixtureBuilder::createPair();
    }

    public function test_full_reversal_updates_invoice_and_creates_cashbook_reversal(): void
    {
        $tenant = $this->fixtures['tenantA'];
        $invoice = $tenant['invoice'];
        $accountant = $this->fixtures['accountantA'];

        $this->actingAs($accountant)
            ->post(route('payments.store', $invoice), [
                'amount' => 4000,
                'method' => 'Cash',
            ])
            ->assertRedirect(route('invoices.index'));

        $payment = InvoicePayment::first();
        $this->assertNotNull($payment);

        $originalInflow = CashbookEntry::where('source_type', InvoicePayment::class)
            ->where('source_id', $payment->id)
            ->where('entry_type', 'original')
            ->first();
        $this->assertNotNull($originalInflow);

        $response = $this->actingAs($accountant)
            ->post(route('payments.reverse', [$invoice, $payment]), [
                'reason' => 'Duplicate payment entry recorded in error',
            ]);

        $response->assertRedirect(route('invoices.index'));

        $invoice->refresh();
        $this->assertSame(0.0, (float) $invoice->amount_paid);
        $this->assertSame(Invoice::STATUS_UNPAID, $invoice->status);
        $this->assertSame(10000.0, (float) $invoice->balance);

        $reversal = InvoicePaymentReversal::first();
        $this->assertNotNull($reversal);
        $this->assertSame(4000.0, (float) $reversal->amount);
        $this->assertSame('Duplicate payment entry recorded in error', $reversal->reason);

        $cashbookReversal = CashbookEntry::where('source_type', InvoicePaymentReversal::class)
            ->where('source_id', $reversal->id)
            ->where('entry_type', 'reversal')
            ->first();

        $this->assertNotNull($cashbookReversal);
        $this->assertSame('outflow', $cashbookReversal->transaction_type);
        $this->assertSame($originalInflow->id, $cashbookReversal->related_entry_id);
    }

    public function test_partial_reversal_allows_second_reversal_until_fully_reversed(): void
    {
        $tenant = $this->fixtures['tenantA'];
        $invoice = $tenant['invoice'];
        $accountant = $this->fixtures['accountantA'];

        $this->actingAs($accountant)
            ->post(route('payments.store', $invoice), [
                'amount' => 1000,
                'method' => 'Cash',
            ]);

        $payment = InvoicePayment::first();

        $this->actingAs($accountant)
            ->post(route('payments.reverse', [$invoice, $payment]), [
                'amount' => 400,
                'reason' => 'Partial correction first step',
            ])
            ->assertRedirect(route('invoices.index'));

        $invoice->refresh();
        $this->assertSame(600.0, (float) $invoice->amount_paid);

        $this->actingAs($accountant)
            ->post(route('payments.reverse', [$invoice, $payment]), [
                'amount' => 600,
                'reason' => 'Remaining amount reversed',
            ])
            ->assertRedirect(route('invoices.index'));

        $payment->refresh();
        $this->assertTrue($payment->isFullyReversed());
        $this->assertSame(2, InvoicePaymentReversal::count());
    }

    public function test_reversal_requires_reason(): void
    {
        $tenant = $this->fixtures['tenantA'];
        $invoice = $tenant['invoice'];
        $accountant = $this->fixtures['accountantA'];

        $this->actingAs($accountant)
            ->post(route('payments.store', $invoice), [
                'amount' => 500,
                'method' => 'Cash',
            ]);

        $payment = InvoicePayment::first();

        $this->actingAs($accountant)
            ->post(route('payments.reverse', [$invoice, $payment]), [
                'reason' => '',
            ])
            ->assertSessionHasErrors('reason');
    }

    public function test_over_reversal_is_rejected(): void
    {
        $tenant = $this->fixtures['tenantA'];
        $invoice = $tenant['invoice'];
        $accountant = $this->fixtures['accountantA'];

        $this->actingAs($accountant)
            ->post(route('payments.store', $invoice), [
                'amount' => 500,
                'method' => 'Cash',
            ]);

        $payment = InvoicePayment::first();

        $this->actingAs($accountant)
            ->from(route('invoices.index'))
            ->post(route('payments.reverse', [$invoice, $payment]), [
                'amount' => 999,
                'reason' => 'Attempting to reverse too much',
            ])
            ->assertRedirect(route('invoices.index'))
            ->assertSessionHas('error');

        $this->assertSame(0, InvoicePaymentReversal::count());
    }

    public function test_transferred_invoice_reversal_refreshes_current_term_balance_forward(): void
    {
        $tenant = $this->fixtures['tenantA'];
        $school = $this->fixtures['schoolA'];
        $accountant = $this->fixtures['accountantA'];

        Model::withoutEvents(function () use ($tenant, $school) {
            ClassFee::createForSchool($school->id, [
                'class_id' => $tenant['class']->id,
                'term_id'  => $tenant['term']->id,
                'amount'   => 8000,
            ]);

            ClassFee::createForSchool($school->id, [
                'class_id' => $tenant['class']->id,
                'term_id'  => $tenant['toTerm']->id,
                'amount'   => 8000,
            ]);
        });

        $previousInvoice = $tenant['invoice'];
        $previousInvoice->update([
            'total_amount' => 8000,
            'amount_paid'  => 5000,
            'balance'      => 0,
            'status'       => Invoice::STATUS_TRANSFERRED,
        ]);

        $payment = InvoicePayment::create([
            'invoice_id'   => $previousInvoice->id,
            'amount'       => 5000,
            'method'       => 'Cash',
            'payment_date' => now()->toDateString(),
        ]);

        $nextEnrollment = StudentEnrollment::createForSchool($school->id, [
            'student_id'                  => $tenant['student']->id,
            'class_id'                    => $tenant['class']->id,
            'stream_id'                   => $tenant['stream']->id,
            'term_id'                     => $tenant['toTerm']->id,
            'status'                      => StudentEnrollment::STATUS_ACTIVE,
            'promoted_from_enrollment_id' => $tenant['enrollment']->id,
        ]);

        $currentInvoice = app(InvoiceService::class)->createOrUpdateInvoice(
            $school->id,
            Student::withoutGlobalScopes()->find($tenant['student']->id),
            $tenant['toTerm']->id,
            $nextEnrollment->id
        );

        $this->assertNotNull($currentInvoice);
        $bfBefore = $currentInvoice->items()->where('description', 'Balance B/F')->first();
        $this->assertNotNull($bfBefore);
        $this->assertSame(3000.0, (float) $bfBefore->amount);

        app(InvoiceService::class)->reversePayment(
            $payment,
            2000,
            'Payment was recorded against wrong term invoice',
            $accountant->id
        );

        $previousInvoice->refresh();
        $this->assertSame(3000.0, (float) $previousInvoice->amount_paid);
        $this->assertSame(Invoice::STATUS_TRANSFERRED, $previousInvoice->status);

        $currentInvoice->refresh();
        $bfAfter = $currentInvoice->items()->where('description', 'Balance B/F')->first();

        $this->assertNotNull($bfAfter);
        $this->assertSame(5000.0, (float) $bfAfter->amount);
    }

    public function test_cross_tenant_payment_reversal_is_blocked(): void
    {
        $tenantB = $this->fixtures['tenantB'];
        $invoiceB = $tenantB['invoice'];
        $accountantA = $this->fixtures['accountantA'];

        Model::withoutEvents(function () use ($invoiceB) {
            InvoicePayment::create([
                'invoice_id'   => $invoiceB->id,
                'amount'       => 1000,
                'method'       => 'Cash',
                'payment_date' => now()->toDateString(),
            ]);
        });

        $paymentB = InvoicePayment::withoutGlobalScopes()
            ->where('invoice_id', $invoiceB->id)
            ->first();

        $response = $this->actingAs($accountantA)
            ->post(route('payments.reverse', [$invoiceB, $paymentB]), [
                'reason' => 'Should not be allowed cross-tenant',
            ]);

        $this->assertBlockedCrossTenant($response);
        $this->assertSame(0, InvoicePaymentReversal::withoutGlobalScopes()->count());
    }
}
