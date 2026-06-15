<?php

namespace App\Policies;

use App\Models\StudentExtraFee;
use App\Models\User;
use App\Policies\Concerns\ChecksSchoolAccess;

class StudentExtraFeePolicy
{
    use ChecksSchoolAccess;

    public function viewAny(User $user): bool
    {
        return $this->isFinanceUser($user);
    }

    public function view(User $user, StudentExtraFee $assignment): bool
    {
        return $this->isFinanceUser($user)
            && $this->belongsToSameSchool($user, $assignment->school_id);
    }

    public function update(User $user, StudentExtraFee $assignment): bool
    {
        return $this->view($user, $assignment);
    }

    public function delete(User $user, StudentExtraFee $assignment): bool
    {
        return $this->view($user, $assignment);
    }
}
