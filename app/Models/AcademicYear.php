<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\SchoolScope;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id', 'name', 'is_current', 'start_date', 'end_date'
    ];

    protected static function booted()
    {
        static::addGlobalScope(new SchoolScope);
    }

    // ── Relationships ────────────────────────────────────────────
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function terms()
    {
        return $this->hasMany(Term::class)->orderBy('term_number');
    }

    // ── Scopes ───────────────────────────────────────────────────
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    // ── Static helpers ───────────────────────────────────────────
    public static function current()
    {
        return self::where('is_current', true)->first();
    }

    /**
     * Academic years strictly after the given year (by start_date).
     */
    public static function after(int $schoolId, ?self $current)
    {
        $query = self::where('school_id', $schoolId);

        if ($current?->start_date) {
            $query->where('start_date', '>', $current->start_date);
        }

        return $query->orderBy('start_date');
    }

    /**
     * The immediate next academic year after $current.
     */
    public static function nextAfter(int $schoolId, ?self $current): ?self
    {
        if (! $current) {
            return null;
        }

        return self::after($schoolId, $current)->first();
    }
}