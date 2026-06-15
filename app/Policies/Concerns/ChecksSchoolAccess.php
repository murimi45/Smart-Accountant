<?php

namespace App\Policies\Concerns;

use App\Models\User;

trait ChecksSchoolAccess
{
    protected function belongsToSameSchool(User $user, int|string|null $schoolId): bool
    {
        if ($schoolId === null || $user->school_id === null) {
            return false;
        }

        return (int) $user->school_id === (int) $schoolId;
    }

    protected function isAdmin(User $user): bool
    {
        return strtolower((string) $user->role) === 'admin';
    }

    protected function isFinanceUser(User $user): bool
    {
        return in_array(strtolower((string) $user->role), ['admin', 'accountant'], true);
    }
}
