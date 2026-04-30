<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ZoneSeeder extends Seeder
{
    public function run(): void
    {
        $zones = [
            ['name' => 'Nawanshahr East', 'district' => 'Nawanshahr', 'division_id' => 1],
            ['name' => 'Nawanshahr West', 'district' => 'Nawanshahr', 'division_id' => 1],
            ['name' => 'Phagwara Rural', 'district' => 'Phagwara', 'division_id' => 2],
            ['name' => 'Hoshiarpur North', 'district' => 'Hoshiarpur', 'division_id' => 3],
            ['name' => 'Jalandhar Cantt', 'district' => 'Jalandhar', 'division_id' => 4],
        ];

        foreach ($zones as $zone) {
            DB::table('zones')->insert(array_merge($zone, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}