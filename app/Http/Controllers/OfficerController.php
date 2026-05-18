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
use App\Models\PowerSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\BillGenerated;
use App\Mail\ConnectionApproved;
use App\Mail\ComplaintResolved;

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

        $schedules = PowerSchedule::where('zone_id', $user->zone_id)->orderBy('scheduled_date', 'desc')->get();

        return view('officer.dashboard', compact(
            'zone', 'pendingConnections', 'complaintsByStatus', 'pendingReadings',
            'monthlyRevenue', 'linemen', 'tariffCategories', 'complaints', 'pendingSubsidies', 'schedules'
        ));
    }

    public function approveConnection(Request $request, $id)
    {
        $request->validate(['tariff_category_id' => 'required|exists:tariff_categories,id']);
        
        $conn = \Illuminate\Support\Facades\DB::transaction(function() use ($request, $id) {
            $conn = Connection::where('id', $id)
                ->whereHas('consumer', fn($q) => $q->where('zone_id', Auth::user()->zone_id))
                ->lockForUpdate()
                ->firstOrFail();

            $last = Connection::where('meter_number', 'like', 'MT-%')
                ->orderByRaw('CAST(SUBSTRING(meter_number, 4) AS UNSIGNED) DESC')
                ->lockForUpdate()
                ->first();

            $nextNum = $last
                ? ((int) substr($last->meter_number, 3)) + 1
                : 10000;

            $conn->update([
                'tariff_category_id' => $request->tariff_category_id,
                'meter_number' => 'MT-' . $nextNum,
                'status' => 'active',
                'installation_date' => now(),
                'sdo_id' => Auth::id(),
            ]);

            return $conn;
        });

        $conn->load('consumer', 'tariffCategory');
        try {
            Mail::to($conn->consumer->email)->send(new ConnectionApproved($conn));
        } catch (\Exception $e) {}
        return back()->with('success', 'Connection ' . $conn->connection_number . ' approved!');
    }

    public function rejectConnection($id)
    {
        $conn = Connection::where('id', $id)
            ->whereHas('consumer', fn($q) => $q->where('zone_id', Auth::user()->zone_id))
            ->firstOrFail();
        $conn->update(['status' => 'rejected']);
        return back()->with('success', 'Connection ' . $conn->connection_number . ' rejected.');
    }

    public function assignComplaint(Request $request, $id)
    {
        $request->validate(['assigned_to' => 'required|exists:users,id']);
        $c = Complaint::where('id', $id)
            ->whereHas('consumer', fn($q) => $q->where('zone_id', Auth::user()->zone_id))
            ->firstOrFail();
        $c->update(['assigned_to' => $request->assigned_to, 'assigned_by' => Auth::id(), 'status' => 'assigned']);
        return back()->with('success', 'Complaint ' . $c->grv_number . ' assigned!');
    }

    public function resolveComplaint(Request $request, $id)
    {
        $c = Complaint::where('id', $id)
            ->whereHas('consumer', fn($q) => $q->where('zone_id', Auth::user()->zone_id))
            ->firstOrFail();
        $c->update(['status' => 'resolved', 'resolution_remarks' => $request->input('resolution_remarks', 'Resolved by SDO.'), 'resolved_at' => now()]);
        $c->load('consumer');
        try {
            Mail::to($c->consumer->email)->send(new ComplaintResolved($c));
        } catch (\Exception $e) {}
        return back()->with('success', 'Complaint resolved.');
    }

    public function verifyReading($id)
    {
        MeterReading::where('id', $id)
            ->whereHas('connection.consumer', fn($q) => $q->where('zone_id', Auth::user()->zone_id))
            ->firstOrFail()
            ->update(['is_verified' => true]);
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

            $approvedSubsidy = ConsumerSubsidy::where('consumer_id', $conn->consumer_id)
                ->where('status', 'approved')
                ->whereHas('scheme', fn($q) => $q->where('is_active', true)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now()))
                ->with('scheme')->first();

            $subsidyAmount = 0;
            if ($approvedSubsidy) {
                $coveredUnits = min($reading->units_consumed, $approvedSubsidy->scheme->max_units_covered);
                $subsidyAmount = $coveredUnits * $t->rate_per_unit
                                 * ($approvedSubsidy->scheme->discount_percentage / 100);
            }

            $bill = Bill::create([
                'bill_number' => 'BILL-' . now()->format('Ym') . '-C' . $conn->id . '-' . $reading->id,
                'connection_id' => $conn->id, 'meter_reading_id' => $reading->id,
                'billing_month' => $cm, 'billing_year' => $cy, 'units_consumed' => $reading->units_consumed,
                'rate_per_unit' => $t->rate_per_unit, 'energy_charges' => $ec, 'fixed_charges' => $fc,
                'taxes' => $tax, 'subsidy_amount' => $subsidyAmount, 'net_payable' => max(0, $ec + $fc + $tax - $subsidyAmount),
                'due_date' => Carbon::create($cy, $cm)->endOfMonth()->addDays(15),
                'status' => 'pending', 'generated_by' => Auth::id(),
            ]);
            $bill->load('connection.consumer');
            try {
                Mail::to($conn->consumer->email ?? $bill->connection->consumer->email)->send(new BillGenerated($bill));
            } catch (\Exception $e) {}
            $count++;
        }
        return back()->with('success', $count . ' bills generated.');
    }

    public function approveSubsidy($id)
    {
        ConsumerSubsidy::where('id', $id)
            ->whereHas('consumer', fn($q) => $q->where('zone_id', Auth::user()->zone_id))
            ->firstOrFail()
            ->update(['status' => 'approved', 'approved_by' => Auth::id(), 'approved_at' => now()]);
        return back()->with('success', 'Subsidy approved!');
    }

    public function rejectSubsidy(Request $request, $id)
    {
        ConsumerSubsidy::where('id', $id)
            ->whereHas('consumer', fn($q) => $q->where('zone_id', Auth::user()->zone_id))
            ->firstOrFail()
            ->update([
            'status' => 'rejected', 'approved_by' => Auth::id(), 'approved_at' => now(),
            'remarks' => $request->input('remarks', 'Rejected by SDO.'),
        ]);
        return back()->with('success', 'Subsidy rejected.');
    }

    public function storeSchedule(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scheduled_date' => 'required|date',
            'from_time' => 'required|date_format:H:i',
            'to_time' => 'required|date_format:H:i|after:from_time',
            'reason' => 'nullable|string|max:255',
        ]);

        PowerSchedule::create([
            'zone_id' => Auth::user()->zone_id,
            'title' => $request->title,
            'description' => $request->description,
            'scheduled_date' => $request->scheduled_date,
            'from_time' => $request->from_time,
            'to_time' => $request->to_time,
            'reason' => $request->reason,
            'posted_by' => Auth::id(),
        ]);

        return back()->with('success', 'Power schedule posted successfully.');
    }

    public function deleteSchedule($id)
    {
        $schedule = PowerSchedule::where('id', $id)->where('zone_id', Auth::user()->zone_id)->firstOrFail();
        $schedule->delete();
        return back()->with('success', 'Power schedule deleted.');
    }
}
