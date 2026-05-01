@extends('layouts.app')

@section('content')
<div class="mb-8">
    <h2 class="text-3xl font-bold text-theme-heading mb-1">Lineman Dashboard</h2>
    <p class="text-sm text-theme-text">
        {{ Auth::user()->name }} · Zone: {{ Auth::user()->zone_id ? \App\Models\Zone::find(Auth::user()->zone_id)?->name : 'Unassigned' }} · {{ now()->format('d M Y') }}
    </p>
</div>

<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <div class="utilitarian-card p-5 border-t border-t-amber-500">
        <p class="text-[10px] text-theme-text font-bold tracking-widest uppercase mb-1">Assigned<br>Complaints</p>
        <h3 class="text-4xl font-bold text-amber-400">{{ $complaints->count() }}</h3>
    </div>
    <div class="utilitarian-card p-5 border-t border-t-emerald-500">
        <p class="text-[10px] text-theme-text font-bold tracking-widest uppercase mb-1">Readings<br>This Month</p>
        <h3 class="text-4xl font-bold text-emerald-400">{{ $myReadings->count() }}</h3>
    </div>
    <div class="utilitarian-card p-5 border-t border-t-indigo-500">
        <p class="text-[10px] text-theme-text font-bold tracking-widest uppercase mb-1">Zone<br>Connections</p>
        <h3 class="text-4xl font-bold text-indigo-400">{{ $connections->count() }}</h3>
    </div>
</div>

<!-- Record Meter Reading -->
<div class="utilitarian-card p-6 mb-6">
    <h3 class="text-lg font-bold text-theme-heading mb-6">Record Meter Reading</h3>
    <form method="POST" action="{{ route('lineman.reading.store') }}" class="flex flex-wrap items-end gap-4">
        @csrf
        <div class="flex-grow min-w-[200px]">
            <label class="block text-[10px] text-theme-text font-bold tracking-widest uppercase mb-2">Connection</label>
            <select name="connection_id" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-4 py-2.5 text-white text-sm" required>
                <option value="">Select connection...</option>
                @foreach($connections as $conn)
                    @php $lastReading = $conn->meterReadings->first(); @endphp
                    <option value="{{ $conn->id }}">
                        {{ $conn->connection_number }} — {{ $conn->consumer->name }}
                        (Last: {{ $lastReading ? number_format($lastReading->current_reading, 2) : '0' }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="w-40">
            <label class="block text-[10px] text-theme-text font-bold tracking-widest uppercase mb-2">Current Reading</label>
            <input type="number" name="current_reading" step="0.01" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-4 py-2.5 text-white text-sm" required>
        </div>
        <div class="w-48">
            <label class="block text-[10px] text-theme-text font-bold tracking-widest uppercase mb-2">Remarks</label>
            <input type="text" name="remarks" placeholder="Optional" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-4 py-2.5 text-white text-sm">
        </div>
        <button type="submit" class="bg-theme-accent hover:bg-theme-hover text-white font-bold py-2.5 px-6 rounded-lg text-sm transition-colors">
            <i class="fa-solid fa-save mr-1"></i> Save Reading
        </button>
    </form>
</div>

<!-- My Readings This Month -->
<div class="utilitarian-card p-6 mb-6">
    <h3 class="text-lg font-bold text-theme-heading mb-6">My Readings This Month</h3>
    @forelse($myReadings as $reading)
        <div class="border border-theme-border rounded-lg p-4 bg-theme-bg/50 mb-3 flex justify-between items-center">
            <div>
                <p class="text-[10px] text-theme-accent font-bold">{{ $reading->connection->connection_number }}</p>
                <p class="text-sm text-theme-heading font-medium">{{ $reading->reading_date->format('d M Y') }}</p>
                <p class="text-xs text-theme-text">
                    Previous: {{ number_format($reading->previous_reading) }} → Current: {{ number_format($reading->current_reading) }}
                </p>
            </div>
            <div class="text-right">
                <p class="text-lg font-bold text-theme-heading">{{ number_format($reading->units_consumed) }}</p>
                <p class="text-[10px] text-theme-text uppercase">kWh</p>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $reading->is_verified ? 'bg-emerald-500/20 text-emerald-400' : 'bg-amber-500/20 text-amber-500' }}">
                    {{ $reading->is_verified ? 'Verified' : 'Pending' }}
                </span>
            </div>
        </div>
    @empty
        <p class="text-sm text-theme-text">No readings recorded this month yet.</p>
    @endforelse
</div>

<!-- Assigned Complaints -->
<div class="utilitarian-card p-6">
    <h3 class="text-lg font-bold text-theme-heading mb-6">Assigned Complaints</h3>
    @forelse($complaints as $complaint)
        <div class="border border-theme-border rounded-lg p-4 bg-theme-bg/50 mb-3">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <p class="text-[10px] text-theme-text font-bold tracking-wider">#{{ $complaint->grv_number }}</p>
                    <h4 class="text-sm font-bold text-theme-heading">{{ $complaint->consumer->name }} — {{ ucwords(str_replace('_', ' ', $complaint->complaint_type)) }}</h4>
                    <p class="text-xs text-theme-text mt-1">{{ $complaint->description }}</p>
                    <p class="text-xs text-theme-text mt-1">Priority: <span class="font-bold {{ $complaint->priority === 'high' ? 'text-rose-400' : ($complaint->priority === 'medium' ? 'text-amber-400' : 'text-theme-text') }}">{{ ucfirst($complaint->priority) }}</span></p>
                </div>
                @php
                    $sc = ['assigned'=>'bg-amber-500/20 text-amber-500','in_review'=>'bg-indigo-500/20 text-indigo-400','resolved'=>'bg-emerald-500/20 text-emerald-400'];
                @endphp
                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold {{ $sc[$complaint->status] ?? 'bg-theme-border text-theme-text' }} flex-shrink-0">{{ ucwords(str_replace('_',' ',$complaint->status)) }}</span>
            </div>
            @if(in_array($complaint->status, ['assigned', 'in_review']))
                <form method="POST" action="{{ route('lineman.complaint.update', $complaint->id) }}" class="flex items-end gap-3 mt-3 pt-3 border-t border-theme-border/50">
                    @csrf
                    <div class="flex-grow">
                        <select name="status" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-3 py-2 text-white text-sm" required>
                            @if($complaint->status === 'assigned')
                                <option value="in_review">Mark as In Review</option>
                            @endif
                            <option value="resolved">Mark as Resolved</option>
                        </select>
                    </div>
                    <div class="w-48">
                        <input type="text" name="resolution_remarks" placeholder="Remarks (optional)" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-3 py-2 text-white text-sm">
                    </div>
                    <button type="submit" class="bg-theme-accent hover:bg-theme-hover text-white font-medium px-4 py-2 rounded-lg text-sm transition-colors">Update</button>
                </form>
            @endif
        </div>
    @empty
        <p class="text-sm text-theme-text">No complaints assigned to you.</p>
    @endforelse
</div>
@endsection
