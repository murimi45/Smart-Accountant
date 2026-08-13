<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportCardItem extends Model
{
    protected $fillable = [
        'term_report_card_id',
        'subject_id',
        'subject_name',
        'total_percent',
        'letter_grade',
        'band_code',
        'gpa_points',
        'teacher_comment',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'total_percent' => 'decimal:2',
            'gpa_points' => 'decimal:2',
        ];
    }

    public function reportCard(): BelongsTo
    {
        return $this->belongsTo(TermReportCard::class, 'term_report_card_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}
