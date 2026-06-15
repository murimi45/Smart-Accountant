<?php

namespace App\Policies;

use App\Models\AcademicYear;
use App\Models\User;
use App\Policies\Concerns\ChecksSchoolAccess;

class AcademicYearPolicy
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

    public function update(User $user, AcademicYear $academicYear): bool
    {
        return $this->isFinanceUser($user)
            && $this->belongsToSameSchool($user, $academicYear->school_id);
    }

    public function delete(User $user, AcademicYear $academicYear): bool
    {
        return $this->update($user, $academicYear);
    }
}
