<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubjectTermResult extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'student_enrollment_id',
        'subject_id',
        'term_id',
        'total_percent',
        'letter_grade',
        'band_code',
        'gpa_points',
        'class_rank',
        'teacher_comment',
        'locked_at',
    ];

    protected function casts(): array
    {
        return [
            'total_percent' => 'decimal:2',
            'gpa_points' => 'decimal:2',
            'locked_at' => 'datetime',
        ];
    }

    public function isLocked(): bool
    {
        return $this->locked_at !== null;
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(StudentEnrollment::class, 'student_enrollment_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }
}
