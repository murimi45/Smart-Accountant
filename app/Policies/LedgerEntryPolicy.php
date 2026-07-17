<?php

namespace App\Policies;

use App\Models\LedgerEntry;
use App\Models\User;
use App\Policies\Concerns\ChecksSchoolAccess;

class LedgerEntryPolicy
{
    use ChecksSchoolAccess;

    public function viewAny(User $user): bool
    {
        return $this->isFinanceUser($user);
    }

    public function view(User $user, LedgerEntry $entry): bool
    {
        return $this->isFinanceUser($user)
            && $this->belongsToSameSchool($user, $entry->school_id);
    }
}
