<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\BankDeposit;
use App\Models\BankReconciliationMatch;
use App\Models\CashbookEntry;
use App\Models\ClassFee;
use App\Models\FeeReminderSetting;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\InvoiceWaiver;
use App\Models\PromotionRun;
use App\Models\SmsLog;
use App\Models\StudentEnrollment;
use App\Models\User;
use App\Jobs\SendSmsJob;
use App\Services\BankReconciliationService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Tests\Support\TenantFixtureBuilder;
use Tests\TestCase;

class CrossTenantIsolationTest extends TestCase
{
    private array $fixtures;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixtures = TenantFixtureBuilder::createPair();
    }

    public function test_parallel_tenants_have_independent_records(): void
    {
        $a = $this->fixtures['tenantA'];
        $b = $this->fixtures['tenantB'];

        $this->assertNotEquals($a['student']->school_id, $b['student']->school_id);
        $this->assertNotEquals($a['enrollment']->id, $b['enrollment']->id);
        // Parallel fixture: same business ordinals (class order, term number), different tenants.
        $this->assertSame($a['class']->order, $b['class']->order);
        $this->assertSame($a['term']->term_number, $b['term']->term_number);
        $this->assertNotSame($a['class']->school_id, $b['class']->school_id);
    }

    public function test_cross_tenant_enrollment_correction_get_is_blocked(): void
    {
        $enrollmentB = $this->fixtures['tenantB']['enrollment'];

        $response = $this->actingAs($this->fixtures['adminA'])
            ->getJson(route('enrollment.show-correction', $enrollmentB->id));

        $this->assertBlockedCrossTenant($response);
    }

    public function test_cross_tenant_enrollment_status_post_is_blocked(): void
    {
        $enrollmentB = $this->fixtures['tenantB']['enrollment'];

        $response = $this->actingAs($this->fixtures['adminA'])
            ->post(route('enrollment.update-status', $enrollmentB->id), [
                'status' => StudentEnrollment::STATUS_INACTIVE,
            ]);

        $this->assertBlockedCrossTenant($response);

        $enrollmentB->refresh();
        $this->assertSame(StudentEnrollment::STATUS_ACTIVE, $enrollmentB->status);
    }

    public function test_cross_tenant_invoice_payment_is_blocked(): void
    {
        $invoiceB = $this->fixtures['tenantB']['invoice'];

        $response = $this->actingAs($this->fixtures['accountantA'])
            ->post(route('payments.store', $invoiceB), [
                'amount' => 100,
                'method' => 'cash',
            ]);

        $this->assertBlockedCrossTenant($response);
    }

    public function test_cross_tenant_user_admin_edit_is_blocked(): void
    {
        $staffB = $this->fixtures['tenantB']['staffUser'];

        $response = $this->actingAs($this->fixtures['adminA'])
            ->get(route('admins.edit', $staffB));

        $this->assertBlockedCrossTenant($response);
    }

    public function test_cross_tenant_user_admin_update_is_blocked(): void
    {
        $staffB = $this->fixtures['tenantB']['staffUser'];

        $response = $this->actingAs($this->fixtures['adminA'])
            ->put(route('admins.update', $staffB), [
                'admin_name' => 'Hijacked Admin',
                'email'      => 'hijacked-admin@test.local',
                'role'       => 'admin',
            ]);

        $this->assertBlockedCrossTenant($response);
        $this->assertDatabaseHas('users', [
            'id'         => $staffB->id,
            'admin_name' => $staffB->admin_name,
            'email'      => "staff-{$this->fixtures['schoolB']->id}@probe.test",
        ]);
    }

    public function test_cross_tenant_user_admin_delete_is_blocked(): void
    {
        $staffB = $this->fixtures['tenantB']['staffUser'];

        $response = $this->actingAs($this->fixtures['adminA'])
            ->delete(route('admins.destroy', $staffB));

        $this->assertBlockedCrossTenant($response);
        $this->assertDatabaseHas('users', ['id' => $staffB->id]);
    }

    public function test_cross_tenant_accountant_edit_is_blocked(): void
    {
        $accountantB = $this->fixtures['accountantB'];

        $response = $this->actingAs($this->fixtures['adminA'])
            ->get(route('accountants.edit', $accountantB));

        $this->assertBlockedCrossTenant($response);
    }

    public function test_cross_tenant_accountant_update_is_blocked(): void
    {
        $accountantB = $this->fixtures['accountantB'];

        $response = $this->actingAs($this->fixtures['adminA'])
            ->put(route('accountants.update', $accountantB), [
                'admin_name' => 'Hijacked Accountant',
                'email'      => 'hijacked-acct@test.local',
            ]);

        $this->assertBlockedCrossTenant($response);
        $this->assertDatabaseHas('users', [
            'id'         => $accountantB->id,
            'admin_name' => $accountantB->admin_name,
            'email'      => 'acct-b@test.local',
        ]);
    }

    public function test_cross_tenant_accountant_delete_is_blocked(): void
    {
        $accountantB = $this->fixtures['accountantB'];

        $response = $this->actingAs($this->fixtures['adminA'])
            ->delete(route('accountants.destroy', $accountantB));

        $this->assertBlockedCrossTenant($response);
        $this->assertDatabaseHas('users', ['id' => $accountantB->id]);
    }

    public function test_cross_tenant_stream_delete_is_blocked(): void
    {
        $streamB = $this->fixtures['tenantB']['stream'];

        $response = $this->actingAs($this->fixtures['adminA'])
            ->delete(route('streams.destroy', $streamB));

        $this->assertBlockedCrossTenant($response);

        $this->assertDatabaseHas('streams', ['id' => $streamB->id]);
    }

    public function test_cross_tenant_extra_fee_assignment_edit_is_blocked(): void
    {
        $assignmentB = $this->fixtures['tenantB']['assignment'];

        $response = $this->actingAs($this->fixtures['adminA'])
            ->get(route('editassignedextrafee', $assignmentB->id));

        $this->assertBlockedCrossTenant($response);
    }

    public function test_cross_tenant_extra_fee_assignment_update_is_blocked(): void
    {
        $assignmentB = $this->fixtures['tenantB']['assignment'];
        $extraFeeB = $this->fixtures['tenantB']['extraFee'];

        $response = $this->actingAs($this->fixtures['adminA'])
            ->post(route('updateassignedextrafee', $assignmentB->id), [
                'extra_fee_id' => $extraFeeB->id,
                'students' => [
                    $this->fixtures['tenantB']['student']->id => [
                        'student_id' => $this->fixtures['tenantB']['student']->id,
                        'selected'   => '1',
                        'quantity'   => 2,
                    ],
                ],
            ]);

        $this->assertBlockedCrossTenant($response);
    }

    public function test_enrollment_index_with_other_school_term_id_is_blocked(): void
    {
        $termB = $this->fixtures['tenantB']['term'];

        $response = $this->actingAs($this->fixtures['adminA'])
            ->get(route('enrollment.index', ['term_id' => $termB->id]));

        $this->assertBlockedCrossTenant($response);
    }

    public function test_promotion_run_poll_other_school_run_is_blocked(): void
    {
        $runB = $this->fixtures['tenantB']['promotionRun'];

        $response = $this->actingAs($this->fixtures['adminA'])
            ->getJson(route('promotion.poll', $runB->id));

        $this->assertBlockedCrossTenant($response);
    }

    public function test_scoped_validation_rejects_other_school_class_id(): void
    {
        $classB = $this->fixtures['tenantB']['class'];
        $termA = $this->fixtures['tenantA']['term'];

        $response = $this->actingAs($this->fixtures['adminA'])
            ->from(route('addStudents'))
            ->post(route('insertStudents'), [
                'name'      => 'Cross Tenant Student',
                'phone'     => '0700999888',
                'admission' => 'CROSS-001',
                'gender'    => 'male',
                'class_id'  => $classB->id,
                'term_id'   => $termA->id,
            ]);

        $response->assertSessionHasErrors('class_id');
        $this->assertDatabaseMissing('students', ['admission' => 'CROSS-001']);
    }

    public function test_same_tenant_enrollment_correction_succeeds(): void
    {
        $enrollmentA = $this->fixtures['tenantA']['enrollment'];

        $response = $this->actingAs($this->fixtures['adminA'])
            ->getJson(route('enrollment.show-correction', $enrollmentA->id));

        $response->assertOk();
        $response->assertJsonPath('enrollment.id', $enrollmentA->id);
    }

    public function test_same_tenant_invoice_payment_authorization_passes_route_layer(): void
    {
        $invoiceA = $this->fixtures['tenantA']['invoice'];

        $response = $this->actingAs($this->fixtures['accountantA'])
            ->post(route('payments.store', $invoiceA), [
                'amount' => 100,
                'method' => 'cash',
            ]);

        // May redirect with success or business-rule error — not 403/404.
        $this->assertNotContains($response->status(), [403, 404]);
    }

    public function test_cross_tenant_promotion_term_start_is_blocked(): void
    {
        $termB = $this->fixtures['tenantB']['term'];
        $toTermB = $this->fixtures['tenantB']['toTerm'];

        $beforeCount = PromotionRun::where('school_id', $this->fixtures['schoolA']->id)->count();

        $response = $this->actingAs($this->fixtures['adminA'])
            ->from(route('enrollment.index'))
            ->post(route('promotion.term'), [
                'from_term_id' => $termB->id,
                'to_term_id'   => $toTermB->id,
            ]);

        $this->assertBlockedCrossTenant($response);
        $this->assertSame(
            $beforeCount,
            PromotionRun::where('school_id', $this->fixtures['schoolA']->id)->count()
        );
    }

    public function test_cross_tenant_expense_category_update_is_blocked(): void
    {
        $categoryB = $this->fixtures['tenantB']['expenseCategory'];

        $response = $this->actingAs($this->fixtures['adminA'])
            ->put(route('expense_categories.update', $categoryB->id), [
                'name'        => 'Hijacked',
                'description' => 'Cross-tenant attempt',
            ]);

        $this->assertBlockedCrossTenant($response);
        $this->assertDatabaseHas('expense_categories', [
            'id'   => $categoryB->id,
            'name' => 'Supplies',
        ]);
    }

    public function test_cross_tenant_expense_update_is_blocked(): void
    {
        $expenseB = $this->fixtures['tenantB']['expense'];
        $termA    = $this->fixtures['tenantA']['term'];

        $response = $this->actingAs($this->fixtures['accountantA'])
            ->put(route('expenses.update', $expenseB->id), [
                'expense_category_id' => $this->fixtures['tenantA']['expenseCategory']->id,
                'description'         => 'Hijacked',
                'amount'              => 9999,
                'payment_method'      => 'cash',
                'expense_date'        => $termA->start_date,
                'term_id'             => $termA->id,
                'year'                => 2026,
            ]);

        $this->assertBlockedCrossTenant($response);
        $this->assertDatabaseHas('expenses', [
            'id'          => $expenseB->id,
            'description' => 'Stationery',
            'amount'      => 1500,
        ]);
    }

    public function test_cross_tenant_expense_delete_is_blocked(): void
    {
        $expenseB = $this->fixtures['tenantB']['expense'];

        $response = $this->actingAs($this->fixtures['accountantA'])
            ->delete(route('expenses.destroy', $expenseB->id));

        $this->assertBlockedCrossTenant($response);
        $this->assertDatabaseHas('expenses', [
            'id'         => $expenseB->id,
            'deleted_at' => null,
        ]);
    }

    public function test_cross_tenant_income_category_update_is_blocked(): void
    {
        $categoryB = $this->fixtures['tenantB']['incomeCategory'];

        $response = $this->actingAs($this->fixtures['adminA'])
            ->put(route('income_categories.update', $categoryB->id), [
                'name'        => 'Hijacked',
                'description' => 'Cross-tenant attempt',
            ]);

        $this->assertBlockedCrossTenant($response);
        $this->assertDatabaseHas('income_categories', [
            'id'   => $categoryB->id,
            'name' => 'Donations',
        ]);
    }

    public function test_cross_tenant_other_income_update_is_blocked(): void
    {
        $otherIncomeB = $this->fixtures['tenantB']['otherIncome'];
        $termA        = $this->fixtures['tenantA']['term'];

        $response = $this->actingAs($this->fixtures['accountantA'])
            ->put(route('other_incomes.update', $otherIncomeB->id), [
                'income_category_id' => $this->fixtures['tenantA']['incomeCategory']->id,
                'description'        => 'Hijacked',
                'amount'             => 9999,
                'payment_method'     => 'cash',
                'income_date'        => $termA->start_date,
                'term_id'            => $termA->id,
            ]);

        $this->assertBlockedCrossTenant($response);
        $this->assertDatabaseHas('other_incomes', [
            'id'          => $otherIncomeB->id,
            'description' => 'Parent contribution',
            'amount'      => 2500,
        ]);
    }

    public function test_cross_tenant_other_income_delete_is_blocked(): void
    {
        $otherIncomeB = $this->fixtures['tenantB']['otherIncome'];

        $response = $this->actingAs($this->fixtures['accountantA'])
            ->delete(route('other_incomes.destroy', $otherIncomeB->id));

        $this->assertBlockedCrossTenant($response);
        $this->assertDatabaseHas('other_incomes', [
            'id'         => $otherIncomeB->id,
            'deleted_at' => null,
        ]);
    }

    public function test_cross_tenant_payment_channel_update_is_blocked(): void
    {
        $channelB = $this->fixtures['tenantB']['paymentChannel'];

        $response = $this->actingAs($this->fixtures['adminA'])
            ->put(route('payment_channels.update', $channelB->id), [
                'type'            => 'paybill',
                'identifier'      => 'HIJACKED',
                'account_pattern' => null,
            ]);

        $this->assertBlockedCrossTenant($response);
        $this->assertDatabaseHas('payment_channels', [
            'id'         => $channelB->id,
            'identifier' => 'PB'.$this->fixtures['schoolB']->id,
        ]);
    }

    public function test_bulk_export_with_other_school_term_id_is_blocked(): void
    {
        $termB = $this->fixtures['tenantB']['term'];

        $response = $this->actingAs($this->fixtures['adminA'])
            ->get(route('bulk.export', ['type' => 'students', 'term_id' => $termB->id]));

        $this->assertBlockedCrossTenant($response);
    }

    public function test_bulk_student_import_does_not_create_student_under_other_school(): void
    {
        $schoolB = $this->fixtures['schoolB'];
        $uniqueClassName = 'Foreign Class '.$schoolB->id;

        \App\Models\Classes::createForSchool($schoolB->id, [
            'name'  => $uniqueClassName,
            'order' => 99,
        ]);

        $content = "admission,full_name,phone,guardian_name,gender,class,term,stream\n";
        $content .= "XTENxBULK,Cross Tenant Bulk,0700111222,Guardian,male,{$uniqueClassName},Term 1 - 2026,\n";

        $file = UploadedFile::fake()->createWithContent('students.csv', $content);

        $response = $this->actingAs($this->fixtures['adminA'])
            ->post(route('bulk.import', 'students'), ['file' => $file]);

        $response->assertRedirect();
        $response->assertSessionHas('import_errors');

        $this->assertDatabaseMissing('students', [
            'school_id' => $schoolB->id,
            'admission' => 'XTENxBULK',
        ]);
        $this->assertDatabaseMissing('students', [
            'school_id' => $this->fixtures['schoolA']->id,
            'admission' => 'XTENxBULK',
        ]);
    }

    public function test_bulk_opening_balance_import_rejects_other_school_admission(): void
    {
        $admissionB = $this->fixtures['tenantB']['student']->admission;

        $content = "admission,term,opening_balance,notes\n";
        $content .= "{$admissionB},Term 1 - 2026,4500,Cross-tenant attempt\n";

        $file = UploadedFile::fake()->createWithContent('opening.csv', $content);

        $response = $this->actingAs($this->fixtures['adminA'])
            ->post(route('bulk.import', 'opening_balances'), ['file' => $file]);

        $response->assertRedirect();
        $response->assertSessionHas('import_errors');

        $this->assertDatabaseHas('invoices', [
            'id'                       => $this->fixtures['tenantB']['invoice']->id,
            'imported_opening_balance' => 0,
        ]);
    }

    public function test_bulk_payment_import_rejects_other_school_admission(): void
    {
        $invoiceB = $this->fixtures['tenantB']['invoice'];
        $admissionB = $this->fixtures['tenantB']['student']->admission;

        $content = "admission,amount,method,payment_date,term\n";
        $content .= "{$admissionB},5000,Cash,2026-01-15,Term 1 - 2026\n";

        $file = UploadedFile::fake()->createWithContent('payments.csv', $content);

        $response = $this->actingAs($this->fixtures['accountantA'])
            ->post(route('bulk.import', 'payments'), ['file' => $file]);

        $response->assertRedirect();
        $response->assertSessionHas('import_errors');

        $invoiceB->refresh();
        $this->assertSame(0.0, (float) $invoiceB->amount_paid);
        $this->assertSame(0, InvoicePayment::where('invoice_id', $invoiceB->id)->count());
    }

    public function test_bulk_class_fee_import_does_not_touch_other_school(): void
    {
        $schoolB = $this->fixtures['schoolB'];
        $uniqueClassName = 'Foreign Fee Class '.$schoolB->id;

        \App\Models\Classes::createForSchool($schoolB->id, [
            'name'  => $uniqueClassName,
            'order' => 98,
        ]);

        $foreignClass = \App\Models\Classes::withoutGlobalScopes()
            ->where('school_id', $schoolB->id)
            ->where('name', $uniqueClassName)
            ->first();

        $content = "class,term,amount,description,status\n";
        $content .= "{$uniqueClassName},Term 1 - 2026,25000,Cross-tenant fee,active\n";

        $file = UploadedFile::fake()->createWithContent('fees.csv', $content);

        $beforeCount = ClassFee::withoutGlobalScopes()
            ->where('school_id', $schoolB->id)
            ->count();

        $response = $this->actingAs($this->fixtures['adminA'])
            ->post(route('bulk.import', 'fees'), ['file' => $file]);

        $response->assertRedirect();
        $response->assertSessionHas('import_errors');

        $this->assertSame(
            $beforeCount,
            ClassFee::withoutGlobalScopes()->where('school_id', $schoolB->id)->count()
        );
        $this->assertFalse(
            ClassFee::withoutGlobalScopes()
                ->where('school_id', $schoolB->id)
                ->where('class_id', $foreignClass->id)
                ->where('amount', 25000)
                ->exists()
        );
    }

    public function test_enrollment_bulk_status_with_other_school_ids_is_blocked(): void
    {
        $enrollmentB = $this->fixtures['tenantB']['enrollment'];

        $response = $this->actingAs($this->fixtures['adminA'])
            ->post(route('enrollment.bulk-status'), [
                'statuses' => [
                    $enrollmentB->id => StudentEnrollment::STATUS_INACTIVE,
                ],
            ]);

        $this->assertBlockedCrossTenant($response);

        $enrollmentB->refresh();
        $this->assertSame(StudentEnrollment::STATUS_ACTIVE, $enrollmentB->status);
    }

    public function test_cross_tenant_bank_deposit_update_is_blocked(): void
    {
        $depositB = $this->fixtures['tenantB']['bankDeposit'];

        $response = $this->actingAs($this->fixtures['accountantA'])
            ->put(route('reconciliation.deposits.update', $depositB), [
                'deposit_date' => now()->toDateString(),
                'amount'       => 1,
                'reference'    => 'HIJACKED',
                'description'  => 'Cross-tenant attempt',
            ]);

        $this->assertBlockedCrossTenant($response);
        $this->assertDatabaseHas('bank_deposits', [
            'id'        => $depositB->id,
            'reference' => 'DEP-'.$this->fixtures['schoolB']->id,
            'amount'    => 5000,
        ]);
    }

    public function test_cross_tenant_bank_deposit_delete_is_blocked(): void
    {
        $depositB = $this->fixtures['tenantB']['bankDeposit'];

        $response = $this->actingAs($this->fixtures['accountantA'])
            ->delete(route('reconciliation.deposits.destroy', $depositB));

        $this->assertBlockedCrossTenant($response);
        $this->assertDatabaseHas('bank_deposits', ['id' => $depositB->id]);
    }

    public function test_cross_tenant_bank_match_is_blocked(): void
    {
        $depositB = $this->fixtures['tenantB']['bankDeposit'];
        $schoolAId = (int) $this->fixtures['schoolA']->id;

        $payment = InvoicePayment::create([
            'invoice_id'   => $this->fixtures['tenantA']['invoice']->id,
            'amount'       => 1000,
            'method'       => 'Bank',
            'payment_date' => now()->toDateString(),
        ]);

        $entry = CashbookEntry::createForSchool($schoolAId, [
            'transaction_type' => 'inflow',
            'entry_type'       => 'original',
            'amount'           => 1000,
            'payment_method'   => 'Bank',
            'transaction_date' => now()->toDateString(),
            'description'      => 'School A payment inflow',
            'source_type'      => InvoicePayment::class,
            'source_id'        => $payment->id,
        ]);

        $response = $this->actingAs($this->fixtures['accountantA'])
            ->from(route('reconciliation.index'))
            ->post(route('reconciliation.match'), [
                'bank_deposit_id'   => $depositB->id,
                'cashbook_entry_id' => $entry->id,
            ]);

        $this->assertTrue(
            in_array($response->status(), [403, 404, 422], true)
            || ($response->status() === 302 && (
                $response->getSession()->has('errors')
                || $response->getSession()->has('error')
            )),
            'Expected cross-tenant match to be blocked, got '.$response->status()
        );

        $this->assertSame(0, BankReconciliationMatch::where('bank_deposit_id', $depositB->id)->count());
        $this->assertDatabaseHas('bank_deposits', [
            'id'     => $depositB->id,
            'status' => 'unmatched',
        ]);
    }

    public function test_cross_tenant_bank_unmatch_is_blocked(): void
    {
        $tenantB = $this->fixtures['tenantB'];
        $schoolBId = (int) $this->fixtures['schoolB']->id;
        $depositB = $tenantB['bankDeposit'];

        $payment = InvoicePayment::create([
            'invoice_id'   => $tenantB['invoice']->id,
            'amount'       => 5000,
            'method'       => 'Bank',
            'payment_date' => now()->toDateString(),
        ]);

        $entry = CashbookEntry::where('source_type', InvoicePayment::class)
            ->where('source_id', $payment->id)
            ->first();

        $this->assertNotNull($entry);

        $service = app(BankReconciliationService::class);
        $match = $service->match($schoolBId, $this->fixtures['accountantB'], $depositB->id, $entry->id);

        $response = $this->actingAs($this->fixtures['accountantA'])
            ->delete(route('reconciliation.unmatch', $match));

        $this->assertBlockedCrossTenant($response);
        $this->assertDatabaseHas('bank_reconciliation_matches', ['id' => $match->id]);
        $depositB->refresh();
        $this->assertSame(BankDeposit::STATUS_RECONCILED, $depositB->status);
    }

    public function test_cross_tenant_waiver_store_is_blocked(): void
    {
        $invoiceB = $this->fixtures['tenantB']['invoice'];

        $response = $this->actingAs($this->fixtures['accountantA'])
            ->post(route('waivers.store', $invoiceB), [
                'scope'         => 'invoice',
                'discount_type' => 'fixed',
                'value'         => 1000,
                'reason'        => 'Cross-tenant waiver attempt',
            ]);

        $this->assertBlockedCrossTenant($response);
        $this->assertDatabaseMissing('invoice_waivers', [
            'invoice_id' => $invoiceB->id,
            'reason'     => 'Cross-tenant waiver attempt',
        ]);
    }

    public function test_cross_tenant_waiver_approve_is_blocked(): void
    {
        $waiverB = $this->fixtures['tenantB']['invoiceWaiver'];

        $response = $this->actingAs($this->fixtures['adminA'])
            ->post(route('waivers.approve', $waiverB));

        $this->assertBlockedCrossTenant($response);
        $this->assertDatabaseHas('invoice_waivers', [
            'id'     => $waiverB->id,
            'status' => InvoiceWaiver::STATUS_PENDING,
        ]);
    }

    public function test_cross_tenant_waiver_reject_is_blocked(): void
    {
        $waiverB = $this->fixtures['tenantB']['invoiceWaiver'];

        $response = $this->actingAs($this->fixtures['adminA'])
            ->post(route('waivers.reject', $waiverB), [
                'review_notes' => 'Cross-tenant rejection attempt',
            ]);

        $this->assertBlockedCrossTenant($response);
        $this->assertDatabaseHas('invoice_waivers', [
            'id'     => $waiverB->id,
            'status' => InvoiceWaiver::STATUS_PENDING,
        ]);
    }

    public function test_user_without_school_id_cannot_access_tenant_routes(): void
    {
        $platform = User::unguarded(fn () => User::create([
            'school_id'          => null,
            'admin_name'         => 'Platform',
            'email'              => 'platform@probe.test',
            'password'           => bcrypt('password'),
            'role'               => User::ROLE_PLATFORM,
            'two_factor_enabled' => false,
        ]));

        $response = $this->actingAs($platform)->get(route('dashboard'));

        $response->assertForbidden();
    }

    public function test_dashboard_with_other_school_term_id_is_blocked(): void
    {
        $termB = $this->fixtures['tenantB']['term'];

        $response = $this->actingAs($this->fixtures['adminA'])
            ->get(route('dashboard', ['view' => 'term', 'term_id' => $termB->id]));

        $this->assertBlockedCrossTenant($response);
    }

    public function test_dashboard_with_other_school_academic_year_id_is_blocked(): void
    {
        $yearB = $this->fixtures['tenantB']['year'];

        $response = $this->actingAs($this->fixtures['adminA'])
            ->get(route('dashboard', ['view' => 'annual', 'academic_year_id' => $yearB->id]));

        $this->assertBlockedCrossTenant($response);
    }

    public function test_ledger_index_with_other_school_account_id_is_blocked(): void
    {
        $accountB = Account::withoutGlobalScopes()
            ->where('school_id', $this->fixtures['schoolB']->id)
            ->first();

        $this->assertNotNull($accountB);

        $response = $this->actingAs($this->fixtures['accountantA'])
            ->get(route('ledger.index', ['account_id' => $accountB->id]));

        $this->assertBlockedCrossTenant($response);
    }

    public function test_cashbook_index_does_not_expose_other_school_entries(): void
    {
        $probe = 'XTENANT_CASHBOOK_PROBE_'.$this->fixtures['schoolB']->id;

        CashbookEntry::createForSchool($this->fixtures['schoolB']->id, [
            'transaction_type' => 'inflow',
            'entry_type'       => 'original',
            'amount'           => 12345,
            'payment_method'   => 'Cash',
            'transaction_date' => now()->toDateString(),
            'description'      => $probe,
        ]);

        $response = $this->actingAs($this->fixtures['accountantA'])
            ->get(route('cashbook.index'));

        $response->assertOk();
        $response->assertDontSee($probe, false);
    }

    public function test_accounts_index_does_not_expose_other_school_accounts(): void
    {
        $probe = 'XTENANT_ACCOUNT_PROBE_'.$this->fixtures['schoolB']->id;

        Account::createForSchool($this->fixtures['schoolB']->id, [
            'name'           => $probe,
            'category'       => 'asset',
            'normal_balance' => 'debit',
            'is_default'     => false,
        ]);

        $response = $this->actingAs($this->fixtures['accountantA'])
            ->get(route('accounts.index'));

        $response->assertOk();
        $response->assertDontSee($probe, false);
    }

    public function test_fee_reminder_update_does_not_change_other_school_settings(): void
    {
        $schoolB = $this->fixtures['schoolB'];
        $settingB = FeeReminderSetting::createForSchool($schoolB->id, [
            'enabled'                => false,
            'min_days_outstanding'   => 99,
            'reminder_interval_days' => 7,
            'current_term_only'      => true,
        ]);

        $this->actingAs($this->fixtures['adminA'])
            ->put(route('reminders.update'), [
                'enabled'                => '1',
                'min_days_outstanding'   => 14,
                'reminder_interval_days' => 10,
                'current_term_only'      => '1',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $settingB->refresh();
        $this->assertSame(99, $settingB->min_days_outstanding);
    }

    public function test_fee_reminder_run_does_not_queue_sms_for_other_school(): void
    {
        Bus::fake([SendSmsJob::class]);

        $schoolA = $this->fixtures['schoolA'];
        $schoolB = $this->fixtures['schoolB'];

        Invoice::withoutGlobalScopes()
            ->whereIn('id', [
                $this->fixtures['tenantA']['invoice']->id,
                $this->fixtures['tenantB']['invoice']->id,
            ])
            ->update(['invoice_date' => now()->subDays(10)->toDateString()]);

        FeeReminderSetting::createForSchool($schoolA->id, [
            'enabled'                => true,
            'min_days_outstanding'   => 7,
            'reminder_interval_days' => 7,
            'current_term_only'      => true,
        ]);

        FeeReminderSetting::createForSchool($schoolB->id, [
            'enabled'                => true,
            'min_days_outstanding'   => 7,
            'reminder_interval_days' => 7,
            'current_term_only'      => true,
        ]);

        $this->actingAs($this->fixtures['adminA'])
            ->post(route('reminders.run'))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertTrue(
            SmsLog::withoutGlobalScopes()
                ->where('school_id', $schoolA->id)
                ->where('invoice_id', $this->fixtures['tenantA']['invoice']->id)
                ->exists()
        );

        $this->assertFalse(
            SmsLog::withoutGlobalScopes()
                ->where('school_id', $schoolB->id)
                ->where('invoice_id', $this->fixtures['tenantB']['invoice']->id)
                ->exists()
        );
    }
}
