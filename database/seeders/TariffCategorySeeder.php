<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TariffCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Agricultural AP (Subsidized)', 'rate_per_unit' => 2.50, 'fixed_charge_per_kw' => 50.00, 'applicable_to' => 'agricultural', 'effective_from' => '2024-01-01'],
            ['name' => 'Agricultural AP (Unmetered)', 'rate_per_unit' => 0.00, 'fixed_charge_per_kw' => 450.00, 'applicable_to' => 'agricultural', 'effective_from' => '2024-01-01'],
            ['name' => 'Domestic Supply', 'rate_per_unit' => 6.00, 'fixed_charge_per_kw' => 100.00, 'applicable_to' => 'domestic', 'effective_from' => '2024-01-01'],
        ];

        foreach ($categories as $cat) {
            DB::table('tariff_categories')->insert(array_merge($cat, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}