@extends('layouts.app')

@section('content')
<div class="mb-8">
    <h2 class="text-3xl font-bold text-theme-heading mb-1">SDO Dashboard</h2>
    <p class="text-sm text-theme-text">
        {{ Auth::user()->name }} · Zone: {{ $zone->name ?? 'Unassigned' }} ({{ $zone->district ?? '' }}) · {{ now()->format('d M Y') }}
    </p>
</div>

<!-- Stats Row -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="utilitarian-card p-5 border-t border-t-amber-500">
        <p class="text-[10px] text-theme-text font-bold tracking-widest uppercase mb-1">Pending<br>Connections</p>
        <h3 class="text-4xl font-bold text-amber-400">{{ $pendingConnections->count() }}</h3>
    </div>
    <div class="utilitarian-card p-5 border-t border-t-rose-500">
        <p class="text-[10px] text-theme-text font-bold tracking-widest uppercase mb-1">Open<br>Complaints</p>
        <h3 class="text-4xl font-bold text-rose-400">{{ $complaints->where('status', '!=', 'resolved')->where('status', '!=', 'closed')->count() }}</h3>
    </div>
    <div class="utilitarian-card p-5 border-t border-t-indigo-500">
        <p class="text-[10px] text-theme-text font-bold tracking-widest uppercase mb-1">Pending<br>Readings</p>
        <h3 class="text-4xl font-bold text-indigo-400">{{ $pendingReadings->count() }}</h3>
    </div>
    <div class="utilitarian-card p-5 border-t border-t-emerald-500">
        <p class="text-[10px] text-theme-text font-bold tracking-widest uppercase mb-1">Monthly<br>Revenue</p>
        <h3 class="text-4xl font-bold text-emerald-400">₹{{ number_format($monthlyRevenue, 0) }}</h3>
    </div>
</div>

