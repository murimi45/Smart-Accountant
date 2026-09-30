<?php

namespace App\Policies;

use App\Models\EmployeeDeduction;
use App\Models\User;
use App\Policies\Concerns\ChecksSchoolAccess;

class EmployeeDeductionPolicy
{
    use ChecksSchoolAccess;

    public function viewAny(User $user): bool
    {
        return $this->isFinanceUser($user);
    }

    public function view(User $user, EmployeeDeduction $deduction): bool
    {
        return $this->isFinanceUser($user)
            && $this->belongsToSameSchool($user, $deduction->school_id);
    }

    public function create(User $user): bool
    {
        return $this->isFinanceUser($user);
    }

    public function update(User $user, EmployeeDeduction $deduction): bool
    {
        return $this->view($user, $deduction);
    }
}