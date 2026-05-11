<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ZoneSeeder extends Seeder
{
    public function run(): void
    {
        $zones = [
            ['name' => 'Jaipur East', 'district' => 'Jaipur', 'division_id' => 1],
            ['name' => 'Jaipur West', 'district' => 'Jaipur', 'division_id' => 1],
            ['name' => 'Jodhpur Rural', 'district' => 'Jodhpur', 'division_id' => 2],
            ['name' => 'Udaipur North', 'district' => 'Udaipur', 'division_id' => 3],
            ['name' => 'Ajmer Cantt', 'district' => 'Ajmer', 'division_id' => 4],
        ];

        foreach ($zones as $zone) {
            DB::table('zones')->insert(array_merge($zone, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}