@extends('layouts.app')

@section('content')
<div class="mb-8">
    <h2 class="text-3xl font-bold text-theme-heading mb-1">Subsidy Schemes</h2>
    <p class="text-sm text-theme-text">View available schemes and apply for subsidies</p>
</div>

<!-- Available Schemes -->
<div class="utilitarian-card p-6 mb-6">
    <h3 class="text-lg font-bold text-theme-heading mb-6">Available Schemes</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($availableSchemes as $scheme)
            <div class="border border-theme-border rounded-lg p-5 bg-theme-bg/50">
                <div class="flex justify-between items-start mb-3">
                    <h4 class="text-sm font-bold text-theme-heading">{{ $scheme->scheme_name }}</h4>
                    <span class="text-emerald-400 font-bold text-sm bg-emerald-500/20 px-2 py-0.5 rounded border border-emerald-500/20">{{ number_format($scheme->discount_percentage) }}%</span>
                </div>
                <p class="text-xs text-theme-text mb-3">{{ $scheme->description }}</p>
                <p class="text-[10px] text-theme-text mb-3">
                    Valid: {{ $scheme->start_date?->format('d M Y') }} — {{ $scheme->end_date?->format('d M Y') }} · 
                    Max Units: {{ number_format($scheme->max_units_covered) }}
                </p>
                @php
                    $alreadyApplied = $mySubsidies->where('scheme_id', $scheme->id)->first();
                @endphp
                @if($alreadyApplied)
                    <span class="text-xs font-bold {{ $alreadyApplied->status === 'approved' ? 'text-emerald-400' : ($alreadyApplied->status === 'rejected' ? 'text-red-400' : 'text-amber-500') }}">
                        {{ ucfirst($alreadyApplied->status) }}
                    </span>
                @else
                    <form method="POST" action="{{ route('farmer.subsidy.apply') }}">
                        @csrf
                        <input type="hidden" name="scheme_id" value="{{ $scheme->id }}">
                        <button type="submit" class="bg-theme-accent hover:bg-theme-hover text-white text-xs font-bold px-4 py-2 rounded-lg transition-colors">
                            Apply Now
                        </button>
                    </form>
                @endif
            </div>
        @empty
            <p class="text-sm text-theme-text col-span-2">No active schemes at the moment.</p>
        @endforelse
    </div>
</div>

<!-- My Applications -->
<div class="utilitarian-card p-6">
    <h3 class="text-lg font-bold text-theme-heading mb-6">My Applications</h3>
    @forelse($mySubsidies as $sub)
        <div class="border border-theme-border rounded-lg p-4 bg-theme-bg/50 mb-3 flex justify-between items-center">
            <div>
                <h4 class="text-sm font-bold text-theme-heading">{{ $sub->scheme->scheme_name }}</h4>
                <p class="text-xs text-theme-text">Applied {{ $sub->applied_at->format('d M Y') }}</p>
                @if($sub->remarks)
                    <p class="text-xs text-theme-text italic mt-1">{{ $sub->remarks }}</p>
                @endif
            </div>
            @php
                $sc = ['applied'=>'bg-amber-500/20 text-amber-500','approved'=>'bg-emerald-500/20 text-emerald-400','rejected'=>'bg-red-500/20 text-red-400'];
            @endphp
            <span class="px-2.5 py-1 rounded-md text-[10px] font-bold {{ $sc[$sub->status] ?? '' }}">{{ ucfirst($sub->status) }}</span>
        </div>
    @empty
        <p class="text-sm text-theme-text">No subsidy applications yet.</p>
    @endforelse
</div>
@endsection
