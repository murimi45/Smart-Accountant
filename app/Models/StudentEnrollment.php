<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Scopes\SchoolScope;

class StudentEnrollment extends Model
{
    protected $table = 'student_enrollments';

    protected $fillable = [
        'school_id',
        'student_id',
        'class_id',
        'stream_id',
        'term_id',
        'status',
        'promoted_from_enrollment_id',
        'correction_reason',
        'is_final'
    ];

    protected $casts = [
        'status' => 'string',
        'is_final' => 'boolean'
    ];

    /*
    |--------------------------------------------------------------------------
    | STATUS CONSTANTS
    | Use these everywhere — never hardcode the string values
    |--------------------------------------------------------------------------
    */
    const STATUS_ACTIVE           = 'active';
    const STATUS_REPEATING        = 'repeating';
    const STATUS_INACTIVE         = 'inactive';
    const STATUS_WRONGLY_PROMOTED = 'wrongly_promoted';
    const STATUS_CANCELLED        = 'cancelled';

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function stream(): BelongsTo
    {
        return $this->belongsTo(Stream::class);
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function promotedFrom(): BelongsTo
    {
        return $this->belongsTo(StudentEnrollment::class, 'promoted_from_enrollment_id');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'enrollment_id');
    }

    /*
    |--------------------------------------------------------------------------
    | QUERY SCOPES
    | These are building blocks — the controller composes them as needed
    |--------------------------------------------------------------------------
    */

    /** Only active enrollments (normal promoting students) */
    public function scopeActive(Builder $q): Builder
    {
        return $q->where('status', self::STATUS_ACTIVE);
    }

    /** Only repeating enrollments */
    public function scopeRepeating(Builder $q): Builder
    {
        return $q->where('status', self::STATUS_REPEATING);
    }

    /** Only inactive enrollments */
    public function scopeInactive(Builder $q): Builder
    {
        return $q->where('status', self::STATUS_INACTIVE);
    }

    /** Only flagged wrongly promoted — these show the Correct button */
    public function scopeWronglyPromoted(Builder $q): Builder
    {
        return $q->where('status', self::STATUS_WRONGLY_PROMOTED);
    }

    /** All visible statuses (excludes cancelled — those are historical only) */
    public function scopeVisible(Builder $q): Builder
    {
        return $q->whereIn('status', [
            self::STATUS_ACTIVE,
            self::STATUS_REPEATING,
            self::STATUS_INACTIVE,
            self::STATUS_WRONGLY_PROMOTED,
        ]);
    }

    /** Filter to a specific school */
   

    /** Filter to a specific term */
    public function scopeForTerm(Builder $q, int $termId): Builder
    {
        return $q->where('term_id', $termId);
    }

    /** Filter to a specific class */
    public function scopeForClass(Builder $q, int $classId): Builder
    {
        return $q->where('class_id', $classId);
    }

    /** Filter to a specific status — used when status comes from request input */
    public function scopeForStatus(Builder $q, ?string $status): Builder
    {
        if (!$status) return $q;
        return $q->where('status', $status);
    }

    /** Search by student name or admission number */
    public function scopeSearch(Builder $q, ?string $search): Builder
    {
        if (!$search) return $q;

        return $q->whereHas('student', function (Builder $sq) use ($search) {
            $sq->where('full_name', 'like', '%' . $search . '%')
               ->orWhere('admission', 'like', '%' . $search . '%');
        });
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    | Convenience methods used in the blade and controller
    |--------------------------------------------------------------------------
    */

    /**
     * Returns the class the student will move to after promotion.
     * Repeating → same class. Active → next class by level.
     * Inactive → null (skipped).
     */
    public function nextClass(): ?Classes
    {
        if ($this->status === self::STATUS_INACTIVE) {
            return null;
        }

        if ($this->status === self::STATUS_REPEATING) {
            return $this->schoolClass;
        }

        // Active or wrongly_promoted → find next class by level
        return Classes::where('school_id', $this->school_id)
            ->where('order', ($this->schoolClass->order ?? 0) + 1)
            ->first();
    }

    /**
     * Human-readable label for what will appear in "Next class" column.
     */
    public function nextClassLabel(): string
    {
        if ($this->status === self::STATUS_INACTIVE) {
            return '—';
        }

        $next = $this->nextClass();

        if (!$next) {
            return $this->schoolClass?->is_final ? 'Graduating' : '—';
        }

        if ($this->status === self::STATUS_REPEATING) {
            $streamName = $this->stream?->name ?? '';
            return $next->name . $streamName . ' (same)';
        }

        // Carry forward the stream name
        $streamName = $this->stream?->name ?? '';
        return $next->name . $streamName;
    }

    /**
     * Whether this enrollment needs a Correct button shown.
     */
    public function needsCorrection(): bool
    {
        return $this->status === self::STATUS_WRONGLY_PROMOTED;
    }
}