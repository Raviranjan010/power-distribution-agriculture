<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Connection;
use App\Models\Complaint;
use App\Models\MeterReading;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\ConsumerSubsidy;
use App\Models\SubsidyScheme;
use Carbon\Carbon;

class FarmerController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $connectionIds = Connection::where('consumer_id', $user->id)->pluck('id');
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $activeConnections = Connection::where('consumer_id', $user->id)->where('status', 'active')->count();
        $pendingConnections = Connection::where('consumer_id', $user->id)->where('status', 'pending')->count();
        $unitsThisMonth = MeterReading::whereIn('connection_id', $connectionIds)
            ->whereMonth('reading_date', $currentMonth)->whereYear('reading_date', $currentYear)->sum('units_consumed');
        $activeSubsidies = ConsumerSubsidy::where('consumer_id', $user->id)->where('status', 'approved')->count();
        $latestBill = Bill::whereIn('connection_id', $connectionIds)->where('status', 'pending')->orderByDesc('due_date')->first();
        $pendingBillsCount = Bill::whereIn('connection_id', $connectionIds)->where('status', 'pending')->count();

        $connections = Connection::where('consumer_id', $user->id)
            ->with(['tariffCategory', 'meterReadings' => fn($q) => $q->orderByDesc('reading_date')->limit(1)])->get();
        $complaints = Complaint::where('consumer_id', $user->id)->orderByDesc('filed_at')->limit(3)->get();

        $usageData = []; $usageLabels = [];
        for ($i = 11; $i >= 0; $i--) {
            $d = Carbon::now()->subMonths($i);
            $usageLabels[] = $d->format('M Y');
            $usageData[] = (float) MeterReading::whereIn('connection_id', $connectionIds)
                ->whereMonth('reading_date', $d->month)->whereYear('reading_date', $d->year)->sum('units_consumed');
        }

        $connectionUsage = [];
        foreach ($connections as $c) {
            $u = MeterReading::where('connection_id', $c->id)
                ->whereMonth('reading_date', $currentMonth)->whereYear('reading_date', $currentYear)->sum('units_consumed');
            $connectionUsage[] = ['name' => $c->field_name ?? $c->connection_number, 'type' => ucwords(str_replace('_', ' ', $c->connection_type)), 'units' => (float) $u];
        }

        $subsidies = ConsumerSubsidy::where('consumer_id', $user->id)->where('status', 'approved')->with('scheme')->get();

        $previousMonthUnits = MeterReading::whereIn('connection_id', $connectionIds)
            ->whereMonth('reading_date', Carbon::now()->subMonth()->month)
            ->whereYear('reading_date', Carbon::now()->subMonth()->year)
            ->sum('units_consumed');

        return view('farmer.dashboard', compact(
            'activeConnections', 'pendingConnections', 'unitsThisMonth', 'activeSubsidies', 'latestBill',
            'pendingBillsCount', 'connections', 'complaints', 'usageLabels', 'usageData',
            'connectionUsage', 'subsidies', 'previousMonthUnits'
        ));
    }

    public function storeConnection(Request $request)
    {
        $request->validate([
            'connection_type' => 'required|in:tubewell_pump,irrigation_motor,thresher,drip_irrigation',
            'field_name' => 'required|string|max:255',
            'sanctioned_load_kw' => 'required|numeric|min:1|max:50',
        ]);
        $user = Auth::user();
        $lastConn = Connection::orderByDesc('id')->first();
        $nextNum = $lastConn ? $lastConn->id + 1 : 1;
        $connectionNumber = 'KV-CN-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

        Connection::create([
            'connection_number' => $connectionNumber, 'consumer_id' => $user->id,
            'connection_type' => $request->connection_type, 'field_name' => $request->field_name,
            'sanctioned_load_kw' => $request->sanctioned_load_kw, 'status' => 'pending',
        ]);
        return back()->with('success', 'Connection request submitted! #' . $connectionNumber);
    }

    public function storeComplaint(Request $request)
    {
        $request->validate([
            'connection_id' => 'required|exists:connections,id',
            'complaint_type' => 'required|in:voltage_fluctuation,no_supply,meter_fault,billing_error,transformer_issue,other',
            'description' => 'required|string|max:1000',
            'priority' => 'required|in:low,medium,high',
        ]);
        $user = Auth::user();
        Connection::where('id', $request->connection_id)->where('consumer_id', $user->id)->firstOrFail();

        $year = date('Y');
        $last = Complaint::where('grv_number', 'like', "GRV-{$year}-%")->orderByDesc('id')->first();
        $nextNum = 1;
        if ($last && preg_match('/GRV-\d{4}-(\d+)/', $last->grv_number, $m)) $nextNum = intval($m[1]) + 1;
        $grv = "GRV-{$year}-" . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

        Complaint::create([
            'grv_number' => $grv, 'consumer_id' => $user->id, 'connection_id' => $request->connection_id,
            'complaint_type' => $request->complaint_type, 'description' => $request->description,
            'priority' => $request->priority, 'status' => 'filed', 'filed_at' => now(),
        ]);
        return back()->with('success', 'Complaint filed! GRV: ' . $grv);
    }

    public function bills()
    {
        $user = Auth::user();
        $ids = Connection::where('consumer_id', $user->id)->pluck('id');
        $bills = Bill::whereIn('connection_id', $ids)->with('connection')
            ->orderByDesc('billing_year')->orderByDesc('billing_month')->paginate(10);

        $totalOutstanding = Bill::whereIn('connection_id', $ids)->where('status', 'pending')->sum('net_payable');
        $totalPaidThisYear = Bill::whereIn('connection_id', $ids)->where('status', 'paid')
            ->where('billing_year', now()->year)->sum('net_payable');
        $nextDue = Bill::whereIn('connection_id', $ids)->where('status', 'pending')
            ->orderBy('due_date')->first();

        return view('farmer.bills', compact('bills', 'totalOutstanding', 'totalPaidThisYear', 'nextDue'));
    }

    public function payBill(Request $request, $id)
    {
        $user = Auth::user();
        $bill = Bill::findOrFail($id);
        // Verify this bill belongs to the user
        $conn = Connection::where('id', $bill->connection_id)->where('consumer_id', $user->id)->firstOrFail();

        if ($bill->status === 'paid') {
            return back()->withErrors(['payment' => 'This bill is already paid.']);
        }

        $txnId = 'TXN-' . now()->format('YmdHis') . '-' . $bill->id;

        Payment::create([
            'bill_id' => $bill->id,
            'consumer_id' => $user->id,
            'amount' => $bill->net_payable,
            'payment_method' => 'online',
            'transaction_id' => $txnId,
            'status' => 'success',
            'paid_at' => now(),
        ]);

        $bill->update(['status' => 'paid']);

        return back()->with('success', 'Payment successful! Transaction ID: ' . $txnId);
    }

    public function connections()
    {
        $user = Auth::user();
        $connections = Connection::where('consumer_id', $user->id)
            ->with(['tariffCategory', 'meterReadings' => fn($q) => $q->orderByDesc('reading_date')])->get();

        $totalActive = $connections->where('status', 'active')->count();
        $totalPending = $connections->where('status', 'pending')->count();
        $totalLoad = $connections->where('status', 'active')->sum('sanctioned_load_kw');

        return view('farmer.connections', compact('connections', 'totalActive', 'totalPending', 'totalLoad'));
    }

    public function complaints()
    {
        $user = Auth::user();
        $allComplaints = Complaint::where('consumer_id', $user->id)->with('connection')
            ->orderByDesc('filed_at')->paginate(10);

        $totalComplaints = Complaint::where('consumer_id', $user->id)->count();
        $openComplaints = Complaint::where('consumer_id', $user->id)->whereNotIn('status', ['resolved', 'closed'])->count();
        $resolvedComplaints = Complaint::where('consumer_id', $user->id)->whereIn('status', ['resolved', 'closed'])->count();

        $userConnections = Connection::where('consumer_id', $user->id)->get();

        return view('farmer.complaints', compact('allComplaints', 'totalComplaints', 'openComplaints', 'resolvedComplaints', 'userConnections'));
    }

    public function usage()
    {
        $user = Auth::user();
        $ids = Connection::where('consumer_id', $user->id)->pluck('id');
        $connections = Connection::where('consumer_id', $user->id)->where('status', 'active')->get();

        $labels = []; $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $d = Carbon::now()->subMonths($i);
            $labels[] = $d->format('M Y');
            $data[] = (float) MeterReading::whereIn('connection_id', $ids)
                ->whereMonth('reading_date', $d->month)->whereYear('reading_date', $d->year)->sum('units_consumed');
        }

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $currentMonthUnits = MeterReading::whereIn('connection_id', $ids)
            ->whereMonth('reading_date', $currentMonth)->whereYear('reading_date', $currentYear)->sum('units_consumed');
        $prevMonthUnits = MeterReading::whereIn('connection_id', $ids)
            ->whereMonth('reading_date', Carbon::now()->subMonth()->month)
            ->whereYear('reading_date', Carbon::now()->subMonth()->year)->sum('units_consumed');
        $totalThisYear = MeterReading::whereIn('connection_id', $ids)
            ->whereYear('reading_date', $currentYear)->sum('units_consumed');

        $connectionUsage = [];
        foreach ($connections as $c) {
            $monthlyData = [];
            for ($i = 5; $i >= 0; $i--) {
                $d = Carbon::now()->subMonths($i);
                $monthlyData[] = [
                    'month' => $d->format('M'),
                    'units' => (float) MeterReading::where('connection_id', $c->id)
                        ->whereMonth('reading_date', $d->month)->whereYear('reading_date', $d->year)->sum('units_consumed'),
                ];
            }
            $connectionUsage[] = [
                'connection' => $c,
                'monthly' => $monthlyData,
                'currentMonth' => (float) MeterReading::where('connection_id', $c->id)
                    ->whereMonth('reading_date', $currentMonth)->whereYear('reading_date', $currentYear)->sum('units_consumed'),
            ];
        }

        return view('farmer.usage', compact('labels', 'data', 'currentMonthUnits', 'prevMonthUnits', 'totalThisYear', 'connectionUsage'));
    }

    public function subsidies()
    {
        $availableSchemes = SubsidyScheme::where('is_active', true)->get();
        $mySubsidies = ConsumerSubsidy::where('consumer_id', Auth::id())->with('scheme')
            ->orderByDesc('applied_at')->get();
        return view('farmer.subsidies', compact('availableSchemes', 'mySubsidies'));
    }

    public function applySubsidy(Request $request)
    {
        $request->validate(['scheme_id' => 'required|exists:subsidy_schemes,id']);
        $existing = ConsumerSubsidy::where('consumer_id', Auth::id())->where('scheme_id', $request->scheme_id)->first();
        if ($existing) return back()->withErrors(['scheme_id' => 'Already applied.']);
        ConsumerSubsidy::create(['consumer_id' => Auth::id(), 'scheme_id' => $request->scheme_id, 'status' => 'applied', 'applied_at' => now()]);
        return back()->with('success', 'Subsidy application submitted!');
    }

    public function usageChart()
    {
        $ids = Connection::where('consumer_id', Auth::id())->pluck('id');
        $labels = []; $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $d = Carbon::now()->subMonths($i);
            $labels[] = $d->format('M Y');
            $data[] = (float) MeterReading::whereIn('connection_id', $ids)
                ->whereMonth('reading_date', $d->month)->whereYear('reading_date', $d->year)->sum('units_consumed');
        }
        return response()->json(['labels' => $labels, 'data' => $data]);
    }

    public function help()
    {
        return view('farmer.help');
    }
}
