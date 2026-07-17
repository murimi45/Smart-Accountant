<?php

namespace App\Models;

use App\Core\Modules\ModuleRegistry;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schools extends Model
{
    use HasFactory;

    protected $table = 'schools';

    protected $fillable = [
        'school_name',
        'email',
        'phone',
        'address',
        'subscription_status',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function feePayments(): HasMany
    {
        return $this->hasMany(FeePayment::class);
    }

    public function schoolModules(): HasMany
    {
        return $this->hasMany(SchoolModule::class, 'school_id');
    }

    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(Module::class, 'school_modules', 'school_id', 'module_id')
            ->withPivot(['status', 'starts_at', 'ends_at', 'settings'])
            ->withTimestamps();
    }

    public function hasModule(string $slug): bool
    {
        return ModuleRegistry::schoolHas((int) $this->id, $slug);
    }
}
