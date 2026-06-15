<?php

namespace App\Policies;

use App\Models\StudentEnrollment;
use App\Models\User;
use App\Policies\Concerns\ChecksSchoolAccess;

class StudentEnrollmentPolicy
{
    use ChecksSchoolAccess;

    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function view(User $user, StudentEnrollment $enrollment): bool
    {
        return $this->isAdmin($user)
            && $this->belongsToSameSchool($user, $enrollment->school_id);
    }

    public function update(User $user, StudentEnrollment $enrollment): bool
    {
        return $this->view($user, $enrollment);
    }
}
