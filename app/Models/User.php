<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
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
    'school_id',
    'phone',
    ];

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

     public function isAdmin()
{
    return $this->role === 'admin';
}

public function isAccountant()
{
    return $this->role === 'accountant';
}


     public function isSensitiveRole(): bool
    {
        return in_array($this->role, ['admin','accountant']);
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
