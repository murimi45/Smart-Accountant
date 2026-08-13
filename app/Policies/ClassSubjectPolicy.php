<?php

namespace App\Policies;

use App\Models\ClassSubject;
use App\Models\User;
use App\Policies\Concerns\ChecksSchoolAccess;

class ClassSubjectPolicy
{
    use ChecksSchoolAccess;

    public function viewAny(User $user): bool
    {
        return $this->isGradingUser($user);
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user, ClassSubject $classSubject): bool
    {
        return $this->isAdmin($user)
            && $this->belongsToSameSchool($user, $classSubject->school_id);
    }

    public function delete(User $user, ClassSubject $classSubject): bool
    {
        return $this->update($user, $classSubject);
    }
}
