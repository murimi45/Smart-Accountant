<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class StatutoryRate extends Model
{
    public const PERSONAL_RELIEF = 'personal_relief';

    public const NSSF_TIER1 = 'nssf_tier1';

    public const NSSF_TIER2 = 'nssf_tier2';

    public const SHIF = 'shif';

    public const HOUSING_LEVY = 'housing_levy';

    protected $fillable = [
        'code',
        'effective_from',
        'employee_rate',
        'employer_rate',
        'fixed_amount',
        'minimum_amount',
        'lower_limit',
        'upper_limit',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'employee_rate' => 'decimal:4',
        'employer_rate' => 'decimal:4',
        'fixed_amount' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
        'lower_limit' => 'decimal:2',
        'upper_limit' => 'decimal:2',
    ];

    public static function rateForDate(string $code, Carbon|string $date): ?self
    {
        $date = Carbon::parse($date)->toDateString();

        return static::query()
            ->where('code', $code)
            ->whereDate('effective_from', '<=', $date)
            ->orderByDesc('effective_from')
            ->first();
    }
}