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
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalFarmers = User::where('role', 'farmer')->count();
        $totalActiveConnections = Connection::where('status', 'active')->count();
        $pendingComplaints = Complaint::whereNotIn('status', ['resolved', 'closed'])->count();
        $totalRevenueThisMonth = Bill::where('status', 'paid')
            ->where('billing_month', now()->month)->where('billing_year', now()->year)->sum('net_payable');

        $revenueLabels = []; $revenueData = []; $connectionLabels = []; $connectionData = [];
        for ($i = 11; $i >= 0; $i--) {
            $d = Carbon::now()->subMonths($i);
            $revenueLabels[] = $d->format('M Y');
            $revenueData[] = (float) Bill::where('status', 'paid')
                ->where('billing_month', $d->month)->where('billing_year', $d->year)->sum('net_payable');
            $connectionLabels[] = $d->format('M Y');
            $connectionData[] = Connection::whereYear('created_at', $d->year)->whereMonth('created_at', $d->month)->count();
        }

        $zones = Zone::with('sdo')->get()->map(function ($z) {
            return [
                'name' => $z->name, 'district' => $z->district, 'sdo' => $z->sdo?->name ?? 'Unassigned',
                'farmers' => User::where('zone_id', $z->id)->where('role', 'farmer')->count(),
                'connections' => Connection::whereHas('consumer', fn($q) => $q->where('zone_id', $z->id))->where('status', 'active')->count(),
            ];
        });

        $total = Complaint::count();
        $resolved = Complaint::whereIn('status', ['resolved', 'closed'])->count();
        $resolutionRate = $total > 0 ? round(($resolved / $total) * 100, 1) : 0;

        return view('admin.dashboard', compact(
            'totalFarmers', 'totalActiveConnections', 'pendingComplaints', 'totalRevenueThisMonth',
            'revenueLabels', 'revenueData', 'connectionLabels', 'connectionData', 'zones', 'resolutionRate'
        ));
    }

    public function users()
    {
        $users = User::orderByDesc('created_at')->paginate(20);
        return view('admin.users', compact('users'));
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

    public function auditLogs()
    {
        $logs = AuditLog::with('user')->orderByDesc('created_at')->paginate(20);
        return view('admin.audit_logs', compact('logs'));
    }
}
