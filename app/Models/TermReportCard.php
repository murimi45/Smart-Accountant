<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TermReportCard extends Model
{
    use BelongsToSchool;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    protected $fillable = [
        'student_enrollment_id',
        'term_id',
        'status',
        'published_at',
        'published_by',
        'pdf_path',
        'scheme_snapshot',
        'term_mean',
        'term_rank',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'scheme_snapshot' => 'array',
            'term_mean' => 'decimal:2',
        ];
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(StudentEnrollment::class, 'student_enrollment_id');
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ReportCardItem::class)->orderBy('sort_order');
    }
}
