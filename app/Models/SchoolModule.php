<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolModule extends Model
{
    protected $fillable = [
        'school_id',
        'module_id',
        'status',
        'starts_at',
        'ends_at',
        'settings',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
        'settings'  => 'array',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(Schools::class, 'school_id');
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function isEntitled(): bool
    {
        if (! in_array($this->status, [Module::STATUS_ACTIVE, Module::STATUS_TRIAL], true)) {
            return false;
        }

        if ($this->ends_at && $this->ends_at->isPast()) {
            return false;
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }

        return true;
    }
}
