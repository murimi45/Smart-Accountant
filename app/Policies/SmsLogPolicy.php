<?php

namespace App\Policies;

use App\Models\SmsLog;
use App\Models\User;
use App\Policies\Concerns\ChecksSchoolAccess;

class SmsLogPolicy
{
    use ChecksSchoolAccess;

    public function viewAny(User $user): bool
    {
        return $this->isFinanceUser($user);
    }

    public function view(User $user, SmsLog $log): bool
    {
        return $this->isFinanceUser($user)
            && $this->belongsToSameSchool($user, $log->school_id);
    }
}
