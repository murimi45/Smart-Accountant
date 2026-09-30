<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;
use App\Policies\Concerns\ChecksSchoolAccess;

class EmployeePolicy
{
    use ChecksSchoolAccess;

    public function viewAny(User $user): bool
    {
        return $this->isFinanceUser($user);
    }

    public function view(User $user, Employee $employee): bool
    {
        return $this->isFinanceUser($user)
            && $this->belongsToSameSchool($user, $employee->school_id);
    }

    public function create(User $user): bool
    {
        return $this->isFinanceUser($user);
    }

    public function update(User $user, Employee $employee): bool
    {
        return $this->view($user, $employee);
    }
}