@extends('layouts.app')

@section('content')
<div class="mb-8">
    <h2 class="text-3xl font-bold text-theme-heading mb-1">Subsidy Schemes</h2>
    <p class="text-sm text-theme-text">Manage government subsidy schemes</p>
</div>

<!-- Create Form -->
<div class="utilitarian-card p-6 mb-6">
    <h3 class="text-sm font-bold text-theme-heading mb-4">Add New Scheme</h3>
    <form method="POST" action="{{ route('admin.subsidy.store') }}" class="space-y-4">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-[10px] text-theme-text font-bold tracking-widest uppercase mb-2">Scheme Name</label>
                <input type="text" name="scheme_name" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-4 py-2.5 text-white text-sm" required>
            </div>
            <div>
                <label class="block text-[10px] text-theme-text font-bold tracking-widest uppercase mb-2">Discount %</label>
                <input type="number" name="discount_percentage" step="0.01" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-4 py-2.5 text-white text-sm" required>
            </div>
        </div>
        <div>
            <label class="block text-[10px] text-theme-text font-bold tracking-widest uppercase mb-2">Description</label>
            <textarea name="description" rows="2" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-4 py-2.5 text-white text-sm" required></textarea>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-[10px] text-theme-text font-bold tracking-widest uppercase mb-2">Max Units</label>
                <input type="number" name="max_units_covered" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-4 py-2.5 text-white text-sm" required>
            </div>
            <div>
                <label class="block text-[10px] text-theme-text font-bold tracking-widest uppercase mb-2">Start Date</label>
                <input type="date" name="start_date" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-4 py-2.5 text-white text-sm" required>
            </div>
            <div>
                <label class="block text-[10px] text-theme-text font-bold tracking-widest uppercase mb-2">End Date</label>
                <input type="date" name="end_date" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-4 py-2.5 text-white text-sm" required>
            </div>
        </div>
        <button type="submit" class="bg-theme-accent hover:bg-theme-hover text-white font-bold py-2.5 px-6 rounded-lg text-sm">Create Scheme</button>
    </form>
</div>

<!-- List -->
<div class="utilitarian-card p-6">
    @foreach($schemes as $scheme)
        <div class="border border-theme-border rounded-lg p-4 bg-theme-bg/50 mb-3">
            <div class="flex justify-between items-start">
                <div>
                    <h4 class="text-sm font-bold text-theme-heading">{{ $scheme->scheme_name }}</h4>
                    <p class="text-xs text-theme-text mt-1">{{ $scheme->description }}</p>
                    <p class="text-xs text-theme-text mt-1">
                        {{ $scheme->start_date?->format('d M Y') }} — {{ $scheme->end_date?->format('d M Y') }} · 
                        Max: {{ number_format($scheme->max_units_covered) }} units
                    </p>
                </div>
                <div class="text-right flex-shrink-0">
                    <span class="text-lg font-bold text-emerald-400">{{ number_format($scheme->discount_percentage) }}%</span>
                    <br>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $scheme->is_active ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400' }}">
                        {{ $scheme->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
