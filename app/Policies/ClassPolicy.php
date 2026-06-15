<?php

namespace App\Policies;

use App\Models\Classes;
use App\Models\User;
use App\Policies\Concerns\ChecksSchoolAccess;

class ClassPolicy
{
    use ChecksSchoolAccess;

    public function viewAny(User $user): bool
    {
        return $this->isFinanceUser($user);
    }

    public function create(User $user): bool
    {
        return $this->isFinanceUser($user);
    }

    public function update(User $user, Classes $class): bool
    {
        return $this->isFinanceUser($user)
            && $this->belongsToSameSchool($user, $class->school_id);
    }

    public function delete(User $user, Classes $class): bool
    {
        return $this->update($user, $class);
    }
}
