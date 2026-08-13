<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentType extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'name',
        'slug',
        'is_competency',
    ];

    protected function casts(): array
    {
        return [
            'is_competency' => 'boolean',
        ];
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    public function weights(): HasMany
    {
        return $this->hasMany(AssessmentWeight::class);
    }
}
