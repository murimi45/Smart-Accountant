<?php

namespace Database\Seeders;

use App\Models\PayeBand;
use App\Models\StatutoryRate;
use Illuminate\Database\Seeder;

class StatutoryRatesSeeder extends Seeder
{
    public function run(): void
    {
        $this->payeBands();
        $this->personalRelief();
        $this->nssf();
        $this->shif();
        $this->housingLevy();
    }

    private function payeBands(): void
    {
        $effectiveFrom = '2023-07-01';

        $bands = [
            ['band_order' => 1, 'lower_limit' => 0, 'upper_limit' => 24000, 'rate' => 10],
            ['band_order' => 2, 'lower_limit' => 24000, 'upper_limit' => 32333, 'rate' => 25],
            ['band_order' => 3, 'lower_limit' => 32333, 'upper_limit' => 500000, 'rate' => 30],
            ['band_order' => 4, 'lower_limit' => 500000, 'upper_limit' => 800000, 'rate' => 32.5],
            ['band_order' => 5, 'lower_limit' => 800000, 'upper_limit' => null, 'rate' => 35],
        ];

        foreach ($bands as $band) {
            PayeBand::query()->updateOrCreate(
                [
                    'effective_from' => $effectiveFrom,
                    'band_order' => $band['band_order'],
                ],
                $band
            );
        }
    }

    private function personalRelief(): void
    {
        StatutoryRate::query()->updateOrCreate(
            [
                'code' => StatutoryRate::PERSONAL_RELIEF,
                'effective_from' => '2023-07-01',
            ],
            ['fixed_amount' => 2400]
        );
    }

    private function nssf(): void
    {
        $sets = [
            '2025-02-01' => ['lel' => 8000, 'uel' => 72000],
            '2026-02-01' => ['lel' => 9000, 'uel' => 108000],
        ];

        foreach ($sets as $effectiveFrom => $limits) {
            StatutoryRate::query()->updateOrCreate(
                ['code' => StatutoryRate::NSSF_TIER1, 'effective_from' => $effectiveFrom],
                [
                    'employee_rate' => 6,
                    'employer_rate' => 6,
                    'lower_limit' => 0,
                    'upper_limit' => $limits['lel'],
                ]
            );

            StatutoryRate::query()->updateOrCreate(
                ['code' => StatutoryRate::NSSF_TIER2, 'effective_from' => $effectiveFrom],
                [
                    'employee_rate' => 6,
                    'employer_rate' => 6,
                    'lower_limit' => $limits['lel'],
                    'upper_limit' => $limits['uel'],
                ]
            );
        }
    }

    private function shif(): void
    {
        StatutoryRate::query()->updateOrCreate(
            ['code' => StatutoryRate::SHIF, 'effective_from' => '2024-10-01'],
            [
                'employee_rate' => 2.75,
                'minimum_amount' => 300,
            ]
        );
    }

    private function housingLevy(): void
    {
        StatutoryRate::query()->updateOrCreate(
            ['code' => StatutoryRate::HOUSING_LEVY, 'effective_from' => '2024-03-19'],
            [
                'employee_rate' => 1.5,
                'employer_rate' => 1.5,
            ]
        );
    }
}