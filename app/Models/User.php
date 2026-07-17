<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    public const ROLE_ADMIN = 'admin';

    public const ROLE_ACCOUNTANT = 'accountant';

    public const ROLE_HR_MANAGER = 'hr_manager';

    public const ROLE_TEACHER = 'teacher';

    public const ROLE_PLATFORM = 'platform';

    /** Roles that belong to a product module (not shared Academics/core). */
    public const MODULE_ROLES = [
        self::ROLE_ACCOUNTANT => 'accountant',
        self::ROLE_HR_MANAGER => 'hr',
        self::ROLE_TEACHER => 'grading',
    ];

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        // 'name',
        // 'email',
        // 'password',
    'admin_name',
    'email',
    'password',
    'role',
    'phone',
    ];

    protected $guarded = ['school_id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_enabled' => 'boolean',
        ];
    }



    public function school()
    {
        return $this->belongsTo(Schools::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isAccountant(): bool
    {
        return $this->role === self::ROLE_ACCOUNTANT;
    }

    public function isHrManager(): bool
    {
        return $this->role === self::ROLE_HR_MANAGER;
    }

    public function isTeacher(): bool
    {
        return $this->role === self::ROLE_TEACHER;
    }

    public function hasRole(string ...$roles): bool
    {
        $current = strtolower((string) $this->role);
        $allowed = array_map('strtolower', $roles);

        return in_array($current, $allowed, true);
    }

    public function isSensitiveRole(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN, self::ROLE_ACCOUNTANT, self::ROLE_HR_MANAGER);
    }

    public function isPlatformAdmin(): bool
    {
        return $this->role === self::ROLE_PLATFORM && $this->school_id === null;
    }

    /** Module slug this role is tied to, if any (admin/platform are not module-scoped). */
    public function moduleSlugForRole(): ?string
    {
        return self::MODULE_ROLES[$this->role] ?? null;
    }

    // Set 2FA secret (encrypt for storage)
    public function setTwoFactorSecret(string $secret): void
    {
        $this->two_factor_secret = encrypt($secret);
        $this->save();
    }

    // Get decrypted secret
    public function getTwoFactorSecret(): ?string
    {
        return $this->two_factor_secret ? decrypt($this->two_factor_secret) : null;
    }

    // Set recovery codes (store JSON encrypted)
    public function setTwoFactorRecoveryCodes(array $codes): void
    {
        $this->two_factor_recovery_codes = encrypt(json_encode($codes));
        $this->save();
    }

    public function getTwoFactorRecoveryCodes(): array
    {
        if (! $this->two_factor_recovery_codes) return [];
        return json_decode(decrypt($this->two_factor_recovery_codes), true) ?? [];
    }

    public function enableTwoFactor(): void
    {
        $this->two_factor_enabled = true;
        $this->two_factor_confirmed_at = now();
        $this->save();
    }

    public function disableTwoFactor(): void
    {
        $this->two_factor_enabled = false;
        $this->two_factor_secret = null;
        $this->two_factor_recovery_codes = null;
        $this->two_factor_confirmed_at = null;
        $this->save();
    }

}
