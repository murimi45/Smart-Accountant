<?php

namespace App\Policies;

use App\Models\User;
use App\Policies\Concerns\ChecksSchoolAccess;

class UserPolicy
{
    use ChecksSchoolAccess;

    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user);
    }

    /** Admin managing admin or accountant users in the same school. */
    public function update(User $user, User $model): bool
    {
        return $this->isAdmin($user)
            && $this->belongsToSameSchool($user, $model->school_id)
            && in_array(strtolower((string) $model->role), ['admin', 'accountant'], true);
    }

    public function delete(User $user, User $model): bool
    {
        return $this->update($user, $model);
    }

    /** Admin managing an accountant in the same school. */
    public function manageAccountant(User $user, User $model): bool
    {
        return $this->isAdmin($user)
            && $this->belongsToSameSchool($user, $model->school_id)
            && strtolower((string) $model->role) === 'accountant';
    }
}