<!-- Pending Connections -->
<div class="utilitarian-card p-6 mb-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-bold text-theme-heading">Pending Connection Requests</h3>
    </div>
    @forelse($pendingConnections as $conn)
        <div class="border border-theme-border rounded-lg p-4 bg-theme-bg/50 mb-3">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <p class="text-[10px] text-amber-500 font-bold mb-0.5">{{ $conn->connection_number }}</p>
                    <h4 class="text-sm font-bold text-theme-heading">{{ $conn->consumer->name }}</h4>
                    <p class="text-xs text-theme-text">{{ ucwords(str_replace('_', ' ', $conn->connection_type)) }} · {{ $conn->field_name }} · {{ $conn->sanctioned_load_kw }} kW</p>
                </div>
                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-[#2b1f13] text-amber-500">Pending</span>
            </div>
            <form method="POST" action="{{ route('sdo.connection.approve', $conn->id) }}" class="flex items-end gap-3">
                @csrf
                <div class="flex-grow">
                    <label class="block text-[10px] text-theme-text font-bold uppercase mb-1">Assign Tariff</label>
                    <select name="tariff_category_id" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-3 py-2 text-white text-sm" required>
                        @foreach($tariffCategories as $tc)
                            <option value="{{ $tc->id }}">{{ $tc->name }} (₹{{ $tc->rate_per_unit }}/unit)</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white font-medium px-4 py-2 rounded-lg text-sm transition-colors">Approve</button>
                <a href="{{ route('sdo.connection.reject', $conn->id) }}" onclick="event.preventDefault(); document.getElementById('reject-form-{{ $conn->id }}').submit();" class="bg-red-600/20 hover:bg-red-600 text-red-400 hover:text-white font-medium px-4 py-2 rounded-lg text-sm transition-colors">Reject</a>
            </form>
            <form id="reject-form-{{ $conn->id }}" method="POST" action="{{ route('sdo.connection.reject', $conn->id) }}" class="hidden">@csrf</form>
        </div>
    @empty
        <p class="text-sm text-theme-text">No pending connection requests in your zone.</p>
    @endforelse
</div>

<!-- Pending Meter Readings -->
<div class="utilitarian-card p-6 mb-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-bold text-theme-heading">Meter Readings Awaiting Verification</h3>
        <form method="POST" action="{{ route('sdo.bills.generate') }}">
            @csrf
            <button type="submit" class="bg-theme-accent hover:bg-theme-hover text-white text-xs font-bold px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                <i class="fa-solid fa-receipt"></i> Generate Bills for Zone
            </button>
        </form>
    </div>
    @forelse($pendingReadings as $reading)
        <div class="border border-theme-border rounded-lg p-4 bg-theme-bg/50 mb-3 flex justify-between items-center">
            <div>
                <p class="text-[10px] text-theme-accent font-bold mb-0.5">{{ $reading->connection->connection_number }}</p>
                <p class="text-sm font-medium text-theme-heading">{{ $reading->connection->consumer->name }}</p>
                <p class="text-xs text-theme-text">{{ $reading->reading_date->format('d M Y') }} · {{ number_format($reading->units_consumed) }} kWh · By {{ $reading->lineman->name ?? 'Unknown' }}</p>
            </div>
            <form method="POST" action="{{ route('sdo.reading.verify', $reading->id) }}">
                @csrf
                <button type="submit" class="bg-indigo-600/20 hover:bg-indigo-600 text-indigo-400 hover:text-white text-xs font-bold px-4 py-2 rounded-lg transition-colors">
                    <i class="fa-solid fa-check mr-1"></i> Verify
                </button>
            </form>
        </div>
    @empty
        <p class="text-sm text-theme-text">All readings verified. Ready to generate bills.</p>
    @endforelse
</div>

<!-- Complaints -->
<div class="utilitarian-card p-6 mb-6">
    <h3 class="text-lg font-bold text-theme-heading mb-6">Zone Complaints</h3>
    @forelse($complaints->take(10) as $complaint)
        <div class="border border-theme-border rounded-lg p-4 bg-theme-bg/50 mb-3">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <p class="text-[10px] text-theme-text font-bold tracking-wider">#{{ $complaint->grv_number }}</p>
                    <h4 class="text-sm font-bold text-theme-heading">{{ $complaint->consumer->name }} — {{ ucwords(str_replace('_', ' ', $complaint->complaint_type)) }}</h4>
                    <p class="text-xs text-theme-text mt-1">{{ $complaint->description }}</p>
                </div>
                @php
                    $sc = ['filed'=>'bg-blue-500/20 text-blue-400','assigned'=>'bg-amber-500/20 text-amber-500','in_review'=>'bg-amber-500/20 text-amber-500','resolved'=>'bg-emerald-500/20 text-emerald-400','closed'=>'bg-theme-border text-theme-text'];
                @endphp
                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold {{ $sc[$complaint->status] ?? '' }} flex-shrink-0">{{ ucwords(str_replace('_',' ',$complaint->status)) }}</span>
            </div>
            @if($complaint->status === 'filed')
                <form method="POST" action="{{ route('sdo.complaint.assign', $complaint->id) }}" class="flex items-end gap-3 mt-3 pt-3 border-t border-theme-border/50">
                    @csrf
                    <div class="flex-grow">
                        <select name="assigned_to" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-3 py-2 text-white text-sm" required>
                            <option value="">Assign to Lineman...</option>
                            @foreach($linemen as $lm)
                                <option value="{{ $lm->id }}">{{ $lm->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="bg-amber-600 hover:bg-amber-500 text-white font-medium px-4 py-2 rounded-lg text-sm transition-colors">Assign</button>
                </form>
            @endif
        </div>
    @empty
        <p class="text-sm text-theme-text">No complaints in this zone.</p>
    @endforelse
</div>

<!-- Pending Subsidy Applications -->
@if($pendingSubsidies->count() > 0)
<div class="utilitarian-card p-6">
    <h3 class="text-lg font-bold text-theme-heading mb-6">Pending Subsidy Applications</h3>
    @foreach($pendingSubsidies as $sub)
        <div class="border border-theme-border rounded-lg p-4 bg-theme-bg/50 mb-3 flex justify-between items-center">
            <div>
                <p class="text-sm font-bold text-theme-heading">{{ $sub->consumer->name }}</p>
                <p class="text-xs text-theme-text">{{ $sub->scheme->scheme_name }} · Applied {{ $sub->applied_at->format('d M Y') }}</p>
            </div>
            <div class="flex gap-2">
                <form method="POST" action="{{ route('sdo.subsidy.approve', $sub->id) }}">
                    @csrf
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold px-3 py-2 rounded-lg">Approve</button>
                </form>
                <form method="POST" action="{{ route('sdo.subsidy.reject', $sub->id) }}">
                    @csrf
                    <button type="submit" class="bg-red-600/20 hover:bg-red-500 text-red-400 hover:text-white text-xs font-bold px-3 py-2 rounded-lg">Reject</button>
                </form>
            </div>
        </div>
    @endforeach
</div>
@endif
@endsection
