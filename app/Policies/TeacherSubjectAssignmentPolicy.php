<?php

namespace App\Policies;

use App\Models\TeacherSubjectAssignment;
use App\Models\User;
use App\Policies\Concerns\ChecksSchoolAccess;

class TeacherSubjectAssignmentPolicy
{
    use ChecksSchoolAccess;

    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function delete(User $user, TeacherSubjectAssignment $assignment): bool
    {
        return $this->isAdmin($user)
            && $this->belongsToSameSchool($user, $assignment->school_id);
    }
}
