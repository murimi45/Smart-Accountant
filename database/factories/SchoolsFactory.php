<?php

namespace Database\Factories;

use App\Models\Schools;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Schools>
 */
class SchoolsFactory extends Factory
{
    public function definition(): array
    {
        return [
            'school_name'         => fake()->company().' School',
            'email'               => fake()->unique()->companyEmail(),
            'phone'               => fake()->numerify('07########'),
            'address'             => fake()->address(),
            'subscription_status' => 'active',
        ];
    }
}
