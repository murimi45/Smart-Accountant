<?php

namespace App\Policies;

use App\Models\BankReconciliationMatch;
use App\Models\User;
use App\Policies\Concerns\ChecksSchoolAccess;

class BankReconciliationMatchPolicy
{
    use ChecksSchoolAccess;

    public function create(User $user): bool
    {
        return $this->isFinanceUser($user);
    }

    public function delete(User $user, BankReconciliationMatch $match): bool
    {
        return $this->isFinanceUser($user)
            && $this->belongsToSameSchool($user, $match->school_id);
    }
}
