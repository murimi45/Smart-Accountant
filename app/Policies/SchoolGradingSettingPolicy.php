<?php

namespace App\Policies;

use App\Models\SchoolGradingSetting;
use App\Models\User;
use App\Policies\Concerns\ChecksSchoolAccess;

class SchoolGradingSettingPolicy
{
    use ChecksSchoolAccess;

    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user, SchoolGradingSetting $setting): bool
    {
        return $this->isAdmin($user)
            && $this->belongsToSameSchool($user, $setting->school_id);
    }
}
