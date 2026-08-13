<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GradeScaleBand extends Model
{
    protected $fillable = [
        'grade_scale_id',
        'label',
        'code',
        'min_score',
        'max_score',
        'gpa_points',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'min_score' => 'decimal:2',
            'max_score' => 'decimal:2',
            'gpa_points' => 'decimal:2',
        ];
    }

    public function gradeScale(): BelongsTo
    {
        return $this->belongsTo(GradeScale::class);
    }
}
