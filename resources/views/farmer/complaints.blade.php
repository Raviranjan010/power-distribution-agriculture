@extends('layouts.app')

@section('content')
<div class="mb-8">
    <div class="flex justify-between items-start">
        <div>
            <h2 class="text-3xl font-bold text-theme-heading mb-1">My Complaints</h2>
            <p class="text-sm text-theme-text">Track your filed grievances and report new issues</p>
        </div>
        <button onclick="document.getElementById('complaintModal').classList.remove('hidden')"
            class="border border-theme-border hover:bg-theme-border text-theme-heading text-xs font-bold px-4 py-2.5 rounded-lg transition-colors flex items-center gap-2">
            <i class="fa-solid fa-plus text-rose-400"></i> File Complaint
        </button>
    </div>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="utilitarian-card p-4 border-t border-t-blue-500">
        <p class="text-[10px] text-theme-text font-bold tracking-widest uppercase mb-1">Total Filed</p>
        <p class="text-2xl font-bold text-blue-400">{{ $totalComplaints }}</p>
    </div>
    <div class="utilitarian-card p-4 border-t border-t-amber-500">
        <p class="text-[10px] text-theme-text font-bold tracking-widest uppercase mb-1">Open / Active</p>
        <p class="text-2xl font-bold text-amber-400">{{ $openComplaints }}</p>
    </div>
    <div class="utilitarian-card p-4 border-t border-t-emerald-500">
        <p class="text-[10px] text-theme-text font-bold tracking-widest uppercase mb-1">Resolved</p>
        <p class="text-2xl font-bold text-emerald-400">{{ $resolvedComplaints }}</p>
    </div>
</div>

<div class="utilitarian-card p-6">
    @forelse($allComplaints as $complaint)
        <div class="border border-theme-border rounded-lg p-4 bg-theme-bg/50 mb-3">
            <div class="flex justify-between items-start mb-2">
                <div class="flex-grow">
                    <div class="flex items-center gap-3 mb-1">
                        <p class="text-[10px] text-theme-text font-bold tracking-wider">#{{ $complaint->grv_number }}</p>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $complaint->priority === 'high' ? 'bg-rose-500/20 text-rose-400' : ($complaint->priority === 'medium' ? 'bg-amber-500/20 text-amber-500' : 'bg-theme-border text-theme-text') }}">
                            {{ ucfirst($complaint->priority) }}
                        </span>
                    </div>
                    <h4 class="text-sm font-bold text-theme-heading">{{ ucwords(str_replace('_', ' ', $complaint->complaint_type)) }}</h4>
                    <p class="text-xs text-theme-text mt-1">{{ $complaint->description }}</p>
                    <p class="text-xs text-theme-text mt-2">
                        <i class="fa-solid fa-plug text-[10px] mr-1"></i> {{ $complaint->connection->connection_number ?? 'N/A' }} ·
                        <i class="fa-regular fa-calendar text-[10px] mr-1"></i> Filed {{ $complaint->filed_at->format('d M Y') }}
                        @if($complaint->resolved_at)
                            · <i class="fa-solid fa-check text-[10px] mr-1 text-emerald-400"></i> Resolved {{ $complaint->resolved_at->format('d M Y') }}
                        @endif
                    </p>
                    @if($complaint->resolution_remarks)
                        <div class="mt-2 p-2 bg-emerald-500/10 border border-emerald-500/20 rounded-lg">
                            <p class="text-xs text-emerald-400"><i class="fa-solid fa-message mr-1"></i> {{ $complaint->resolution_remarks }}</p>
                        </div>
                    @endif
                </div>
                @php
                    $sc = [
                        'filed' => 'bg-blue-500/20 text-blue-400',
                        'assigned' => 'bg-amber-500/20 text-amber-500',
                        'in_review' => 'bg-indigo-500/20 text-indigo-400',
                        'resolved' => 'bg-emerald-500/20 text-emerald-400',
                        'closed' => 'bg-theme-border text-theme-text',
                    ];
                @endphp
                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold {{ $sc[$complaint->status] ?? '' }} flex-shrink-0 ml-4">{{ ucwords(str_replace('_',' ',$complaint->status)) }}</span>
            </div>
        </div>
    @empty
        <div class="text-center py-8">
            <i class="fa-regular fa-comment-dots text-3xl text-theme-text mb-3"></i>
            <h3 class="text-lg font-bold text-theme-heading mb-2">No Complaints Filed</h3>
            <p class="text-sm text-theme-text mb-4">If you face any issues with your power supply, file a complaint here.</p>
            <button onclick="document.getElementById('complaintModal').classList.remove('hidden')"
                class="bg-rose-600 hover:bg-rose-500 text-white text-sm font-bold px-6 py-2.5 rounded-lg transition-colors">
                <i class="fa-solid fa-plus mr-2"></i> File Complaint
            </button>
        </div>
    @endforelse

    @if($allComplaints->hasPages())
        <div class="mt-6 flex justify-center">
            {{ $allComplaints->links() }}
        </div>
    @endif
</div>

{{-- Complaint Modal --}}
<div id="complaintModal" class="hidden fixed inset-0 bg-[#0A110D]/80 backdrop-blur-sm z-50 flex items-center justify-center">
    <div class="utilitarian-card p-6 w-full max-w-md border-t-2 border-t-rose-500 relative">
        <button onclick="document.getElementById('complaintModal').classList.add('hidden')"
            class="absolute top-4 right-4 text-theme-text hover:text-white"><i class="fa-solid fa-xmark"></i></button>
        <h3 class="text-xl font-bold text-theme-heading mb-4">File a Complaint</h3>
        <form method="POST" action="{{ route('farmer.complaint.store') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-bold text-theme-text tracking-widest uppercase mb-2">Connection</label>
                <select name="connection_id" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-4 py-2.5 text-white focus:border-rose-500 focus:outline-none" required>
                    <option value="">Select Connection</option>
                    @foreach($userConnections as $conn)
                        <option value="{{ $conn->id }}">{{ $conn->connection_number }} — {{ ucwords(str_replace('_', ' ', $conn->connection_type)) }} ({{ $conn->field_name }})</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-theme-text tracking-widest uppercase mb-2">Complaint Type</label>
                <select name="complaint_type" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-4 py-2.5 text-white focus:border-rose-500 focus:outline-none" required>
                    <option value="">Select Type</option>
                    <option value="voltage_fluctuation">Voltage Fluctuation</option>
                    <option value="no_supply">No Supply</option>
                    <option value="meter_fault">Meter Fault</option>
                    <option value="billing_error">Billing Error</option>
                    <option value="transformer_issue">Transformer Issue</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-theme-text tracking-widest uppercase mb-2">Priority</label>
                <select name="priority" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-4 py-2.5 text-white focus:border-rose-500 focus:outline-none" required>
                    <option value="low">Low</option>
                    <option value="medium" selected>Medium</option>
                    <option value="high">High</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-theme-text tracking-widest uppercase mb-2">Description</label>
                <textarea name="description" rows="3" placeholder="Describe the issue in detail..."
                    class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-4 py-2.5 text-white focus:border-rose-500 focus:outline-none placeholder-theme-text/40"
                    required></textarea>
            </div>
            <button type="submit" class="w-full bg-rose-600 hover:bg-rose-500 text-white font-bold py-2.5 px-4 rounded-lg transition-all text-sm">Submit Complaint</button>
        </form>
    </div>
</div>
@endsection
