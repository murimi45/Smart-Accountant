<?php

namespace App\Policies;

use App\Models\Term;
use App\Models\User;
use App\Policies\Concerns\ChecksSchoolAccess;

class TermPolicy
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

    public function update(User $user, Term $term): bool
    {
        return $this->isFinanceUser($user)
            && $this->belongsToSameSchool($user, $term->school_id);
    }

    public function delete(User $user, Term $term): bool
    {
        return $this->update($user, $term);
    }
}
