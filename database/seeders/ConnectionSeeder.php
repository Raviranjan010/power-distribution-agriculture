<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Connection;
use App\Models\TariffCategory;
use App\Models\MeterReading;
use App\Models\Bill;
use Carbon\Carbon;

class ConnectionSeeder extends Seeder
{
    public function run(): void
    {
        $farmers = User::where('role', 'farmer')->get();
        $tariff = TariffCategory::where('applicable_to', 'agricultural')->first();

        foreach ($farmers as $index => $farmer) {
            $connection = Connection::create([
                'connection_number' => 'KV-CN-00' . ($index + 1),
                'consumer_id' => $farmer->id,
                'connection_type' => 'tubewell_pump',
                'field_name' => $farmer->village . ' Farm',
                'sanctioned_load_kw' => 7.5,
                'meter_number' => 'MT-' . rand(10000, 99999),
                'tariff_category_id' => $tariff->id,
                'status' => 'active',
                'installation_date' => Carbon::now()->subMonths(14),
                'sdo_id' => $farmer->zone->sdo_id ?? 2, // assuming SDO is created
            ]);

            // Create 12 months of meter readings and bills
            $previousReading = 100;
            for ($i = 12; $i >= 1; $i--) {
                $readingDate = Carbon::now()->subMonths($i)->startOfMonth();
                $units = rand(150, 400);
                $currentReading = $previousReading + $units;

                $reading = MeterReading::create([
                    'connection_id' => $connection->id,
                    'lineman_id' => User::where('role', 'lineman')->first()->id,
                    'reading_date' => $readingDate,
                    'previous_reading' => $previousReading,
                    'current_reading' => $currentReading,
                    'units_consumed' => $units,
                    'is_verified' => true,
                ]);

                $energyCharges = $units * $tariff->rate_per_unit;
                $fixedCharges = $connection->sanctioned_load_kw * $tariff->fixed_charge_per_kw;
                $netPayable = $energyCharges + $fixedCharges;

                Bill::create([
                    'bill_number' => 'BILL-' . $readingDate->format('Ym') . '-C' . $connection->id . '-' . ($i),
                    'connection_id' => $connection->id,
                    'meter_reading_id' => $reading->id,
                    'billing_month' => $readingDate->month,
                    'billing_year' => $readingDate->year,
                    'units_consumed' => $units,
                    'rate_per_unit' => $tariff->rate_per_unit,
                    'energy_charges' => $energyCharges,
                    'fixed_charges' => $fixedCharges,
                    'taxes' => 0,
                    'subsidy_amount' => 0,
                    'net_payable' => $netPayable,
                    'due_date' => $readingDate->copy()->endOfMonth()->addDays(15),
                    'status' => $i > 1 ? 'paid' : 'pending', // last month pending
                ]);

                $previousReading = $currentReading;
            }
        }
    }
}