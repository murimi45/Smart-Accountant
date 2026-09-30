<?php

namespace App\Policies;

use App\Models\SalaryGrade;
use App\Models\User;
use App\Policies\Concerns\ChecksSchoolAccess;

class SalaryGradePolicy
{
    use ChecksSchoolAccess;

    public function viewAny(User $user): bool
    {
        return $this->isFinanceUser($user);
    }

    public function view(User $user, SalaryGrade $salaryGrade): bool
    {
        return $this->isFinanceUser($user)
            && $this->belongsToSameSchool($user, $salaryGrade->school_id);
    }

    public function create(User $user): bool
    {
        return $this->isFinanceUser($user);
    }

    public function update(User $user, SalaryGrade $salaryGrade): bool
    {
        return $this->view($user, $salaryGrade);
    }

    public function delete(User $user, SalaryGrade $salaryGrade): bool
    {
        return $this->update($user, $salaryGrade);
    }
}