<?php

namespace App\Policies;

use App\Models\Stream;
use App\Models\User;
use App\Policies\Concerns\ChecksSchoolAccess;

class StreamPolicy
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

    public function update(User $user, Stream $stream): bool
    {
        $stream->loadMissing('class');

        return $this->isFinanceUser($user)
            && $this->belongsToSameSchool($user, $stream->class?->school_id);
    }

    public function delete(User $user, Stream $stream): bool
    {
        return $this->update($user, $stream);
    }
}
