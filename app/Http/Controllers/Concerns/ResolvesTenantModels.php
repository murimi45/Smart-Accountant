<?php

namespace App\Http\Controllers\Concerns;

use App\Models\User;

trait ResolvesTenantModels
{
    protected function schoolId(): int
    {
        return (int) auth()->user()->school_id;
    }

    protected function authorizeSchoolUser(User $user, ?array $roles = null): void
    {
        if ((int) $user->school_id !== $this->schoolId()) {
            abort(404);
        }

        if ($roles !== null) {
            $allowed = array_map('strtolower', $roles);
            if (! in_array(strtolower((string) $user->role), $allowed, true)) {
                abort(404);
            }
        }
    }
}
