<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Module extends Model
{
    public const SLUG_ACCOUNTANT = 'accountant';

    public const SLUG_HR = 'hr';

    public const SLUG_GRADING = 'grading';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_TRIAL = 'trial';

    public const STATUS_EXPIRED = 'expired';

    public const STATUS_DISABLED = 'disabled';

    protected $fillable = [
        'slug',
        'name',
        'description',
        'version',
        'is_core',
    ];

    protected $casts = [
        'is_core' => 'boolean',
    ];

    public function schoolModules(): HasMany
    {
        return $this->hasMany(SchoolModule::class);
    }

    public function schools(): BelongsToMany
    {
        return $this->belongsToMany(Schools::class, 'school_modules', 'module_id', 'school_id')
            ->withPivot(['status', 'starts_at', 'ends_at', 'settings'])
            ->withTimestamps();
    }
}
