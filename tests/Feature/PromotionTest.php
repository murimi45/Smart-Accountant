<?php

namespace Tests\Feature;

use App\Jobs\RunPromotion;
use App\Models\ClassFee;
use App\Models\PromotionRun;
use App\Models\StudentEnrollment;
use App\Services\PromotionService;
use Tests\Support\TenantFixtureBuilder;
use Tests\TestCase;

class PromotionTest extends TestCase
{
    public function test_term_promotion_generates_invoices_without_school_id_error(): void
    {
        $fixtures = TenantFixtureBuilder::createPair();
        $tenant = $fixtures['tenantA'];

        ClassFee::createForSchool($fixtures['schoolA']->id, [
            'class_id' => $tenant['class']->id,
            'term_id'  => $tenant['toTerm']->id,
            'amount'   => 5000,
        ]);

        $run = PromotionRun::createForSchool($fixtures['schoolA']->id, [
            'from_term_id' => $tenant['term']->id,
            'to_term_id'   => $tenant['toTerm']->id,
            'promoted_by'  => $fixtures['adminA']->id,
            'type'         => 'term_promotion',
            'status'       => 'pending',
            'active_key'   => PromotionRun::activeKey(
                $fixtures['schoolA']->id,
                $tenant['term']->id,
                $tenant['toTerm']->id,
                'term_promotion'
            ),
        ]);

        (new RunPromotion(
            $run->id,
            $fixtures['schoolA']->id,
            $tenant['term']->id,
            $tenant['toTerm']->id,
            $fixtures['adminA']->id,
            'term'
        ))->handle(app(PromotionService::class));

        $run->refresh();

        $this->assertSame('completed', $run->status);
        $this->assertNull($run->error_message);

        $this->assertTrue(
            StudentEnrollment::withoutGlobalScopes()
                ->where('school_id', $fixtures['schoolA']->id)
                ->where('term_id', $tenant['toTerm']->id)
                ->whereNotNull('promoted_from_enrollment_id')
                ->exists()
        );
    }

    public function test_term_promotion_is_blocked_when_destination_class_fee_is_missing(): void
    {
        $fixtures = TenantFixtureBuilder::createPair();
        $tenant = $fixtures['tenantA'];

        $response = $this->actingAs($fixtures['adminA'])
            ->from(route('enrollment.index'))
            ->post(route('promotion.term'), [
                'from_term_id' => $tenant['term']->id,
                'to_term_id'   => $tenant['toTerm']->id,
            ]);

        $response->assertRedirect(route('addclassfee'))
            ->assertSessionHas('error');

        $this->assertStringContainsString('Grade 1', $response->getSession()->get('error'));
    }

    public function test_term_promotion_starts_when_destination_class_fee_exists(): void
    {
        $fixtures = TenantFixtureBuilder::createPair();
        $tenant = $fixtures['tenantA'];

        $tenant['promotionRun']->update([
            'status'     => 'failed',
            'active_key' => null,
        ]);

        ClassFee::createForSchool($fixtures['schoolA']->id, [
            'class_id' => $tenant['class']->id,
            'term_id'  => $tenant['toTerm']->id,
            'amount'   => 5000,
        ]);

        $response = $this->actingAs($fixtures['adminA'])
            ->from(route('enrollment.index'))
            ->post(route('promotion.term'), [
                'from_term_id' => $tenant['term']->id,
                'to_term_id'   => $tenant['toTerm']->id,
            ]);

        $run = PromotionRun::withoutGlobalScopes()
            ->where('school_id', $fixtures['schoolA']->id)
            ->where('from_term_id', $tenant['term']->id)
            ->where('to_term_id', $tenant['toTerm']->id)
            ->where('type', 'term_promotion')
            ->whereNotNull('active_key')
            ->latest('id')
            ->first();

        $this->assertNotNull($run);
        $response->assertRedirect(route('promotion.progress', $run->id));
    }
}
