<?php

namespace App\Policies;

use App\Models\ExtraFee;
use App\Models\User;
use App\Policies\Concerns\ChecksSchoolAccess;

class ExtraFeePolicy
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

    public function update(User $user, ExtraFee $extraFee): bool
    {
        return $this->isFinanceUser($user)
            && $this->belongsToSameSchool($user, $extraFee->school_id);
    }

    public function delete(User $user, ExtraFee $extraFee): bool
    {
        return $this->update($user, $extraFee);
    }
}
