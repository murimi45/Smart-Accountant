<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;

class TenantBulkIds
{
    /**
     * Ensure every ID in $ids exists for the current school on $modelClass.
     *
     * @param  class-string<Model>  $modelClass
     */
    public static function assertBelongToSchool(string $modelClass, array $ids, ?int $schoolId = null): void
    {
        $schoolId ??= TenantFilters::schoolId();

        $ids = collect($ids)
            ->filter(fn ($id) => $id !== null && $id !== '')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return;
        }

        $found = $modelClass::forSchool($schoolId)
            ->whereIn('id', $ids)
            ->count();

        if ($found !== $ids->count()) {
            CrossTenantSecurityLog::bulkIdMismatch($modelClass, $ids->all());
            abort(422, 'One or more records do not belong to your school.');
        }
    }
}
