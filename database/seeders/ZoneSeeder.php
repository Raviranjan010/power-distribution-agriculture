<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ZoneSeeder extends Seeder
{
    public function run(): void
    {
        $zones = [
            ['name' => 'Jaipur East', 'district' => 'Jaipur', 'state' => 'india', 'division_id' => 1],
            ['name' => 'Jaipur West', 'district' => 'Jaipur', 'state' => 'india', 'division_id' => 1],
            ['name' => 'Jodhpur Rural', 'district' => 'Jodhpur', 'state' => 'india', 'division_id' => 2],
            ['name' => 'Udaipur North', 'district' => 'Udaipur', 'state' => 'india', 'division_id' => 3],
            ['name' => 'Ajmer Cantt', 'district' => 'Ajmer', 'state' => 'india', 'division_id' => 4],
            ['name' => 'Amritsar City', 'district' => 'Amritsar', 'state' => 'Punjab', 'division_id' => 5],
            ['name' => 'Ludhiana South', 'district' => 'Ludhiana', 'state' => 'Punjab', 'division_id' => 6],
            ['name' => 'Lucknow Central', 'district' => 'Lucknow', 'state' => 'Uttar Pradesh', 'division_id' => 7],
            ['name' => 'Varanasi North', 'district' => 'Varanasi', 'state' => 'Uttar Pradesh', 'division_id' => 8],
            ['name' => 'Ahmedabad West', 'district' => 'Ahmedabad', 'state' => 'Gujarat', 'division_id' => 9],
            ['name' => 'Surat East', 'district' => 'Surat', 'state' => 'Gujarat', 'division_id' => 10],
            ['name' => 'Pune South', 'district' => 'Pune', 'state' => 'Maharashtra', 'division_id' => 11],
            ['name' => 'Nagpur North', 'district' => 'Nagpur', 'state' => 'Maharashtra', 'division_id' => 12],
            ['name' => 'Kapurthala Central', 'district' => 'Kapurthala', 'state' => 'Punjab', 'division_id' => 5],
            ['name' => 'Patna East', 'district' => 'Patna', 'state' => 'Bihar', 'division_id' => 13],
        ];

        foreach ($zones as $zone) {
            DB::table('zones')->insert(array_merge($zone, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}