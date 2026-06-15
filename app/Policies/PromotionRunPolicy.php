<?php

namespace App\Policies;

use App\Models\PromotionRun;
use App\Models\User;
use App\Policies\Concerns\ChecksSchoolAccess;

class PromotionRunPolicy
{
    use ChecksSchoolAccess;

    public function view(User $user, PromotionRun $run): bool
    {
        return $this->isAdmin($user)
            && $this->belongsToSameSchool($user, $run->school_id);
    }
}
