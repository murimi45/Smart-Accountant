<?php

namespace App\Http\Controllers;

use App\Models\SmsLog;
use App\Support\TenantFilters;

class SmsLogController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', SmsLog::class);

        $logs = SmsLog::forSchool(TenantFilters::schoolId())
            ->latest()
            ->paginate(50);

        return view('sms.logs', compact('logs'));
    }
}
