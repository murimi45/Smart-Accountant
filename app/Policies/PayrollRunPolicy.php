<?php

namespace App\Policies;

use App\Models\PayrollRun;
use App\Models\User;
use App\Policies\Concerns\ChecksSchoolAccess;

class PayrollRunPolicy
{
    use ChecksSchoolAccess;

    public function viewAny(User $user): bool
    {
        return $this->isFinanceUser($user);
    }

    public function view(User $user, PayrollRun $run): bool
    {
        return $this->isFinanceUser($user)
            && $this->belongsToSameSchool($user, $run->school_id);
    }

    public function create(User $user): bool
    {
        return $this->isFinanceUser($user);
    }

    public function update(User $user, PayrollRun $run): bool
    {
        return $this->view($user, $run);
    }
}
