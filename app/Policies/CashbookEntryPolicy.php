<?php

namespace App\Policies;

use App\Models\CashbookEntry;
use App\Models\User;
use App\Policies\Concerns\ChecksSchoolAccess;

class CashbookEntryPolicy
{
    use ChecksSchoolAccess;

    public function viewAny(User $user): bool
    {
        return $this->isFinanceUser($user);
    }

    public function view(User $user, CashbookEntry $entry): bool
    {
        return $this->isFinanceUser($user)
            && $this->belongsToSameSchool($user, $entry->school_id);
    }
}
