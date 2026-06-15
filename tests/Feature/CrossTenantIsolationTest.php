<?php

namespace Tests\Feature;

use App\Models\PromotionRun;
use App\Models\StudentEnrollment;
use App\Models\User;
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
}
