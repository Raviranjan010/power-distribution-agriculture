<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Connection;
use App\Models\Complaint;
use App\Models\MeterReading;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\ConsumerSubsidy;
use App\Models\SubsidyScheme;
use App\Models\PowerSchedule;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

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

        $schedules = PowerSchedule::where('zone_id', $user->zone_id)
            ->where('scheduled_date', '>=', today())->orderBy('scheduled_date')->get();

        return view('farmer.dashboard', compact(
            'activeConnections', 'pendingConnections', 'unitsThisMonth', 'activeSubsidies', 'latestBill',
            'pendingBillsCount', 'connections', 'complaints', 'usageLabels', 'usageData',
            'connectionUsage', 'subsidies', 'previousMonthUnits', 'schedules'
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

        $connection = DB::transaction(function() use ($request, $user) {
            // Use max connection_number suffix instead of id
            $last = Connection::lockForUpdate()
                ->where('connection_number', 'like', 'KV-CN-%')
                ->orderByRaw('CAST(SUBSTRING(connection_number, 7) AS UNSIGNED) DESC')
                ->first();
            $nextNum = $last
                ? ((int) substr($last->connection_number, 6)) + 1
                : 1;
            $connectionNumber = 'KV-CN-' . str_pad($nextNum, 5, '0', STR_PAD_LEFT);
            return Connection::create([
                'connection_number' => $connectionNumber, 'consumer_id' => $user->id,
                'connection_type' => $request->connection_type, 'field_name' => $request->field_name,
                'sanctioned_load_kw' => $request->sanctioned_load_kw, 'status' => 'pending',
            ]);
        });

        // Notify SDOs in the same zone
        $officers = \App\Models\User::where('role', 'sdo')->where('zone_id', $user->zone_id)->get();
        foreach ($officers as $officer) {
            $officer->notify(new \App\Notifications\RealTimeNotification(
                'New Connection Request',
                'Farmer ' . $user->name . ' has requested a new connection: ' . $connection->connection_number,
                route('officer.dashboard'),
                'fa-solid fa-file-signature'
            ));
        }

        return back()->with('success', 'Connection request submitted! #' . $connection->connection_number);
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

        $grv = DB::transaction(function() use ($request, $user) {
            $year = date('Y');
            $last = Complaint::lockForUpdate()
                ->where('grv_number', 'like', "GRV-{$year}-%")
                ->orderByDesc('id')->first();
            $nextNum = 1;
            if ($last && preg_match('/GRV-\d{4}-(\d+)/', $last->grv_number, $m)) $nextNum = intval($m[1]) + 1;
            $grv = "GRV-{$year}-" . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

            Complaint::create([
                'grv_number' => $grv, 'consumer_id' => $user->id, 'connection_id' => $request->connection_id,
                'complaint_type' => $request->complaint_type, 'description' => $request->description,
                'priority' => $request->priority, 'status' => 'filed', 'filed_at' => now(),
            ]);

            return $grv;
        });

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

    public function payConfirm($id)
    {
        $user = Auth::user();
        $bill = Bill::findOrFail($id);
        $conn = Connection::where('id', $bill->connection_id)->where('consumer_id', $user->id)->firstOrFail();

        if ($bill->status === 'paid') {
            return redirect()->route('farmer.bills')->withErrors(['payment' => 'This bill is already paid.']);
        }

        $razorpayOrderId = null;
        if (config('services.razorpay.key') && config('services.razorpay.secret') && class_exists('\Razorpay\Api\Api')) {
            try {
                $api = new \Razorpay\Api\Api(config('services.razorpay.key'), config('services.razorpay.secret'));
                $order = $api->order->create([
                    'receipt' => (string)$bill->id,
                    'amount' => $bill->net_payable * 100,
                    'currency' => 'INR'
                ]);
                $razorpayOrderId = $order['id'];
            } catch (\Exception $e) {
                $razorpayOrderId = null;
            }
        }

        return view('farmer.pay_confirm', compact('bill', 'conn', 'razorpayOrderId'));
    }

    public function payBill(Request $request, $id)
    {
        $user = Auth::user();
        return DB::transaction(function() use ($request, $id, $user) {
            $bill = Bill::lockForUpdate()->findOrFail($id);
            Connection::where('id', $bill->connection_id)
                      ->where('consumer_id', $user->id)->firstOrFail();
            if ($bill->status === 'paid') {
                return redirect()->route('farmer.bills')
                       ->withErrors(['payment' => 'This bill is already paid.']);
            }

            if ($request->has('razorpay_payment_id') && config('services.razorpay.key') && config('services.razorpay.secret') && class_exists('\Razorpay\Api\Api')) {
                $api = new \Razorpay\Api\Api(config('services.razorpay.key'), config('services.razorpay.secret'));
                try {
                    $attributes = [
                        'razorpay_order_id' => $request->razorpay_order_id,
                        'razorpay_payment_id' => $request->razorpay_payment_id,
                        'razorpay_signature' => $request->razorpay_signature
                    ];
                    $api->utility->verifyPaymentSignature($attributes);
                    $txnId = $request->razorpay_payment_id;
                } catch(\Exception $e) {
                    return back()->withErrors(['payment' => 'Payment verification failed.']);
                }
            } else {
                sleep(1);
                $txnId = 'TXN-' . now()->format('YmdHis') . '-' . $bill->id;
            }

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

            return redirect()->route('farmer.bills')->with('success', 'Payment successful! Transaction ID: ' . $txnId);
        });
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
        $allComplaints = Complaint::where('consumer_id', $user->id)->with(['connection', 'assignedTo'])
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
        $request->validate([
            'scheme_id' => 'required|exists:subsidy_schemes,id',
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        $existing = ConsumerSubsidy::where('consumer_id', Auth::id())->where('scheme_id', $request->scheme_id)->first();
        if ($existing) return back()->withErrors(['scheme_id' => 'Already applied.']);

        $path = null;
        if ($request->hasFile('document')) {
            $path = $request->file('document')->store('subsidies', 'public');
        }

        ConsumerSubsidy::create([
            'consumer_id' => Auth::id(),
            'scheme_id' => $request->scheme_id,
            'document_path' => $path,
            'status' => 'applied',
            'applied_at' => now()
        ]);

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

    public function downloadBill($id)
    {
        $user = Auth::user();
        $bill = Bill::with(['connection.consumer', 'connection.tariffCategory'])->findOrFail($id);
        Connection::where('id', $bill->connection_id)->where('consumer_id', $user->id)->firstOrFail();

        $pdf = Pdf::loadView('farmer.bill_pdf', compact('bill'));
        return $pdf->download('Bill-' . $bill->bill_number . '.pdf');
    }

    public function profile()
    {
        return view('farmer.profile', ['user' => Auth::user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'phone' => 'required|digits:10',
            'address' => 'required|string|max:255',
            'village' => 'required|string|max:255',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->village = $request->village;

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }
}
