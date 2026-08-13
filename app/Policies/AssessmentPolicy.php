<?php

namespace App\Policies;

use App\Models\Assessment;
use App\Models\User;
use App\Modules\Grading\Services\RosterService;
use App\Policies\Concerns\ChecksSchoolAccess;

class AssessmentPolicy
{
    use ChecksSchoolAccess;

    public function viewAny(User $user): bool
    {
        return $this->isGradingUser($user);
    }

    public function view(User $user, Assessment $assessment): bool
    {
        if (! $this->isGradingUser($user) || ! $this->belongsToSameSchool($user, $assessment->school_id)) {
            return false;
        }

        if ($this->isAdmin($user)) {
            return true;
        }

        return app(RosterService::class)->teacherCanAccess(
            $user,
            (int) $assessment->class_id,
            (int) $assessment->subject_id,
            $assessment->stream_id ? (int) $assessment->stream_id : null,
            (int) $assessment->term_id
        );
    }

    public function create(User $user): bool
    {
        return $this->isGradingUser($user);
    }

    public function update(User $user, Assessment $assessment): bool
    {
        return $this->view($user, $assessment);
    }

    public function delete(User $user, Assessment $assessment): bool
    {
        return $this->isAdmin($user)
            && $this->belongsToSameSchool($user, $assessment->school_id);
    }

    public function enterMarks(User $user, Assessment $assessment): bool
    {
        return $this->view($user, $assessment) && $assessment->isOpen();
    }
}
