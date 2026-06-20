<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\FinanceAuditLogService;
use App\Support\FinanceAudit;
use App\Support\TenantFilters;
use Illuminate\Http\Request;

class FinanceAuditLogController extends Controller
{
    public function index(Request $request, FinanceAuditLogService $service)
    {
        $schoolId = TenantFilters::schoolId();

        $logs = $service->paginateForSchool(
            $schoolId,
            $request->filled('event') ? $request->string('event')->toString() : null,
            $request->filled('user_id') ? (int) $request->user_id : null,
            $request->filled('search') ? $request->string('search')->toString() : null,
        );

        $users = User::query()
            ->where('school_id', $schoolId)
            ->whereIn('role', ['admin', 'accountant', 'Admin', 'Accountant'])
            ->orderBy('admin_name')
            ->get(['id', 'admin_name', 'email']);

        $eventLabels = FinanceAudit::EVENT_LABELS;

        return view('audit.index', compact('logs', 'users', 'eventLabels'));
    }
}
