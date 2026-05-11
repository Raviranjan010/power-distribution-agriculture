<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complaint;
use App\Models\MeterReading;
use App\Models\Connection;
use Illuminate\Support\Facades\Auth;

class LinemanController extends Controller
{
    public function dashboard()
    {
        $complaints = Complaint::where('assigned_to', Auth::id())->get();
        $readings = MeterReading::where('lineman_id', Auth::id())
            ->whereMonth('reading_date', now()->month)
            ->get();
        $connections = Connection::where('zone_id', Auth::user()->zone_id)
            ->where('status', 'active')
            ->get();

        return view('lineman.dashboard', compact('complaints', 'readings', 'connections'));
    }

    public function storeReading(Request $request)
    {
        $request->validate([
            'connection_id' => 'required|exists:connections,id',
            'current_reading' => 'required|numeric|min:0',
            'remarks' => 'nullable|string'
        ]);

        $connection = Connection::where('id', $request->connection_id)
            ->whereHas('consumer', fn($q) => $q->where('zone_id', Auth::user()->zone_id))
            ->firstOrFail();
        
        $lastReading = MeterReading::where('connection_id', $connection->id)
            ->orderBy('reading_date', 'desc')
            ->first();
            
        $previous_reading = $lastReading ? $lastReading->current_reading : 0;
        
        $units_consumed = max(0, $request->current_reading - $previous_reading);

        MeterReading::create([
            'connection_id' => $connection->id,
            'lineman_id' => Auth::id(),
            'reading_date' => now(),
            'previous_reading' => $previous_reading,
            'current_reading' => $request->current_reading,
            'units_consumed' => $units_consumed,
            'is_verified' => false,
            'remarks' => $request->remarks
        ]);

        return back()->with('success', 'Meter reading submitted successfully and is pending verification.');
    }

    public function updateComplaint(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:in_progress,resolved',
            'remarks' => 'nullable|string'
        ]);

        $complaint = Complaint::where('assigned_to', Auth::id())->findOrFail($id);
        
        $complaint->status = $request->status;
        $complaint->resolution_remarks = $request->remarks;
        if ($request->status === 'resolved') {
            $complaint->resolved_at = now();
        }
        $complaint->save();

        return back()->with('success', 'Complaint updated successfully.');
    }
}
