<?php

namespace App\Modules\Grading\Services;

use App\Models\StudentEnrollment;
use App\Models\TeacherSubjectAssignment;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class RosterService
{
    public function enrollmentsFor(int $schoolId, int $termId, int $classId, ?int $streamId = null): Collection
    {
        $query = StudentEnrollment::withoutGlobalScopes()
            ->with('student')
            ->where('school_id', $schoolId)
            ->where('term_id', $termId)
            ->where('class_id', $classId)
            ->whereIn('status', [
                StudentEnrollment::STATUS_ACTIVE,
                StudentEnrollment::STATUS_REPEATING,
            ]);

        if ($streamId) {
            $query->where('stream_id', $streamId);
        }

        return $query->orderBy('id')->get();
    }

    public function teacherCanAccess(User $user, int $classId, int $subjectId, ?int $streamId = null, ?int $termId = null, ?int $yearId = null): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if (! $user->isTeacher() || ! $user->school_id) {
            return false;
        }

        $query = TeacherSubjectAssignment::withoutGlobalScopes()
            ->where('school_id', $user->school_id)
            ->where('user_id', $user->id)
            ->where('class_id', $classId)
            ->where('subject_id', $subjectId);

        if ($streamId) {
            $query->where(function ($q) use ($streamId) {
                $q->whereNull('stream_id')->orWhere('stream_id', $streamId);
            });
        }

        if ($termId) {
            $query->where(function ($q) use ($termId) {
                $q->whereNull('term_id')->orWhere('term_id', $termId);
            });
        }

        if ($yearId) {
            $query->where('academic_year_id', $yearId);
        }

        return $query->exists();
    }
}
