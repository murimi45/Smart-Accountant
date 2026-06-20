<?php

namespace App\Services;

use App\Support\FinanceAudit;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Spatie\Activitylog\Models\Activity;

class FinanceAuditLogService
{
    /** @return list<string> */
    public function eventOptions(): array
    {
        return array_keys(FinanceAudit::EVENT_LABELS);
    }

    public function paginateForSchool(
        int $schoolId,
        ?string $event = null,
        ?int $userId = null,
        ?string $search = null,
        int $perPage = 50
    ): LengthAwarePaginator {
        $query = Activity::query()
            ->where('log_name', FinanceAudit::LOG_NAME)
            ->where('school_id', $schoolId)
            ->with(['causer'])
            ->latest();

        if ($event) {
            $query->where('event', $event);
        }

        if ($userId) {
            $query->where('causer_type', \App\Models\User::class)
                ->where('causer_id', $userId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', '%'.$search.'%')
                    ->orWhere('properties', 'like', '%'.$search.'%');
            });
        }

        return $query->paginate($perPage)->withQueryString();
    }
}
