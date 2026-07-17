<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModulesSeeder extends Seeder
{
    public function run(): void
    {
        $catalog = [
            [
                'slug'        => Module::SLUG_ACCOUNTANT,
                'name'        => 'Accountant',
                'description' => 'Fees, invoices, ledger, expenses, and bank reconciliation.',
                'version'     => '1.0.0',
                'is_core'     => true,
            ],
            [
                'slug'        => Module::SLUG_HR,
                'name'        => 'HR',
                'description' => 'Employees, departments, and leave management.',
                'version'     => '0.1.0',
                'is_core'     => false,
            ],
            [
                'slug'        => Module::SLUG_GRADING,
                'name'        => 'Grading',
                'description' => 'Subjects, assessments, grades, and report cards.',
                'version'     => '0.1.0',
                'is_core'     => false,
            ],
        ];

        foreach ($catalog as $row) {
            Module::query()->updateOrCreate(
                ['slug' => $row['slug']],
                $row
            );
        }
    }
}
