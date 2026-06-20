<?php

namespace App\Http\Controllers;

use App\Models\FeeReminderSetting;
use App\Services\OverdueSmsReminderService;
use App\Support\TenantFilters;
use Illuminate\Http\Request;

class FeeReminderController extends Controller
{
    public function edit()
    {
        $schoolId = TenantFilters::schoolId();
        $setting = FeeReminderSetting::forSchoolOrDefault($schoolId);

        return view('reminders.settings', compact('setting'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'enabled'               => ['nullable', 'boolean'],
            'min_days_outstanding'  => ['required', 'integer', 'min:0', 'max:365'],
            'reminder_interval_days'=> ['required', 'integer', 'min:1', 'max:90'],
            'current_term_only'     => ['nullable', 'boolean'],
        ]);

        $schoolId = TenantFilters::schoolId();
        $setting = FeeReminderSetting::forSchoolOrDefault($schoolId);

        $setting->update([
            'enabled'                => $request->boolean('enabled'),
            'min_days_outstanding'   => (int) $validated['min_days_outstanding'],
            'reminder_interval_days' => (int) $validated['reminder_interval_days'],
            'current_term_only'      => $request->boolean('current_term_only'),
        ]);

        return back()->with('success', 'Fee reminder settings saved.');
    }

    public function runNow(OverdueSmsReminderService $service)
    {
        $schoolId = TenantFilters::schoolId();
        $setting = FeeReminderSetting::forSchoolOrDefault($schoolId);

        if (! $setting->enabled) {
            return back()->with('error', 'Enable automated reminders before sending.');
        }

        $stats = $service->runForSchool($setting);

        return back()->with(
            'success',
            "Overdue reminders queued: {$stats['queued']} sent, {$stats['skipped']} skipped."
        );
    }
}
