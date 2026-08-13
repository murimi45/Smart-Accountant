<?php

namespace Database\Seeders;

use App\Models\Schools;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $school = Schools::factory()->create([
            'school_name' => 'Demo School',
            'email'       => 'demo@school.test',
        ]);

        User::factory()
            ->admin()
            ->forSchool($school)
            ->create([
                'admin_name' => 'Demo Admin',
                'email'      => 'admin@school.test',
            ]);

        $this->call(AccountsTableSeeder::class, false, ['schoolId' => $school->id]);
        $this->call(ModulesSeeder::class);
        $this->call(GradingSchemesSeeder::class);
    }
}
