<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;
use App\Policies\Concerns\ChecksSchoolAccess;

class TransactionPolicy
{
    use ChecksSchoolAccess;

    public function viewAny(User $user): bool
    {
        return $this->isFinanceUser($user);
    }

    public function view(User $user, Transaction $transaction): bool
    {
        return $this->isFinanceUser($user)
            && $this->belongsToSameSchool($user, $transaction->school_id);
    }
}
