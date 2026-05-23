<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Connection;
use App\Models\Complaint;
use App\Models\Bill;
use App\Models\MeterReading;
use App\Models\TariffCategory;
use App\Models\SubsidyScheme;
use App\Models\ConsumerSubsidy;
use App\Models\Zone;
use App\Models\AuditLog;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalFarmers = Cache::remember('stat.total_farmers', 300, fn() => User::where('role', 'farmer')->count());
        $totalActiveConnections = Cache::remember('stat.active_connections', 300, fn() => Connection::where('status', 'active')->count());
        $pendingComplaints = Cache::remember('stat.pending_complaints', 300, fn() => Complaint::whereNotIn('status', ['resolved', 'closed'])->count());
        $totalRevenueThisMonth = Cache::remember('stat.revenue_this_month', 300, fn() => Bill::where('status', 'paid')
            ->where('billing_month', now()->month)->where('billing_year', now()->year)->sum('net_payable'));

        $chartData = Cache::remember('stat.admin_charts', 300, function() {
            $revenueLabels = []; $revenueData = []; $connectionLabels = []; $connectionData = [];
            for ($i = 11; $i >= 0; $i--) {
                $d = Carbon::now()->subMonths($i);
                $revenueLabels[] = $d->format('M Y');
                $revenueData[] = (float) Bill::where('status', 'paid')
                    ->where('billing_month', $d->month)->where('billing_year', $d->year)->sum('net_payable');
                $connectionLabels[] = $d->format('M Y');
                $connectionData[] = Connection::whereYear('created_at', $d->year)->whereMonth('created_at', $d->month)->count();
            }
            return compact('revenueLabels', 'revenueData', 'connectionLabels', 'connectionData');
        });

        $revenueLabels = $chartData['revenueLabels'];
        $revenueData = $chartData['revenueData'];
        $connectionLabels = $chartData['connectionLabels'];
        $connectionData = $chartData['connectionData'];

        $zones = Cache::remember('stat.zones_overview', 300, function() {
            return Zone::with('sdo')->get()->map(function ($z) {
                return [
                    'name' => $z->name, 'district' => $z->district, 'sdo' => $z->sdo?->name ?? 'Unassigned',
                    'farmers' => User::where('zone_id', $z->id)->where('role', 'farmer')->count(),
                    'connections' => Connection::whereHas('consumer', fn($q) => $q->where('zone_id', $z->id))->where('status', 'active')->count(),
                ];
            });
        });

        $complaintStats = Cache::remember('stat.complaint_resolution', 300, function() {
            $total = Complaint::count();
            $resolved = Complaint::whereIn('status', ['resolved', 'closed'])->count();
            return [
                'resolutionRate' => $total > 0 ? round(($resolved / $total) * 100, 1) : 0
            ];
        });
        $resolutionRate = $complaintStats['resolutionRate'];

        $revenuePerZone = Cache::remember('stat.revenue_per_zone', 300, function() {
            return Zone::all()->map(function($zone) {
                $revenue = Bill::where('status', 'paid')
                    ->whereHas('connection.consumer', fn($q) => $q->where('zone_id', $zone->id))
                    ->sum('net_payable');
                return [
                    'zone' => $zone->name,
                    'revenue' => (float)$revenue
                ];
            });
        });

        $resolutionTimeTrend = Cache::remember('stat.resolution_time_trend', 300, function() {
            $data = [];
            for ($i = 5; $i >= 0; $i--) {
                $d = Carbon::now()->subMonths($i);
                $complaints = Complaint::where('status', 'resolved')
                    ->whereMonth('resolved_at', $d->month)
                    ->whereYear('resolved_at', $d->year)
                    ->get();
                
                $avgHours = 0;
                if ($complaints->count() > 0) {
                    $totalHours = 0;
                    foreach ($complaints as $c) {
                        $totalHours += Carbon::parse($c->filed_at)->diffInHours(Carbon::parse($c->resolved_at));
                    }
                    $avgHours = round($totalHours / $complaints->count(), 1);
                }
                $data[] = [
                    'label' => $d->format('M Y'),
                    'avg_hours' => $avgHours
                ];
            }
            return $data;
        });

        $linemanPerformance = Cache::remember('stat.lineman_performance', 300, function() {
            $linemen = User::where('role', 'lineman')->get();
            return $linemen->map(function($lm) {
                $totalReadings = MeterReading::where('lineman_id', $lm->id)->count();
                $verifiedReadings = MeterReading::where('lineman_id', $lm->id)->where('is_verified', true)->count();
                $readingsThisMonth = MeterReading::where('lineman_id', $lm->id)
                    ->whereMonth('reading_date', now()->month)
                    ->whereYear('reading_date', now()->year)
                    ->count();
                
                return [
                    'name' => $lm->name,
                    'readings_this_month' => $readingsThisMonth,
                    'verification_rate' => $totalReadings > 0 ? round(($verifiedReadings / $totalReadings) * 100, 1) : 0.0
                ];
            });
        });

        $agingBuckets = Cache::remember('stat.aging_buckets', 300, function() {
            $now = Carbon::now();
            $bills = Bill::whereIn('status', ['pending', 'overdue'])->get();

            $bucket1 = 0; // 0-30 days
            $bucket2 = 0; // 30-60 days
            $bucket3 = 0; // 60-90 days
            $bucket4 = 0; // 90d+

            foreach ($bills as $b) {
                $dueDate = Carbon::parse($b->due_date);
                if ($dueDate->isPast()) {
                    $daysPast = $dueDate->diffInDays($now);
                    if ($daysPast <= 30) {
                        $bucket1 += $b->net_payable;
                    } elseif ($daysPast <= 60) {
                        $bucket2 += $b->net_payable;
                    } elseif ($daysPast <= 90) {
                        $bucket3 += $b->net_payable;
                    } else {
                        $bucket4 += $b->net_payable;
                    }
                }
            }
            return [
                '0_30' => (float)$bucket1,
                '30_60' => (float)$bucket2,
                '60_90' => (float)$bucket3,
                '90_plus' => (float)$bucket4
            ];
        });

        return view('admin.dashboard', compact(
            'totalFarmers', 'totalActiveConnections', 'pendingComplaints', 'totalRevenueThisMonth',
            'revenueLabels', 'revenueData', 'connectionLabels', 'connectionData', 'zones', 'resolutionRate',
            'revenuePerZone', 'resolutionTimeTrend', 'linemanPerformance', 'agingBuckets'
        ));
    }

    public function users()
    {
        $users = User::orderByDesc('created_at')->paginate(20);
        $zones = Zone::all();
        return view('admin.users', compact('users', 'zones'));
    }

    public function toggleUserStatus($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => !$user->is_active]);
        AuditLog::create([
            'user_id' => Auth::id(), 'action' => $user->is_active ? 'activated' : 'deactivated',
            'model_type' => 'User', 'model_id' => $user->id, 'ip_address' => request()->ip(),
        ]);
        return back()->with('success', 'User ' . $user->name . ' ' . ($user->is_active ? 'activated' : 'deactivated'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:sdo,lineman,admin',
            'zone_id' => 'required_if:role,sdo,lineman|nullable|exists:zones,id',
            'phone' => 'required|string|max:15',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role' => $request->role,
            'zone_id' => $request->zone_id,
            'phone' => $request->phone,
            'is_active' => true,
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'created_user',
            'model_type' => 'User',
            'model_id' => $user->id,
            'new_values' => ['name' => $user->name, 'role' => $user->role, 'email' => $user->email],
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', 'User ' . $user->name . ' created successfully.');
    }

    public function tariffs()
    {
        $tariffs = TariffCategory::orderByDesc('effective_from')->get();
        return view('admin.tariffs', compact('tariffs'));
    }

    public function storeTariff(Request $request)
    {
        $request->validate([
            'name' => 'required|string', 'rate_per_unit' => 'required|numeric|min:0',
            'fixed_charge_per_kw' => 'required|numeric|min:0',
            'applicable_to' => 'required|in:agricultural,domestic,commercial', 'effective_from' => 'required|date',
        ]);
        TariffCategory::create($request->only(['name', 'rate_per_unit', 'fixed_charge_per_kw', 'applicable_to', 'effective_from']));
        AuditLog::create([
            'user_id' => Auth::id(), 'action' => 'created_tariff', 'model_type' => 'TariffCategory',
            'new_values' => $request->all(), 'ip_address' => request()->ip(),
        ]);
        return back()->with('success', 'Tariff created.');
    }

    public function updateTariff(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string', 'rate_per_unit' => 'required|numeric|min:0',
            'fixed_charge_per_kw' => 'required|numeric|min:0',
            'applicable_to' => 'required|in:agricultural,domestic,commercial', 'effective_from' => 'required|date',
        ]);
        $tariff = TariffCategory::findOrFail($id);
        $oldValues = $tariff->toArray();
        $tariff->update($request->only(['name', 'rate_per_unit', 'fixed_charge_per_kw', 'applicable_to', 'effective_from']));
        AuditLog::create([
            'user_id' => Auth::id(), 'action' => 'updated_tariff', 'model_type' => 'TariffCategory',
            'model_id' => $id, 'old_values' => $oldValues, 'new_values' => $request->all(), 'ip_address' => request()->ip(),
        ]);
        return back()->with('success', 'Tariff updated.');
    }

    public function deleteTariff($id)
    {
        $tariff = TariffCategory::findOrFail($id);
        $tariff->update(['is_active' => false]);
        AuditLog::create([
            'user_id' => Auth::id(), 'action' => 'deactivated_tariff', 'model_type' => 'TariffCategory',
            'model_id' => $id, 'ip_address' => request()->ip(),
        ]);
        return back()->with('success', 'Tariff deactivated.');
    }

    public function subsidySchemes()
    {
        $schemes = SubsidyScheme::orderByDesc('created_at')->get();
        return view('admin.subsidies', compact('schemes'));
    }

    public function storeSubsidyScheme(Request $request)
    {
        $request->validate([
            'scheme_name' => 'required|string', 'description' => 'required|string',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'max_units_covered' => 'required|numeric|min:0',
            'start_date' => 'required|date', 'end_date' => 'required|date|after:start_date',
        ]);
        SubsidyScheme::create($request->only([
            'scheme_name', 'description', 'discount_percentage', 'max_units_covered', 'start_date', 'end_date'
        ]));
        return back()->with('success', 'Scheme created.');
    }

    public function updateSubsidyScheme(Request $request, $id)
    {
        $request->validate([
            'scheme_name' => 'required|string', 'description' => 'required|string',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'max_units_covered' => 'required|numeric|min:0',
            'start_date' => 'required|date', 'end_date' => 'required|date|after:start_date',
        ]);
        $scheme = SubsidyScheme::findOrFail($id);
        $scheme->update($request->only([
            'scheme_name', 'description', 'discount_percentage', 'max_units_covered', 'start_date', 'end_date'
        ]));
        return back()->with('success', 'Scheme updated.');
    }

    public function deleteSubsidyScheme($id)
    {
        $scheme = SubsidyScheme::findOrFail($id);
        $scheme->update(['is_active' => false]);
        return back()->with('success', 'Scheme deactivated.');
    }

    public function auditLogs()
    {
        $logs = AuditLog::with('user')->orderByDesc('created_at')->paginate(20);
        return view('admin.audit_logs', compact('logs'));
    }

    public function zones()
    {
        $zones = Zone::orderBy('state')->orderBy('district')->get();
        return view('admin.zones', compact('zones'));
    }

    public function storeZone(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'sdo_id' => 'nullable|exists:users,id',
        ]);

        Zone::create([
            'name' => $request->name,
            'district' => $request->district,
            'state' => $request->state,
            'sdo_id' => $request->sdo_id,
        ]);

        return back()->with('success', 'Zone created successfully!');
    }

    public function updateZone(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'sdo_id' => 'nullable|exists:users,id',
        ]);

        $zone = Zone::findOrFail($id);
        $zone->update([
            'name' => $request->name,
            'district' => $request->district,
            'state' => $request->state,
            'sdo_id' => $request->sdo_id,
        ]);

        return back()->with('success', 'Zone updated successfully!');
    }

    public function deleteZone($id)
    {
        $zone = Zone::findOrFail($id);
        $zone->delete();

        return back()->with('success', 'Zone deleted successfully!');
    }

    public function assignSdo(Request $request, $id)
    {
        $request->validate([
            'sdo_id' => 'nullable|exists:users,id',
        ]);

        $zone = Zone::findOrFail($id);
        $zone->update([
            'sdo_id' => $request->sdo_id,
        ]);

        return back()->with('success', 'SDO assigned to zone successfully!');
    }

    public function exportReport(Request $request)
    {
        $type = $request->query('type');
        $data = [];
        $headers = [];

        if ($type === 'analytics_pdf') {
            $user = Auth::user();
            $adminName = $user->name;

            $totalFarmers = Cache::remember('stat.total_farmers', 300, fn() => User::where('role', 'farmer')->count());
            $totalActiveConnections = Cache::remember('stat.active_connections', 300, fn() => Connection::where('status', 'active')->count());
            $pendingComplaints = Cache::remember('stat.pending_complaints', 300, fn() => Complaint::whereNotIn('status', ['resolved', 'closed'])->count());
            $totalRevenueThisMonth = Cache::remember('stat.revenue_this_month', 300, fn() => Bill::where('status', 'paid')
                ->where('billing_month', now()->month)->where('billing_year', now()->year)->sum('net_payable'));

            $revenuePerZone = Cache::remember('stat.revenue_per_zone', 300, function() {
                return Zone::all()->map(function($zone) {
                    $revenue = Bill::where('status', 'paid')
                        ->whereHas('connection.consumer', fn($q) => $q->where('zone_id', $zone->id))
                        ->sum('net_payable');
                    return [
                        'zone' => $zone->name,
                        'revenue' => (float)$revenue
                    ];
                });
            });

            $resolutionTimeTrend = Cache::remember('stat.resolution_time_trend', 300, function() {
                $data = [];
                for ($i = 5; $i >= 0; $i--) {
                    $d = Carbon::now()->subMonths($i);
                    $complaints = Complaint::where('status', 'resolved')
                        ->whereMonth('resolved_at', $d->month)
                        ->whereYear('resolved_at', $d->year)
                        ->get();
                    
                    $avgHours = 0;
                    if ($complaints->count() > 0) {
                        $totalHours = 0;
                        foreach ($complaints as $c) {
                            $totalHours += Carbon::parse($c->filed_at)->diffInHours(Carbon::parse($c->resolved_at));
                        }
                        $avgHours = round($totalHours / $complaints->count(), 1);
                    }
                    $data[] = [
                        'label' => $d->format('M Y'),
                        'avg_hours' => $avgHours
                    ];
                }
                return $data;
            });

            $linemanPerformance = Cache::remember('stat.lineman_performance', 300, function() {
                $linemen = User::where('role', 'lineman')->get();
                return $linemen->map(function($lm) {
                    $totalReadings = MeterReading::where('lineman_id', $lm->id)->count();
                    $verifiedReadings = MeterReading::where('lineman_id', $lm->id)->where('is_verified', true)->count();
                    $readingsThisMonth = MeterReading::where('lineman_id', $lm->id)
                        ->whereMonth('reading_date', now()->month)
                        ->whereYear('reading_date', now()->year)
                        ->count();
                    
                    return [
                        'name' => $lm->name,
                        'readings_this_month' => $readingsThisMonth,
                        'verification_rate' => $totalReadings > 0 ? round(($verifiedReadings / $totalReadings) * 100, 1) : 0.0
                    ];
                });
            });

            $agingBuckets = Cache::remember('stat.aging_buckets', 300, function() {
                $now = Carbon::now();
                $bills = Bill::whereIn('status', ['pending', 'overdue'])->get();

                $bucket1 = 0; // 0-30 days
                $bucket2 = 0; // 30-60 days
                $bucket3 = 0; // 60-90 days
                $bucket4 = 0; // 90d+

                foreach ($bills as $b) {
                    $dueDate = Carbon::parse($b->due_date);
                    if ($dueDate->isPast()) {
                        $daysPast = $dueDate->diffInDays($now);
                        if ($daysPast <= 30) {
                            $bucket1 += $b->net_payable;
                        } elseif ($daysPast <= 60) {
                            $bucket2 += $b->net_payable;
                        } elseif ($daysPast <= 90) {
                            $bucket3 += $b->net_payable;
                        } else {
                            $bucket4 += $b->net_payable;
                        }
                    }
                }
                return [
                    '0_30' => (float)$bucket1,
                    '30_60' => (float)$bucket2,
                    '60_90' => (float)$bucket3,
                    '90_plus' => (float)$bucket4
                ];
            });

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.analytics_pdf', compact(
                'adminName', 'totalFarmers', 'totalActiveConnections', 'pendingComplaints', 'totalRevenueThisMonth',
                'revenuePerZone', 'resolutionTimeTrend', 'linemanPerformance', 'agingBuckets'
            ));

            return $pdf->download('executive-analytics-report-' . now()->format('Y-m-d') . '.pdf');
        }

        if ($type === 'farmers') {
            $data = User::where('role', 'farmer')->get(['name', 'email', 'phone', 'village', 'district', 'farmer_id_number']);
            $headers = ['Name', 'Email', 'Phone', 'Village', 'District', 'Farmer ID'];
        } elseif ($type === 'connections') {
            $data = Connection::with('consumer')->get()->map(function(Connection $c) {
                return [
                    'connection_number' => $c->connection_number,
                    'farmer_name' => $c->consumer->name ?? '',
                    'type' => $c->connection_type,
                    'load' => $c->sanctioned_load_kw,
                    'status' => $c->status,
                ];
            });
            $headers = ['Connection Number', 'Farmer Name', 'Type', 'Load (kW)', 'Status'];
        } elseif ($type === 'bills') {
            $cm = now()->month;
            $cy = now()->year;
            $data = Bill::with('connection.consumer')->where('billing_month', $cm)->where('billing_year', $cy)->get()->map(function(Bill $b) {
                return [
                    'bill_number' => $b->bill_number,
                    'farmer_name' => $b->connection->consumer->name ?? '',
                    'units' => $b->units_consumed,
                    'amount' => $b->net_payable,
                    'status' => $b->status,
                ];
            });
            $headers = ['Bill Number', 'Farmer Name', 'Units', 'Amount', 'Status'];
        } elseif ($type === 'payments') {
            $data = Payment::with('bill.connection.consumer')->get()->map(function(Payment $p) {
                return [
                    'transaction_id' => $p->transaction_id,
                    'farmer_name' => $p->bill->connection->consumer->name ?? '',
                    'amount' => $p->amount,
                    'method' => $p->payment_method,
                    'date' => $p->paid_at?->format('d/m/Y'),
                ];
            });
            $headers = ['Transaction ID', 'Farmer Name', 'Amount', 'Method', 'Date'];
        } else {
            return back()->with('error', 'Invalid report type.');
        }

        return response()->streamDownload(function() use ($data, $headers) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $headers);
            foreach ($data as $row) {
                fputcsv($out, is_array($row) ? $row : $row->toArray());
            }
            fclose($out);
        }, 'report-'.$type.'-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv']);
    }
}
