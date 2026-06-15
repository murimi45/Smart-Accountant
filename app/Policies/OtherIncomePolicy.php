<?php

namespace App\Policies;

use App\Models\OtherIncome;
use App\Models\User;
use App\Policies\Concerns\ChecksSchoolAccess;

class OtherIncomePolicy
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

    public function update(User $user, OtherIncome $income): bool
    {
        return $this->isFinanceUser($user)
            && $this->belongsToSameSchool($user, $income->school_id);
    }

    public function delete(User $user, OtherIncome $income): bool
    {
        return $this->update($user, $income);
    }
}
