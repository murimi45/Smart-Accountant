<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;
use App\Policies\Concerns\ChecksSchoolAccess;

class StudentPolicy
{
    use ChecksSchoolAccess;

    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function view(User $user, Student $student): bool
    {
        return $this->isFinanceUser($user)
            && $this->belongsToSameSchool($user, $student->school_id);
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user, Student $student): bool
    {
        return $this->isAdmin($user)
            && $this->belongsToSameSchool($user, $student->school_id);
    }

    public function delete(User $user, Student $student): bool
    {
        return $this->update($user, $student);
    }
}
