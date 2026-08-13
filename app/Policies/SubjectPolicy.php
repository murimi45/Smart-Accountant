<?php

namespace App\Policies;

use App\Models\Subject;
use App\Models\User;
use App\Policies\Concerns\ChecksSchoolAccess;

class SubjectPolicy
{
    use ChecksSchoolAccess;

    public function viewAny(User $user): bool
    {
        return $this->isGradingUser($user);
    }

    public function view(User $user, Subject $subject): bool
    {
        return $this->isGradingUser($user)
            && $this->belongsToSameSchool($user, $subject->school_id);
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user, Subject $subject): bool
    {
        return $this->isAdmin($user)
            && $this->belongsToSameSchool($user, $subject->school_id);
    }

    public function delete(User $user, Subject $subject): bool
    {
        return $this->update($user, $subject);
    }
}
