<?php

namespace App\Policies;

use App\Models\Budget;
use App\Models\User;
use App\Policies\Concerns\ChecksSchoolAccess;

class BudgetPolicy
{
    use ChecksSchoolAccess;

    public function viewAny(User $user): bool
    {
        return $this->isFinanceUser($user);
    }

    public function update(User $user): bool
    {
        return $this->isFinanceUser($user);
    }

    public function delete(User $user, Budget $budget): bool
    {
        return $this->isFinanceUser($user)
            && $this->belongsToSameSchool($user, $budget->school_id);
    }
}
