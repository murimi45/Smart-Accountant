<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Scopes\SchoolScope;

class Term extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'name',
        'academic_year_id',
        'term_number',
        'start_date',
        'end_date',
        'active',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new SchoolScope);

        static::creating(function ($model) {
        if (auth()->check()) {
            $model->school_id = auth()->user()->school_id;
        }
    });
    }

    // ── Relationships ────────────────────────────────────────────
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function classfees()
    {
        return $this->hasMany(ClassFee::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function otherIncomes()
    {
        return $this->hasMany(OtherIncome::class);
    }

    // ── Static helpers ───────────────────────────────────────────
    public static function getRecord()
    {
        return self::all();
    }

    public static function getSingle($id)
    {
        return self::find($id);
    }

    public static function current()
    {
        if (auth()->check() && auth()->user()->school_id) {
            return self::current1(auth()->user()->school_id);
        }

        return self::with('academicYear')
            ->where('active', true)
            ->orderByDesc('start_date')
            ->first();
    }

  /**
     * The school's current operational term.
     * 1. Term explicitly flagged active (most recent start if multiple)
     * 2. Term whose date range includes today
     * 3. Most recent term that has already started
     * 4. Latest term by start date (last resort)
     */
    public static function current1($schoolId)
    {
        if (! $schoolId) {
            return null;
        }

        $active = self::with('academicYear')
            ->where('school_id', $schoolId)
            ->where('active', true)
            ->orderByDesc('start_date')
            ->first();

        if ($active) {
            return $active;
        }

        $today = now()->toDateString();

        $inProgress = self::with('academicYear')
            ->where('school_id', $schoolId)
            ->where('start_date', '<=', $today)
            ->where(function ($q) use ($today) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', $today);
            })
            ->orderByDesc('start_date')
            ->first();

        if ($inProgress) {
            return $inProgress;
        }

        return self::with('academicYear')
            ->where('school_id', $schoolId)
            ->where('start_date', '<=', $today)
            ->orderByDesc('start_date')
            ->first()
            ?? self::with('academicYear')
                ->where('school_id', $schoolId)
                ->orderByDesc('start_date')
                ->first();
    }

    public static function currentId()
    {
        return optional(self::current())->id;
    }

    /**
     * Terms for a school ordered by start_date (promotion sequence).
     */
    public static function orderedForSchool(int $schoolId)
    {
        return self::with('academicYear')
            ->where('school_id', $schoolId)
            ->orderBy('start_date')
            ->get();
    }

    /**
     * Next term in the same academic year (term_number + 1, later start_date).
     * Use for term-to-term promotion only — not cross-year.
     */
    public static function nextInYear(int $schoolId, ?self $from): ?self
    {
        if (! $from) {
            return null;
        }

        return self::with('academicYear')
            ->where('school_id', $schoolId)
            ->where('academic_year_id', $from->academic_year_id)
            ->where('term_number', $from->term_number + 1)
            ->where('start_date', '>', $from->start_date)
            ->first();
    }

    /**
     * @deprecated Use nextInYear() for promotion. Alias for backward compatibility.
     */
    public static function nextAfter(int $schoolId, ?self $from): ?self
    {
        return self::nextInYear($schoolId, $from);
    }

    /**
     * Terms for one academic year, ordered by term_number.
     */
    public static function orderedForAcademicYear(int $schoolId, int $academicYearId)
    {
        return self::with('academicYear')
            ->where('school_id', $schoolId)
            ->where('academic_year_id', $academicYearId)
            ->orderBy('term_number')
            ->get();
    }

    // Year now comes from academic year relationship
    public function getYearAttribute(): ?string
    {
        return $this->academicYear?->name;
    }

    public static function currentYear()
    {
        $term = self::with('academicYear')->current();
        return $term ? $term->academicYear->name : now()->year;
    }
}