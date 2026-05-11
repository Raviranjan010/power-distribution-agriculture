<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Zone;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');
        $zones = Zone::all();

        // Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@agripower.gov.in',
            'password' => $password,
            'role' => 'admin',
            'phone' => '9876543210',
            'is_active' => true,
        ]);

        // SDOs
        foreach ($zones as $index => $zone) {
            $sdo = User::create([
                'name' => "SDO " . $zone->district,
                'email' => "sdo.{$zone->id}@agripower.gov.in",
                'password' => $password,
                'role' => 'sdo',
                'zone_id' => $zone->id,
                'phone' => '987650000' . $index,
                'is_active' => true,
            ]);
            $zone->sdo_id = $sdo->id;
            $zone->save();
        }

        // Linemen
        foreach ($zones as $index => $zone) {
            User::create([
                'name' => "Lineman " . $zone->district,
                'email' => "lineman.{$zone->id}@agripower.gov.in",
                'password' => $password,
                'role' => 'lineman',
                'zone_id' => $zone->id,
                'phone' => '987660000' . $index,
                'is_active' => true,
            ]);
        }

        // Farmers
        $farmers = [
            ['name' => 'Rameshwar Jat', 'village' => 'Amer', 'district' => 'Jaipur'],
            ['name' => 'Kailash Bishnoi', 'village' => 'Luni', 'district' => 'Jodhpur'],
            ['name' => 'Madan Lal', 'village' => 'Gogunda', 'district' => 'Udaipur'],
            ['name' => 'Bhagirath Singh', 'village' => 'Kishangarh', 'district' => 'Ajmer'],
            ['name' => 'Omprakash Sharma', 'village' => 'Sanganer', 'district' => 'Jaipur'],
        ];

        foreach ($farmers as $index => $farmer) {
            User::create([
                'name' => $farmer['name'],
                'email' => strtolower(str_replace(' ', '.', $farmer['name'])) . "@gmail.com",
                'password' => $password,
                'role' => 'farmer',
                'zone_id' => $index + 1,
                'phone' => '987670000' . $index,
                'farmer_id_number' => 'KV-2024-' . (8820 + $index),
                'village' => $farmer['village'],
                'district' => $farmer['district'],
                'state' => 'Rajasthan',
                'aadhar_number' => '12345678901' . $index,
                'is_active' => true,
            ]);
        }
    }
}