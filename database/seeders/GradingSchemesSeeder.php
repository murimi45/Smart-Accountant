<?php

namespace Database\Seeders;

use App\Models\GradingScheme;
use Illuminate\Database\Seeder;

class GradingSchemesSeeder extends Seeder
{
    public function run(): void
    {
        GradingScheme::query()->updateOrCreate(
            ['slug' => GradingScheme::SLUG_CBC],
            [
                'name' => 'Kenya CBC',
                'config' => [
                    'entry_mode' => 'competency',
                    'bands' => ['EE', 'ME', 'AE', 'BE'],
                    'band_labels' => [
                        'EE' => 'Exceeding Expectations',
                        'ME' => 'Meeting Expectations',
                        'AE' => 'Approaching Expectations',
                        'BE' => 'Below Expectations',
                    ],
                ],
            ]
        );

        GradingScheme::query()->updateOrCreate(
            ['slug' => GradingScheme::SLUG_INTERNATIONAL],
            [
                'name' => 'International',
                'config' => [
                    'entry_mode' => 'numeric',
                    'default_scale' => [
                        ['label' => 'A', 'code' => 'A', 'min' => 80, 'max' => 100, 'gpa' => 4.0, 'sort' => 1],
                        ['label' => 'B', 'code' => 'B', 'min' => 70, 'max' => 79.99, 'gpa' => 3.0, 'sort' => 2],
                        ['label' => 'C', 'code' => 'C', 'min' => 60, 'max' => 69.99, 'gpa' => 2.0, 'sort' => 3],
                        ['label' => 'D', 'code' => 'D', 'min' => 50, 'max' => 59.99, 'gpa' => 1.0, 'sort' => 4],
                        ['label' => 'E', 'code' => 'E', 'min' => 40, 'max' => 49.99, 'gpa' => 0.5, 'sort' => 5],
                        ['label' => 'F', 'code' => 'F', 'min' => 0, 'max' => 39.99, 'gpa' => 0.0, 'sort' => 6],
                    ],
                ],
            ]
        );
    }
}
