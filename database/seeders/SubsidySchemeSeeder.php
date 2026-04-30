<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubsidySchemeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('subsidy_schemes')->insert([
            [
                'scheme_name' => 'PM-KUSUM Solar Subsidy',
                'description' => 'Subsidy for solar water pumps and grid-connected solar power plants.',
                'eligibility_criteria' => json_encode(['max_land_area_hectares' => 5, 'farmer_category' => 'all']),
                'discount_percentage' => 30.00,
                'max_units_covered' => 500,
                'start_date' => '2024-01-01',
                'end_date' => '2026-12-31',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'scheme_name' => 'Punjab State Free Power (Agriculture)',
                'description' => 'Free power for tubewells up to specified limits.',
                'eligibility_criteria' => json_encode(['max_load_kw' => 10, 'state' => 'Punjab']),
                'discount_percentage' => 100.00,
                'max_units_covered' => 1000,
                'start_date' => '2024-04-01',
                'end_date' => '2026-03-31',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}