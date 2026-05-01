@extends('layouts.app')

@section('content')
<div class="mb-8">
    <h2 class="text-3xl font-bold text-theme-heading mb-1">Admin Dashboard</h2>
    <p class="text-sm text-theme-text">System Overview · Ministry of Power · Agriculture Distribution · {{ now()->format('d M Y') }}</p>
</div>

<!-- Stats Row -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="utilitarian-card p-5 border-t border-t-emerald-500">
        <p class="text-[10px] text-theme-text font-bold tracking-widest uppercase mb-1">Total<br>Farmers</p>
        <h3 class="text-4xl font-bold text-emerald-400">{{ number_format($totalFarmers) }}</h3>
    </div>
    <div class="utilitarian-card p-5 border-t border-t-indigo-500">
        <p class="text-[10px] text-theme-text font-bold tracking-widest uppercase mb-1">Active<br>Connections</p>
        <h3 class="text-4xl font-bold text-indigo-400">{{ number_format($totalActiveConnections) }}</h3>
    </div>
    <div class="utilitarian-card p-5 border-t border-t-amber-500">
        <p class="text-[10px] text-theme-text font-bold tracking-widest uppercase mb-1">Revenue<br>This Month</p>
        <h3 class="text-4xl font-bold text-amber-400">₹{{ number_format($totalRevenueThisMonth, 0) }}</h3>
    </div>
    <div class="utilitarian-card p-5 border-t border-t-rose-500">
        <p class="text-[10px] text-theme-text font-bold tracking-widest uppercase mb-1">Pending<br>Complaints</p>
        <h3 class="text-4xl font-bold text-rose-400">{{ number_format($pendingComplaints) }}</h3>
        <p class="text-xs text-theme-text">{{ $resolutionRate }}% resolution rate</p>
    </div>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Revenue Trend -->
    <div class="utilitarian-card p-6">
        <h3 class="text-sm font-bold text-theme-heading mb-4">Monthly Revenue Trend</h3>
        <div class="h-56">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>
    <!-- Connection Growth -->
    <div class="utilitarian-card p-6">
        <h3 class="text-sm font-bold text-theme-heading mb-4">Connection Growth</h3>
        <div class="h-56">
            <canvas id="connectionChart"></canvas>
        </div>
    </div>
</div>

<!-- Zone Summary -->
<div class="utilitarian-card p-6">
    <h3 class="text-lg font-bold text-theme-heading mb-6">Zone-wise Summary</h3>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[10px] text-theme-text font-bold tracking-widest uppercase border-b border-theme-border">
                    <th class="pb-3 pr-4">Zone</th>
                    <th class="pb-3 pr-4">District</th>
                    <th class="pb-3 pr-4">SDO</th>
                    <th class="pb-3 pr-4 text-right">Farmers</th>
                    <th class="pb-3 text-right">Connections</th>
                </tr>
            </thead>
            <tbody>
                @foreach($zones as $z)
                    <tr class="border-b border-theme-border/50 hover:bg-theme-border/20 transition-colors">
                        <td class="py-3 pr-4 font-medium text-theme-heading">{{ $z['name'] }}</td>
                        <td class="py-3 pr-4 text-theme-text">{{ $z['district'] }}</td>
                        <td class="py-3 pr-4 text-theme-text">{{ $z['sdo'] }}</td>
                        <td class="py-3 pr-4 text-right font-bold text-theme-heading">{{ $z['farmers'] }}</td>
                        <td class="py-3 text-right font-bold text-theme-heading">{{ $z['connections'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartOpts = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#9AA8A0' } },
            x: { grid: { display: false }, ticks: { color: '#9AA8A0', maxRotation: 45 } }
        }
    };

    new Chart(document.getElementById('revenueChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: @json($revenueLabels),
            datasets: [{
                label: 'Revenue (₹)',
                data: @json($revenueData),
                borderColor: 'rgba(245, 158, 11, 1)',
                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 2,
                pointRadius: 3,
                pointBackgroundColor: 'rgba(245, 158, 11, 1)',
            }]
        },
        options: chartOpts
    });

    new Chart(document.getElementById('connectionChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: @json($connectionLabels),
            datasets: [{
                label: 'New Connections',
                data: @json($connectionData),
                backgroundColor: 'rgba(99, 102, 241, 0.6)',
                borderColor: 'rgba(99, 102, 241, 1)',
                borderWidth: 1,
                borderRadius: 4,
            }]
        },
        options: chartOpts
    });
});
</script>
@endsection
