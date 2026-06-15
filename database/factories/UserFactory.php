<?php

namespace Database\Factories;

use App\Models\Schools;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'admin_name'         => fake()->name(),
            'email'              => fake()->unique()->safeEmail(),
            'email_verified_at'  => now(),
            'password'           => static::$password ??= Hash::make('password'),
            'remember_token'     => Str::random(10),
            'role'               => User::ROLE_ADMIN,
            'two_factor_enabled' => false,
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (User $user) {
            if ($user->isPlatformAdmin()) {
                $user->school_id = null;

                return;
            }

            if (! $user->school_id) {
                $user->school_id = Schools::factory()->create()->id;
            }
        });
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_ADMIN,
        ]);
    }

    public function accountant(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_ACCOUNTANT,
        ]);
    }

    public function platform(): static
    {
        return $this->state(fn (array $attributes) => [
            'role'      => User::ROLE_PLATFORM,
            'school_id' => null,
        ]);
    }

    public function forSchool(Schools $school): static
    {
        return $this->afterMaking(function (User $user) use ($school) {
            $user->school_id = $school->id;
        });
    }
}
