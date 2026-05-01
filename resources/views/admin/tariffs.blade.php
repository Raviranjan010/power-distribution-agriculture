@extends('layouts.app')

@section('content')
<div class="mb-8">
    <h2 class="text-3xl font-bold text-theme-heading mb-1">Tariff Categories</h2>
    <p class="text-sm text-theme-text">Manage electricity tariff rates</p>
</div>

<!-- Create Form -->
<div class="utilitarian-card p-6 mb-6">
    <h3 class="text-sm font-bold text-theme-heading mb-4">Add New Tariff</h3>
    <form method="POST" action="{{ route('admin.tariff.store') }}" class="flex flex-wrap items-end gap-4">
        @csrf
        <div class="flex-grow min-w-[200px]">
            <label class="block text-[10px] text-theme-text font-bold tracking-widest uppercase mb-2">Name</label>
            <input type="text" name="name" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-4 py-2.5 text-white text-sm" required>
        </div>
        <div class="w-32">
            <label class="block text-[10px] text-theme-text font-bold tracking-widest uppercase mb-2">Rate/Unit</label>
            <input type="number" name="rate_per_unit" step="0.01" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-4 py-2.5 text-white text-sm" required>
        </div>
        <div class="w-32">
            <label class="block text-[10px] text-theme-text font-bold tracking-widest uppercase mb-2">Fixed/kW</label>
            <input type="number" name="fixed_charge_per_kw" step="0.01" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-4 py-2.5 text-white text-sm" required>
        </div>
        <div class="w-40">
            <label class="block text-[10px] text-theme-text font-bold tracking-widest uppercase mb-2">Type</label>
            <select name="applicable_to" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-4 py-2.5 text-white text-sm" required>
                <option value="agricultural">Agricultural</option>
                <option value="domestic">Domestic</option>
                <option value="commercial">Commercial</option>
            </select>
        </div>
        <div class="w-40">
            <label class="block text-[10px] text-theme-text font-bold tracking-widest uppercase mb-2">Effective From</label>
            <input type="date" name="effective_from" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-4 py-2.5 text-white text-sm" required>
        </div>
        <button type="submit" class="bg-theme-accent hover:bg-theme-hover text-white font-bold py-2.5 px-6 rounded-lg text-sm">Add</button>
    </form>
</div>

<!-- List -->
<div class="utilitarian-card p-6">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[10px] text-theme-text font-bold tracking-widest uppercase border-b border-theme-border">
                    <th class="pb-3 pr-4">Name</th>
                    <th class="pb-3 pr-4 text-right">Rate/Unit</th>
                    <th class="pb-3 pr-4 text-right">Fixed/kW</th>
                    <th class="pb-3 pr-4">Type</th>
                    <th class="pb-3 pr-4">Effective From</th>
                    <th class="pb-3">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tariffs as $t)
                    <tr class="border-b border-theme-border/50 hover:bg-theme-border/20 transition-colors">
                        <td class="py-3 pr-4 font-medium text-theme-heading">{{ $t->name }}</td>
                        <td class="py-3 pr-4 text-right font-bold text-theme-heading">₹{{ number_format($t->rate_per_unit, 2) }}</td>
                        <td class="py-3 pr-4 text-right text-theme-text">₹{{ number_format($t->fixed_charge_per_kw, 2) }}</td>
                        <td class="py-3 pr-4 text-theme-text">{{ ucfirst($t->applicable_to) }}</td>
                        <td class="py-3 pr-4 text-theme-text">{{ \Carbon\Carbon::parse($t->effective_from)->format('d M Y') }}</td>
                        <td class="py-3">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $t->is_active ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400' }}">
                                {{ $t->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
