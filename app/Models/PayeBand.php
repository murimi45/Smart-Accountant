<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class PayeBand extends Model
{
    protected $fillable = [
        'effective_from',
        'band_order',
        'lower_limit',
        'upper_limit',
        'rate',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'lower_limit' => 'decimal:2',
        'upper_limit' => 'decimal:2',
        'rate' => 'decimal:2',
    ];

    /** @return Collection<int, self> */
    public static function bandsForDate(Carbon|string $date): Collection
    {
        $date = Carbon::parse($date)->toDateString();

        $effectiveFrom = static::query()
            ->whereDate('effective_from', '<=', $date)
            ->max('effective_from');

        if (! $effectiveFrom) {
            return collect();
        }

        return static::query()
            ->whereDate('effective_from', $effectiveFrom)
            ->orderBy('band_order')
            ->get();
    }

    public function scopeEffectiveOn(Builder $query, Carbon|string $date): Builder
    {
        $date = Carbon::parse($date)->toDateString();

        return $query->whereDate('effective_from', '<=', $date);
    }
}