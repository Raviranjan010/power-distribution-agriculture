@extends('layouts.app')

@section('content')
<div class="mb-8">
    <div class="flex justify-between items-start">
        <div>
            <h2 class="text-3xl font-bold text-theme-heading mb-1">My Connections</h2>
            <p class="text-sm text-theme-text">Manage your agricultural power connections</p>
        </div>
        <button onclick="document.getElementById('connectionModal').classList.remove('hidden')" class="border border-theme-border hover:bg-theme-border text-theme-heading text-xs font-bold px-4 py-2.5 rounded-lg transition-colors flex items-center gap-2">
            <i class="fa-solid fa-plus text-theme-accent"></i> Request New
        </button>
    </div>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="utilitarian-card p-4 border-t border-t-emerald-500">
        <p class="text-[10px] text-theme-text font-bold tracking-widest uppercase mb-1">Active</p>
        <p class="text-2xl font-bold text-emerald-400">{{ $totalActive }}</p>
    </div>
    <div class="utilitarian-card p-4 border-t border-t-amber-500">
        <p class="text-[10px] text-theme-text font-bold tracking-widest uppercase mb-1">Pending</p>
        <p class="text-2xl font-bold text-amber-400">{{ $totalPending }}</p>
    </div>
    <div class="utilitarian-card p-4 border-t border-t-indigo-500">
        <p class="text-[10px] text-theme-text font-bold tracking-widest uppercase mb-1">Total Load</p>
        <p class="text-2xl font-bold text-indigo-400">{{ number_format($totalLoad, 1) }} <span class="text-sm font-medium text-theme-text">kW</span></p>
    </div>
</div>

@forelse($connections as $conn)
    <div class="utilitarian-card p-6 mb-4">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-[10px] {{ $conn->status === 'active' ? 'text-theme-accent' : 'text-amber-500' }} font-bold mb-0.5">{{ $conn->connection_number }}</p>
                <h3 class="text-lg font-bold text-theme-heading">{{ ucwords(str_replace('_', ' ', $conn->connection_type)) }}</h3>
                <p class="text-xs text-theme-text">{{ $conn->field_name }} · Load: {{ $conn->sanctioned_load_kw }} kW · Meter: {{ $conn->meter_number ?? 'Pending' }}</p>
                @if($conn->tariffCategory)
                    <p class="text-xs text-theme-text mt-1">Tariff: {{ $conn->tariffCategory->name }} (₹{{ number_format($conn->tariffCategory->rate_per_unit, 2) }}/unit)</p>
                @endif
                @if($conn->installation_date)
                    <p class="text-xs text-theme-text mt-1">Installed: {{ $conn->installation_date->format('d M Y') }}</p>
                @endif
            </div>
            <span class="px-3 py-1.5 rounded-lg text-xs font-bold {{ $conn->status === 'active' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/20' : ($conn->status === 'pending' ? 'bg-amber-500/20 text-amber-500 border border-amber-500/20' : 'bg-red-500/20 text-red-400 border border-red-500/20') }}">
                {{ ucfirst($conn->status) }}
            </span>
        </div>

        @if($conn->meterReadings->count() > 0)
            <h4 class="text-xs font-bold text-theme-text tracking-widest uppercase mb-3">Recent Readings</h4>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="text-left text-[10px] text-theme-text font-bold tracking-widest uppercase border-b border-theme-border">
                            <th class="pb-2 pr-4">Date</th>
                            <th class="pb-2 pr-4 text-right">Previous</th>
                            <th class="pb-2 pr-4 text-right">Current</th>
                            <th class="pb-2 pr-4 text-right">Units</th>
                            <th class="pb-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($conn->meterReadings->take(5) as $reading)
                            <tr class="border-b border-theme-border/30">
                                <td class="py-2 pr-4 text-theme-text">{{ $reading->reading_date->format('d M Y') }}</td>
                                <td class="py-2 pr-4 text-right text-theme-text">{{ number_format($reading->previous_reading) }}</td>
                                <td class="py-2 pr-4 text-right text-theme-heading font-medium">{{ number_format($reading->current_reading) }}</td>
                                <td class="py-2 pr-4 text-right font-bold text-theme-heading">{{ number_format($reading->units_consumed) }}</td>
                                <td class="py-2">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $reading->is_verified ? 'bg-emerald-500/20 text-emerald-400' : 'bg-amber-500/20 text-amber-500' }}">
                                        {{ $reading->is_verified ? 'Verified' : 'Pending' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4 border-t border-theme-border">
                <p class="text-xs text-theme-text">
                    @if($conn->status === 'pending')
                        Connection is pending approval. Readings will appear once active.
                    @else
                        No meter readings recorded yet.
                    @endif
                </p>
            </div>
        @endif
    </div>
@empty
    <div class="utilitarian-card p-8 text-center">
        <i class="fa-solid fa-plug text-3xl text-theme-text mb-3"></i>
        <h3 class="text-lg font-bold text-theme-heading mb-2">No Connections Yet</h3>
        <p class="text-sm text-theme-text mb-4">Request your first agricultural power connection to get started.</p>
        <button onclick="document.getElementById('connectionModal').classList.remove('hidden')"
            class="bg-theme-accent hover:bg-theme-hover text-white text-sm font-bold px-6 py-2.5 rounded-lg transition-colors">
            <i class="fa-solid fa-plus mr-2"></i> Request Connection
        </button>
    </div>
@endforelse

{{-- Connection Request Modal --}}
<div id="connectionModal" class="hidden fixed inset-0 bg-[#0A110D]/80 backdrop-blur-sm z-50 flex items-center justify-center">
    <div class="utilitarian-card p-6 w-full max-w-md border-t-2 border-t-emerald-500 relative">
        <button onclick="document.getElementById('connectionModal').classList.add('hidden')" class="absolute top-4 right-4 text-theme-text hover:text-white"><i class="fa-solid fa-xmark"></i></button>
        <h3 class="text-xl font-bold text-theme-heading mb-4">Request New Connection</h3>
        <form method="POST" action="{{ route('farmer.connection.store') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-bold text-theme-text tracking-widest uppercase mb-2">Connection Type</label>
                <select name="connection_type" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-4 py-2.5 text-white focus:border-emerald-500 focus:outline-none" required>
                    <option value="">Select Type</option>
                    <option value="tubewell_pump">Tubewell Pump</option>
                    <option value="irrigation_motor">Irrigation Motor</option>
                    <option value="thresher">Thresher</option>
                    <option value="drip_irrigation">Drip Irrigation</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-theme-text tracking-widest uppercase mb-2">Field Name / Location</label>
                <input type="text" name="field_name" placeholder="e.g. North Field, Plot A" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-4 py-2.5 text-white focus:border-emerald-500 focus:outline-none placeholder-theme-text/40" required>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-theme-text tracking-widest uppercase mb-2">Sanctioned Load (kW)</label>
                <input type="number" name="sanctioned_load_kw" min="1" max="50" step="0.5" placeholder="e.g. 7.5" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-4 py-2.5 text-white focus:border-emerald-500 focus:outline-none placeholder-theme-text/40" required>
            </div>
            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-2.5 px-4 rounded-lg transition-all text-sm">Submit Request</button>
        </form>
    </div>
</div>
@endsection
