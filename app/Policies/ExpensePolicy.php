<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;
use App\Policies\Concerns\ChecksSchoolAccess;

class ExpensePolicy
{
    use ChecksSchoolAccess;

    public function viewAny(User $user): bool
    {
        return $this->isFinanceUser($user);
    }

    public function view(User $user, Expense $expense): bool
    {
        return $this->isFinanceUser($user)
            && $this->belongsToSameSchool($user, $expense->school_id);
    }

    public function create(User $user): bool
    {
        return $this->isFinanceUser($user);
    }

    public function update(User $user, Expense $expense): bool
    {
        return $this->belongsToSameSchool($user, $expense->school_id)
            && $user->id === $expense->created_by;
    }

    public function delete(User $user, Expense $expense): bool
    {
        return $this->update($user, $expense);
    }

    public function restore(User $user, Expense $expense): bool
    {
        return $this->isFinanceUser($user)
            && $this->belongsToSameSchool($user, $expense->school_id);
    }
}
