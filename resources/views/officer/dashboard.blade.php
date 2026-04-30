@extends('layouts.app')

@section('content')
<div class="mb-8">
    <h2 class="text-3xl font-bold text-white mb-2">Officer Dashboard</h2>
    <p class="text-slate-400">Welcome, {{ Auth::user()->name }}. Manage your assigned transformers and complaints.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Managed Transformers -->
    <div class="glass-panel rounded-2xl p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-white"><i class="fa-solid fa-server mr-2 text-brand-500"></i> Assigned Transformers</h3>
        </div>
        
        @if($transformers->isEmpty())
            <p class="text-slate-400 text-sm">No transformers assigned to you yet.</p>
        @else
            <div class="space-y-4">
                @foreach($transformers as $transformer)
                    <div class="p-4 rounded-xl border border-slate-700 bg-slate-800/50 flex justify-between items-center">
                        <div>
                            <p class="font-bold text-white">{{ $transformer->code }}</p>
                            <p class="text-xs text-slate-400"><i class="fa-solid fa-location-dot mr-1"></i> {{ $transformer->location }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-white">{{ $transformer->current_load_kw }} / {{ $transformer->capacity_kw }} kW</p>
                            <div class="w-24 h-2 bg-slate-700 rounded-full mt-2 overflow-hidden">
                                @php
                                    $percentage = ($transformer->current_load_kw / $transformer->capacity_kw) * 100;
                                    $color = $percentage > 90 ? 'bg-red-500' : ($percentage > 75 ? 'bg-yellow-500' : 'bg-emerald-500');
                                @endphp
                                <div class="h-full {{ $color }}" style="width: {{ min($percentage, 100) }}%"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Active Complaints -->
    <div class="glass-panel rounded-2xl p-6 border-t-4 border-t-red-500">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-white"><i class="fa-solid fa-clipboard-list mr-2 text-red-500"></i> Active Complaints</h3>
        </div>
        
        @if($assignedComplaints->isEmpty())
            <p class="text-slate-400 text-sm">You have no pending complaints to resolve.</p>
        @else
            <div class="space-y-4">
                @foreach($assignedComplaints as $complaint)
                    <div class="p-4 rounded-xl border border-red-500/20 bg-red-500/5">
                        <div class="flex justify-between mb-2">
                            <h4 class="font-bold text-white text-sm">{{ $complaint->subject }}</h4>
                            <span class="px-2 py-1 rounded-md text-xs font-semibold bg-red-500/20 text-red-400">Action Required</span>
                        </div>
                        <p class="text-xs text-slate-400 mb-3">{{ $complaint->description }}</p>
                        <div class="flex justify-between items-center mt-3">
                            <span class="text-xs text-slate-500">From: {{ $complaint->user->name ?? 'Unknown' }}</span>
                            <form method="POST" action="{{ route('officer.complaint.resolve', $complaint->id) }}">
                                @csrf
                                <button type="submit" class="text-xs font-medium bg-red-500 hover:bg-red-400 text-white px-3 py-1.5 rounded transition-colors">
                                    Resolve Now
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- Pending Connections -->
<div class="glass-panel rounded-2xl p-6 mb-8 border-t-4 border-t-yellow-500">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-bold text-white"><i class="fa-solid fa-code-pull-request mr-2 text-yellow-500"></i> Pending Connections</h3>
    </div>
    
    @if($pendingConnections->isEmpty())
        <p class="text-slate-400 text-sm">No pending connection requests.</p>
    @else
        <div class="space-y-4">
            @foreach($pendingConnections as $conn)
                <div class="p-4 rounded-xl border border-yellow-500/20 bg-yellow-500/5">
                    <div class="flex justify-between mb-2">
                        <h4 class="font-bold text-white text-sm">Farmer: {{ $conn->farmer->user->name ?? 'Unknown' }}</h4>
                        <span class="px-2 py-1 rounded-md text-xs font-semibold bg-yellow-500/20 text-yellow-400">Requested: {{ $conn->requested_load_kw }} kW</span>
                    </div>
                    <form method="POST" action="{{ route('officer.connection.approve', $conn->id) }}" class="flex items-end gap-4 mt-4">
                        @csrf
                        <div class="flex-grow">
                            <label class="block text-xs text-slate-400 mb-1">Assign Transformer</label>
                            <select name="transformer_id" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-white text-sm" required>
                                <option value="">Select Transformer...</option>
                                @foreach($transformers as $t)
                                    <option value="{{ $t->id }}">{{ $t->code }} (Avail: {{ $t->capacity_kw - $t->current_load_kw }} kW)</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-32">
                            <label class="block text-xs text-slate-400 mb-1">Allocated (kW)</label>
                            <input type="number" name="allocated_load_kw" value="{{ $conn->requested_load_kw }}" min="1" max="{{ $conn->requested_load_kw }}" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-white text-sm" required>
                        </div>
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white font-medium px-4 py-2 rounded-lg text-sm transition-colors">
                            Approve
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
