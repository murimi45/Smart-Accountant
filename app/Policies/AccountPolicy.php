<?php

namespace App\Policies;

use App\Models\Account;
use App\Models\User;
use App\Policies\Concerns\ChecksSchoolAccess;

class AccountPolicy
{
    use ChecksSchoolAccess;

    public function viewAny(User $user): bool
    {
        return $this->isFinanceUser($user);
    }

    public function view(User $user, Account $account): bool
    {
        return $this->isFinanceUser($user)
            && $this->belongsToSameSchool($user, $account->school_id);
    }
}
