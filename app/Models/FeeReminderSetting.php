<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class FeeReminderSetting extends Model
{
    use BelongsToSchool;

    public const DEFAULT_MIN_DAYS = 7;

    public const DEFAULT_INTERVAL_DAYS = 7;

    protected $fillable = [
        'school_id',
        'enabled',
        'min_days_outstanding',
        'reminder_interval_days',
        'current_term_only',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'current_term_only' => 'boolean',
    ];

    public static function forSchoolOrDefault(int $schoolId): self
    {
        $existing = static::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->first();

        if ($existing) {
            return $existing;
        }

        return static::createForSchool($schoolId, [
            'enabled' => false,
            'min_days_outstanding' => self::DEFAULT_MIN_DAYS,
            'reminder_interval_days' => self::DEFAULT_INTERVAL_DAYS,
            'current_term_only' => true,
        ]);
    }
}
