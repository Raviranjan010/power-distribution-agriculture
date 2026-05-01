<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Connection;
use App\Models\Complaint;
use App\Models\MeterReading;
use App\Models\Bill;
use App\Models\ConsumerSubsidy;
use App\Models\TariffCategory;
use App\Models\Zone;
use Carbon\Carbon;

class OfficerController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $zone = Zone::find($user->zone_id);

        $pendingConnections = Connection::where('status', 'pending')
            ->whereHas('consumer', fn($q) => $q->where('zone_id', $user->zone_id))->with('consumer')->get();

        $complaints = Complaint::whereHas('consumer', fn($q) => $q->where('zone_id', $user->zone_id))
            ->with(['consumer', 'assignedTo'])->orderByDesc('filed_at')->get();
        $complaintsByStatus = $complaints->groupBy('status');

        $pendingReadings = MeterReading::where('is_verified', false)
            ->whereHas('connection.consumer', fn($q) => $q->where('zone_id', $user->zone_id))
            ->with(['connection.consumer', 'lineman'])->orderByDesc('reading_date')->get();

        $zoneConnIds = Connection::whereHas('consumer', fn($q) => $q->where('zone_id', $user->zone_id))->pluck('id');
        $monthlyRevenue = Bill::whereIn('connection_id', $zoneConnIds)->where('status', 'paid')
            ->where('billing_month', now()->month)->where('billing_year', now()->year)->sum('net_payable');

        $linemen = User::where('role', 'lineman')->where('zone_id', $user->zone_id)->where('is_active', true)->get();
        $tariffCategories = TariffCategory::where('is_active', true)->get();

        $pendingSubsidies = ConsumerSubsidy::where('status', 'applied')
            ->whereHas('consumer', fn($q) => $q->where('zone_id', $user->zone_id))
            ->with(['consumer', 'scheme'])->get();

        return view('officer.dashboard', compact(
            'zone', 'pendingConnections', 'complaintsByStatus', 'pendingReadings',
            'monthlyRevenue', 'linemen', 'tariffCategories', 'complaints', 'pendingSubsidies'
        ));
    }

    public function approveConnection(Request $request, $id)
    {
        $request->validate(['tariff_category_id' => 'required|exists:tariff_categories,id']);
        $conn = Connection::findOrFail($id);
        $conn->update([
            'tariff_category_id' => $request->tariff_category_id, 'meter_number' => 'MT-' . rand(10000, 99999),
            'status' => 'active', 'installation_date' => now(), 'sdo_id' => Auth::id(),
        ]);
        return back()->with('success', 'Connection ' . $conn->connection_number . ' approved!');
    }

    public function rejectConnection($id)
    {
        $conn = Connection::findOrFail($id);
        $conn->update(['status' => 'disconnected']);
        return back()->with('success', 'Connection ' . $conn->connection_number . ' rejected.');
    }

    public function assignComplaint(Request $request, $id)
    {
        $request->validate(['assigned_to' => 'required|exists:users,id']);
        $c = Complaint::findOrFail($id);
        $c->update(['assigned_to' => $request->assigned_to, 'assigned_by' => Auth::id(), 'status' => 'assigned']);
        return back()->with('success', 'Complaint ' . $c->grv_number . ' assigned!');
    }

    public function resolveComplaint(Request $request, $id)
    {
        $c = Complaint::findOrFail($id);
        $c->update(['status' => 'resolved', 'resolution_remarks' => $request->input('resolution_remarks', 'Resolved by SDO.'), 'resolved_at' => now()]);
        return back()->with('success', 'Complaint resolved.');
    }

    public function verifyReading($id)
    {
        MeterReading::findOrFail($id)->update(['is_verified' => true]);
        return back()->with('success', 'Meter reading verified.');
    }

    public function generateBills()
    {
        $user = Auth::user();
        $cm = now()->month; $cy = now()->year; $count = 0;
        $conns = Connection::where('status', 'active')
            ->whereHas('consumer', fn($q) => $q->where('zone_id', $user->zone_id))->with('tariffCategory')->get();

        foreach ($conns as $conn) {
            $reading = MeterReading::where('connection_id', $conn->id)
                ->whereMonth('reading_date', $cm)->whereYear('reading_date', $cy)->where('is_verified', true)->first();
            if (!$reading) continue;
            if (Bill::where('connection_id', $conn->id)->where('billing_month', $cm)->where('billing_year', $cy)->exists()) continue;
            $t = $conn->tariffCategory;
            if (!$t) continue;

            $ec = $reading->units_consumed * $t->rate_per_unit;
            $fc = $conn->sanctioned_load_kw * $t->fixed_charge_per_kw;
            $tax = ($ec + $fc) * 0.05;

            Bill::create([
                'bill_number' => 'BILL-' . now()->format('Ym') . '-C' . $conn->id . '-' . $reading->id,
                'connection_id' => $conn->id, 'meter_reading_id' => $reading->id,
                'billing_month' => $cm, 'billing_year' => $cy, 'units_consumed' => $reading->units_consumed,
                'rate_per_unit' => $t->rate_per_unit, 'energy_charges' => $ec, 'fixed_charges' => $fc,
                'taxes' => $tax, 'subsidy_amount' => 0, 'net_payable' => $ec + $fc + $tax,
                'due_date' => Carbon::create($cy, $cm)->endOfMonth()->addDays(15),
                'status' => 'pending', 'generated_by' => Auth::id(),
            ]);
            $count++;
        }
        return back()->with('success', $count . ' bills generated.');
    }

    public function approveSubsidy($id)
    {
        ConsumerSubsidy::findOrFail($id)->update(['status' => 'approved', 'approved_by' => Auth::id(), 'approved_at' => now()]);
        return back()->with('success', 'Subsidy approved!');
    }

    public function rejectSubsidy(Request $request, $id)
    {
        ConsumerSubsidy::findOrFail($id)->update([
            'status' => 'rejected', 'approved_by' => Auth::id(), 'approved_at' => now(),
            'remarks' => $request->input('remarks', 'Rejected by SDO.'),
        ]);
        return back()->with('success', 'Subsidy rejected.');
    }
}
