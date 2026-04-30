@extends('layouts.app')

@section('content')
<div class="mb-8">
    <h2 class="text-3xl font-bold text-white mb-2">Admin Dashboard</h2>
    <p class="text-slate-400">System Overview | Ministry of Power - Agriculture Distribution</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Stat 1 -->
    <div class="glass-panel rounded-2xl p-6 border-t-4 border-t-brand-500">
        <p class="text-sm text-slate-400 font-medium mb-1">Total Farmers Registered</p>
        <h3 class="text-3xl font-bold text-white">{{ $stats['total_farmers'] }}</h3>
    </div>
    <!-- Stat 2 -->
    <div class="glass-panel rounded-2xl p-6 border-t-4 border-t-blue-500">
        <p class="text-sm text-slate-400 font-medium mb-1">Total Connections</p>
        <h3 class="text-3xl font-bold text-white">{{ $stats['total_connections'] }}</h3>
    </div>
    <!-- Stat 3 -->
    <div class="glass-panel rounded-2xl p-6 border-t-4 border-t-yellow-500">
        <p class="text-sm text-slate-400 font-medium mb-1">Pending Requests</p>
        <h3 class="text-3xl font-bold text-white">{{ $stats['pending_connections'] }}</h3>
    </div>
    <!-- Stat 4 -->
    <div class="glass-panel rounded-2xl p-6 border-t-4 border-t-red-500">
        <p class="text-sm text-slate-400 font-medium mb-1">Open Complaints</p>
        <h3 class="text-3xl font-bold text-white">{{ $stats['open_complaints'] }}</h3>
    </div>
</div>

<div class="glass-panel rounded-2xl p-6 mb-8">
    <h3 class="text-xl font-bold text-white mb-6">New Connections per Month</h3>
    <div class="h-64 w-full">
        <canvas id="adminChart"></canvas>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('adminChart').getContext('2d');
        const months = @json($months);
        const data = @json($chartData);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                    label: 'Connections Registered',
                    data: data,
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#94a3b8', stepSize: 1 } },
                    x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
                }
            }
        });
    });
</script>
@endsection
