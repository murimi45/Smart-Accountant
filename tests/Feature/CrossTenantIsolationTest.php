<?php

namespace Tests\Feature;

use App\Models\StudentEnrollment;
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
}
