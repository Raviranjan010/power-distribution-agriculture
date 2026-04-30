<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Farmer;
use App\Models\Connection;
use App\Models\Complaint;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_farmers' => Farmer::count(),
            'total_connections' => Connection::where('status', 'active')->count(),
            'pending_connections' => Connection::where('status', 'pending')->count(),
            'open_complaints' => Complaint::where('status', 'open')->count(),
        ];

        // Fetch Connections grouped by Month for the Chart
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $chartData = array_fill(0, 12, 0); // initialize with 0s

        $connectionsByMonth = Connection::selectRaw('strftime("%m", created_at) as month, count(*) as count')
                                        ->groupBy('month')
                                        ->get();

        foreach($connectionsByMonth as $data) {
            $monthIndex = intval($data->month) - 1; // 0-indexed
            $chartData[$monthIndex] = $data->count;
        }

        return view('admin.dashboard', compact('stats', 'months', 'chartData'));
    }
}
