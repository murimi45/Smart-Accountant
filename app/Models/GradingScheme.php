<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GradingScheme extends Model
{
    public const SLUG_CBC = 'cbc';

    public const SLUG_INTERNATIONAL = 'international';

    protected $fillable = [
        'slug',
        'name',
        'config',
    ];

    protected function casts(): array
    {
        return [
            'config' => 'array',
        ];
    }

    public function isCbc(): bool
    {
        return $this->slug === self::SLUG_CBC;
    }

    public function isInternational(): bool
    {
        return $this->slug === self::SLUG_INTERNATIONAL;
    }

    public function schoolSettings(): HasMany
    {
        return $this->hasMany(SchoolGradingSetting::class);
    }

    public function gradeScales(): HasMany
    {
        return $this->hasMany(GradeScale::class);
    }
}
