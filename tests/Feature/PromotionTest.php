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
}
