<?php

namespace App\Policies;

use App\Models\TermReportCard;
use App\Models\User;
use App\Policies\Concerns\ChecksSchoolAccess;

class TermReportCardPolicy
{
    use ChecksSchoolAccess;

    public function viewAny(User $user): bool
    {
        return $this->isGradingUser($user);
    }

    public function view(User $user, TermReportCard $card): bool
    {
        return $this->isGradingUser($user)
            && $this->belongsToSameSchool($user, $card->school_id);
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function publish(User $user, TermReportCard $card): bool
    {
        return $this->isAdmin($user)
            && $this->belongsToSameSchool($user, $card->school_id);
    }

    public function unpublish(User $user, TermReportCard $card): bool
    {
        return $this->publish($user, $card);
    }
}
