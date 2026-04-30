<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ZoneSeeder::class,
            UserSeeder::class,
            TariffCategorySeeder::class,
            ConnectionSeeder::class,
            SubsidySchemeSeeder::class,
            ComplaintSeeder::class,
        ]);
    }
}