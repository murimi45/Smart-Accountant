<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'name',
        'code',
        'is_learning_area',
        'parent_subject_id',
    ];

    protected function casts(): array
    {
        return [
            'is_learning_area' => 'boolean',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_subject_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_subject_id');
    }

    public function classSubjects(): HasMany
    {
        return $this->hasMany(ClassSubject::class);
    }
}
