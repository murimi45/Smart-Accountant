<?php

namespace App\Policies;

use App\Models\BankDeposit;
use App\Models\User;
use App\Policies\Concerns\ChecksSchoolAccess;

class BankDepositPolicy
{
    use ChecksSchoolAccess;

    public function viewAny(User $user): bool
    {
        return $this->isFinanceUser($user);
    }

    public function view(User $user, BankDeposit $deposit): bool
    {
        return $this->isFinanceUser($user)
            && $this->belongsToSameSchool($user, $deposit->school_id);
    }

    public function create(User $user): bool
    {
        return $this->isFinanceUser($user);
    }

    public function update(User $user, BankDeposit $deposit): bool
    {
        return $this->view($user, $deposit);
    }

    public function delete(User $user, BankDeposit $deposit): bool
    {
        return $this->view($user, $deposit);
    }
}
