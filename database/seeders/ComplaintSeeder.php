<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Connection;
use App\Models\Complaint;
use Carbon\Carbon;

class ComplaintSeeder extends Seeder
{
    public function run(): void
    {
        $farmer = User::where('role', 'farmer')->first();
        $connection = Connection::where('consumer_id', $farmer->id)->first();
        $lineman = User::where('role', 'lineman')->where('zone_id', $farmer->zone_id)->first();

        Complaint::create([
            'grv_number' => 'GRV-2026-0001',
            'consumer_id' => $farmer->id,
            'connection_id' => $connection->id,
            'complaint_type' => 'transformer_issue',
            'description' => 'Transformer sparked and caught fire near the tubewell.',
            'priority' => 'high',
            'status' => 'assigned',
            'assigned_to' => $lineman->id,
            'filed_at' => Carbon::now()->subDays(2),
        ]);

        Complaint::create([
            'grv_number' => 'GRV-2026-0002',
            'consumer_id' => $farmer->id,
            'connection_id' => $connection->id,
            'complaint_type' => 'billing_error',
            'description' => 'Bill amount is unusually high despite power cut last month.',
            'priority' => 'medium',
            'status' => 'resolved',
            'resolution_remarks' => 'Reading was miscalculated. Corrected and new bill generated.',
            'filed_at' => Carbon::now()->subDays(10),
            'resolved_at' => Carbon::now()->subDays(8),
        ]);
    }
}