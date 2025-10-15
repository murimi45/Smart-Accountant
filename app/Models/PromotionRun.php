<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionRun extends Model
{
    protected $fillable = [
        'school_id',
        'from_term_id',
        'to_term_id',
        'promoted_by',
        'type',
    ];
}
