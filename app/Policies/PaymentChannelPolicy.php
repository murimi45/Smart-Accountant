<?php

namespace App\Policies;

use App\Models\PaymentChannel;
use App\Models\User;
use App\Policies\Concerns\ChecksSchoolAccess;

class PaymentChannelPolicy
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

    public function update(User $user, PaymentChannel $channel): bool
    {
        return $this->isFinanceUser($user)
            && $this->belongsToSameSchool($user, $channel->school_id);
    }
}
