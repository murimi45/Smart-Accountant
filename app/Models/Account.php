<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;

class Account extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'name',
        'code',
        'category',
        'normal_balance',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function ledgerEntries()
    {
        return $this->hasMany(LedgerEntry::class);
    }

    public static function defaultFor(int $schoolId, string $name): self
    {
        $account = static::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('name', $name)
            ->first();

        if (! $account) {
            throw new RuntimeException("Default account \"{$name}\" not found for school #{$schoolId}. Run accounts:backfill.");
        }

        return $account;
    }
}
