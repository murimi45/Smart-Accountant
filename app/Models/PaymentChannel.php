<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToSchool;

class PaymentChannel extends Model
{
    use BelongsToSchool;
     protected $fillable = [
        'school_id', 'type', 'identifier', 'account_pattern', 'is_active'
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /** Resolve paybill/till → tenant (API callbacks — no auth). */
    public static function findActiveByIdentifier(string $identifier): ?self
    {
        return static::withoutGlobalScopes()
            ->where('identifier', $identifier)
            ->where('is_active', true)
            ->first();
    }
}
