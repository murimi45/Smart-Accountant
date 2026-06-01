<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class PromotionRun extends Model
{
    protected $fillable = [
        'school_id',
        'from_term_id',
        'to_term_id',
        'promoted_by',
        'type',
        'status',
        'active_key',
    ];




public function fromTerm(): BelongsTo
{
    return $this->belongsTo(Term::class, 'from_term_id');
}
public function toTerm(): BelongsTo
{
    return $this->belongsTo(Term::class, 'to_term_id');
}
public function promotedBy(): BelongsTo
{
    return $this->belongsTo(User::class, 'promoted_by');
}
// optional, if you need school on the run
public function school(): BelongsTo
{
    return $this->belongsTo(Schools::class, 'school_id');
}

public static function activeKey(
    int $schoolId,
    int $fromTermId,
    int $toTermId,
    string $type // 'term_promotion' | 'class_promotion'
): string {
    return "{$schoolId}-{$fromTermId}-{$toTermId}-{$type}";
}


}
