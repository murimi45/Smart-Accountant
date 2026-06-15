<?php

namespace App\Policies;

use App\Models\IncomeCategory;
use App\Models\User;
use App\Policies\Concerns\ChecksSchoolAccess;

class IncomeCategoryPolicy
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

    public function update(User $user, IncomeCategory $category): bool
    {
        return $this->isFinanceUser($user)
            && $this->belongsToSameSchool($user, $category->school_id);
    }

    public function delete(User $user, IncomeCategory $category): bool
    {
        return $this->update($user, $category);
    }
}
