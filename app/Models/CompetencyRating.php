<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetencyRating extends Model
{
    use BelongsToSchool;

    public const BANDS = ['EE', 'ME', 'AE', 'BE'];

    protected $fillable = [
        'assessment_id',
        'student_enrollment_id',
        'band_code',
        'comment',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(StudentEnrollment::class, 'student_enrollment_id');
    }
}
