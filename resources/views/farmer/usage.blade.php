@extends('layouts.app')

@section('content')
<div class="mb-8">
    <h2 class="text-3xl font-bold text-theme-heading mb-1">Usage Analytics</h2>
    <p class="text-sm text-theme-text">Track your electricity consumption patterns over time</p>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="utilitarian-card p-4 border-t border-t-emerald-500">
        <p class="text-[10px] text-theme-text font-bold tracking-widest uppercase mb-1">This Month</p>
        <p class="text-2xl font-bold text-emerald-400">{{ number_format($currentMonthUnits) }} <span class="text-sm font-medium text-theme-text">kWh</span></p>
    </div>
    <div class="utilitarian-card p-4 border-t border-t-amber-500">
        <p class="text-[10px] text-theme-text font-bold tracking-widest uppercase mb-1">Last Month</p>
        <p class="text-2xl font-bold text-amber-400">{{ number_format($prevMonthUnits) }} <span class="text-sm font-medium text-theme-text">kWh</span></p>
        @php
            $change = $prevMonthUnits > 0 ? (($currentMonthUnits - $prevMonthUnits) / $prevMonthUnits) * 100 : 0;
        @endphp
        @if($change != 0)
            <p class="text-[10px] mt-1 {{ $change > 0 ? 'text-rose-400' : 'text-emerald-400' }}">
                <i class="fa-solid fa-arrow-{{ $change > 0 ? 'up' : 'down' }}"></i>
                {{ abs(round($change, 1)) }}% vs this month
            </p>
        @endif
    </div>
    <div class="utilitarian-card p-4 border-t border-t-indigo-500">
        <p class="text-[10px] text-theme-text font-bold tracking-widest uppercase mb-1">Total This Year</p>
        <p class="text-2xl font-bold text-indigo-400">{{ number_format($totalThisYear) }} <span class="text-sm font-medium text-theme-text">kWh</span></p>
    </div>
</div>

{{-- Usage Chart --}}
<div class="utilitarian-card p-6 mb-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-sm font-bold text-theme-heading">12-Month Usage Trend</h3>
        <span class="text-xs text-theme-text">{{ now()->subMonths(11)->format('M Y') }} — {{ now()->format('M Y') }}</span>
    </div>
    <div style="height: 300px;">
        <canvas id="usageChart"></canvas>
    </div>
</div>

{{-- Per-Connection Breakdown --}}
<div class="utilitarian-card p-6">
    <h3 class="text-sm font-bold text-theme-heading mb-6">Per-Connection Breakdown</h3>

    @forelse($connectionUsage as $cu)
        <div class="mb-6 last:mb-0 pb-6 last:pb-0 border-b last:border-b-0 border-theme-border">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-[10px] text-theme-accent font-bold mb-0.5">{{ $cu['connection']->connection_number }}</p>
                    <h4 class="text-sm font-bold text-theme-heading">{{ ucwords(str_replace('_', ' ', $cu['connection']->connection_type)) }}</h4>
                    <p class="text-xs text-theme-text">{{ $cu['connection']->field_name }} · {{ $cu['connection']->sanctioned_load_kw }} kW</p>
                </div>
                <div class="text-right">
                    <p class="text-lg font-bold text-theme-heading">{{ number_format($cu['currentMonth']) }}</p>
                    <p class="text-[10px] text-theme-text uppercase">kWh this month</p>
                </div>
            </div>

            {{-- Mini bar chart for last 6 months --}}
            <div class="flex items-end gap-2 h-16">
                @php
                    $maxVal = max(array_column($cu['monthly'], 'units')) ?: 1;
                @endphp
                @foreach($cu['monthly'] as $m)
                    <div class="flex-1 flex flex-col items-center gap-1">
                        <div class="w-full rounded-t" style="height: {{ ($m['units'] / $maxVal) * 48 }}px; background-color: {{ $loop->last ? '#10b981' : '#1F2F24' }};"></div>
                        <span class="text-[9px] text-theme-text">{{ $m['month'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div class="text-center py-6">
            <i class="fa-solid fa-chart-line text-3xl text-theme-text mb-3"></i>
            <p class="text-sm text-theme-text">No active connections with usage data.</p>
        </div>
    @endforelse
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('usageChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($labels),
            datasets: [{
                label: 'Units Consumed (kWh)',
                data: @json($data),
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 2,
                pointRadius: 4,
                pointBackgroundColor: '#10b981',
                pointBorderColor: '#121C16',
                pointBorderWidth: 2,
                pointHoverRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#121C16',
                    borderColor: '#1F2F24',
                    borderWidth: 1,
                    titleColor: '#E5EDE8',
                    bodyColor: '#9AA8A0',
                    padding: 12,
                    displayColors: false,
                    callbacks: {
                        label: function(ctx) { return ctx.parsed.y.toLocaleString() + ' kWh'; }
                    }
                }
            },
            scales: {
                x: {
                    grid: { color: '#1F2F24', drawBorder: false },
                    ticks: { color: '#9AA8A0', font: { size: 10 }, maxRotation: 45 }
                },
                y: {
                    grid: { color: '#1F2F24', drawBorder: false },
                    ticks: { color: '#9AA8A0', font: { size: 10 }, callback: function(v) { return v.toLocaleString(); } },
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
@endsection
