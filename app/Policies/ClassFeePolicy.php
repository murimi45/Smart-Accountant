<?php

namespace App\Policies;

use App\Models\ClassFee;
use App\Models\User;
use App\Policies\Concerns\ChecksSchoolAccess;

class ClassFeePolicy
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

    public function update(User $user, ClassFee $classFee): bool
    {
        return $this->isFinanceUser($user)
            && $this->belongsToSameSchool($user, $classFee->school_id);
    }

    public function delete(User $user, ClassFee $classFee): bool
    {
        return $this->update($user, $classFee);
    }
}
