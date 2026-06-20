<?php

namespace Tests\Feature;

use App\Jobs\SendSmsJob;
use App\Models\FeeReminderSetting;
use App\Models\Invoice;
use App\Models\SmsLog;
use App\Services\OverdueSmsReminderService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Bus;
use Tests\Support\TenantFixtureBuilder;
use Tests\TestCase;

class OverdueSmsReminderTest extends TestCase
{
    private array $fixtures;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixtures = TenantFixtureBuilder::createPair();
    }

    public function test_overdue_reminder_queues_sms_for_eligible_invoice(): void
    {
        Bus::fake([SendSmsJob::class]);

        $tenant = $this->fixtures['tenantA'];
        $schoolId = $this->fixtures['schoolA']->id;

        Invoice::withoutGlobalScopes()
            ->whereKey($tenant['invoice']->id)
            ->update(['invoice_date' => now()->subDays(10)->toDateString()]);

        FeeReminderSetting::createForSchool($schoolId, [
            'enabled' => true,
            'min_days_outstanding' => 7,
            'reminder_interval_days' => 7,
            'current_term_only' => true,
        ]);

        $stats = app(OverdueSmsReminderService::class)->runForSchool(
            FeeReminderSetting::forSchoolOrDefault($schoolId),
            Carbon::today()
        );

        $this->assertSame(1, $stats['queued']);

        $log = SmsLog::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('invoice_id', $tenant['invoice']->id)
            ->where('source', OverdueSmsReminderService::SOURCE_OVERDUE_AUTO)
            ->first();

        $this->assertNotNull($log);
        $this->assertStringContainsString('overdue', strtolower($log->message));

        Bus::assertDispatched(SendSmsJob::class);
    }

    public function test_recent_reminder_is_not_sent_again(): void
    {
        Bus::fake([SendSmsJob::class]);

        $tenant = $this->fixtures['tenantA'];
        $schoolId = $this->fixtures['schoolA']->id;

        Invoice::withoutGlobalScopes()
            ->whereKey($tenant['invoice']->id)
            ->update(['invoice_date' => now()->subDays(10)->toDateString()]);

        FeeReminderSetting::createForSchool($schoolId, [
            'enabled' => true,
            'min_days_outstanding' => 7,
            'reminder_interval_days' => 7,
            'current_term_only' => true,
        ]);

        SmsLog::create([
            'school_id'  => $schoolId,
            'to'         => '+254711111111',
            'message'    => 'Prior reminder',
            'status'     => 'sent',
            'source'     => OverdueSmsReminderService::SOURCE_OVERDUE_AUTO,
            'student_id' => $tenant['student']->id,
            'invoice_id' => $tenant['invoice']->id,
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDays(2),
        ]);

        $stats = app(OverdueSmsReminderService::class)->runForSchool(
            FeeReminderSetting::forSchoolOrDefault($schoolId)
        );

        $this->assertSame(0, $stats['queued']);
        Bus::assertNothingDispatched();
    }

    public function test_admin_can_save_reminder_settings(): void
    {
        $this->actingAs($this->fixtures['adminA'])
            ->put(route('reminders.update'), [
                'enabled' => '1',
                'min_days_outstanding' => 14,
                'reminder_interval_days' => 10,
                'current_term_only' => '1',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $setting = FeeReminderSetting::withoutGlobalScopes()
            ->where('school_id', $this->fixtures['schoolA']->id)
            ->first();

        $this->assertTrue($setting->enabled);
        $this->assertSame(14, $setting->min_days_outstanding);
        $this->assertSame(10, $setting->reminder_interval_days);
    }
}
