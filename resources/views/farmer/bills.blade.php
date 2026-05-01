@extends('layouts.app')

@section('content')
<div class="mb-8">
    <h2 class="text-3xl font-bold text-theme-heading mb-1">Bills & Payments</h2>
    <p class="text-sm text-theme-text">View and manage your electricity bills</p>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="utilitarian-card p-4 border-t border-t-rose-500">
        <p class="text-[10px] text-theme-text font-bold tracking-widest uppercase mb-1">Outstanding</p>
        <p class="text-2xl font-bold text-rose-400">₹{{ number_format($totalOutstanding, 2) }}</p>
    </div>
    <div class="utilitarian-card p-4 border-t border-t-emerald-500">
        <p class="text-[10px] text-theme-text font-bold tracking-widest uppercase mb-1">Paid This Year</p>
        <p class="text-2xl font-bold text-emerald-400">₹{{ number_format($totalPaidThisYear, 2) }}</p>
    </div>
    <div class="utilitarian-card p-4 border-t border-t-amber-500">
        <p class="text-[10px] text-theme-text font-bold tracking-widest uppercase mb-1">Next Due Date</p>
        <p class="text-2xl font-bold text-amber-400">
            @if($nextDue)
                {{ $nextDue->due_date->format('d M') }}
            @else
                —
            @endif
        </p>
    </div>
</div>

<div class="utilitarian-card p-6">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[10px] text-theme-text font-bold tracking-widest uppercase border-b border-theme-border">
                    <th class="pb-3 pr-4">Bill #</th>
                    <th class="pb-3 pr-4">Connection</th>
                    <th class="pb-3 pr-4">Period</th>
                    <th class="pb-3 pr-4 text-right">Units</th>
                    <th class="pb-3 pr-4 text-right">Amount</th>
                    <th class="pb-3 pr-4">Due Date</th>
                    <th class="pb-3 pr-4">Status</th>
                    <th class="pb-3">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bills as $bill)
                    <tr class="border-b border-theme-border/50 hover:bg-theme-border/20 transition-colors">
                        <td class="py-3 pr-4 font-bold text-theme-heading text-xs">{{ $bill->bill_number }}</td>
                        <td class="py-3 pr-4 text-theme-text text-xs">{{ $bill->connection->connection_number }}</td>
                        <td class="py-3 pr-4 text-theme-text text-xs">{{ \Carbon\Carbon::create($bill->billing_year, $bill->billing_month)->format('M Y') }}</td>
                        <td class="py-3 pr-4 text-right font-bold text-theme-heading text-xs">{{ number_format($bill->units_consumed) }} kWh</td>
                        <td class="py-3 pr-4 text-right font-bold text-theme-heading">₹{{ number_format($bill->net_payable, 2) }}</td>
                        <td class="py-3 pr-4 text-theme-text text-xs">{{ $bill->due_date->format('d M Y') }}</td>
                        <td class="py-3 pr-4">
                            @php
                                $colors = [
                                    'pending' => 'bg-amber-500/20 text-amber-500',
                                    'paid' => 'bg-emerald-500/20 text-emerald-400',
                                    'overdue' => 'bg-red-500/20 text-red-400',
                                    'partially_paid' => 'bg-indigo-500/20 text-indigo-400',
                                ];
                            @endphp
                            <span class="px-2.5 py-1 rounded-md text-[10px] font-bold {{ $colors[$bill->status] ?? 'bg-theme-border text-theme-text' }}">{{ ucwords(str_replace('_', ' ', $bill->status)) }}</span>
                        </td>
                        <td class="py-3">
                            @if($bill->status === 'pending')
                                <form method="POST" action="{{ route('farmer.bill.pay', $bill->id) }}">
                                    @csrf
                                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white text-[10px] font-bold px-3 py-1.5 rounded-md transition-colors">
                                        <i class="fa-solid fa-credit-card mr-1"></i> Pay ₹{{ number_format($bill->net_payable, 0) }}
                                    </button>
                                </form>
                            @elseif($bill->status === 'paid')
                                <span class="text-[10px] text-emerald-400 font-bold"><i class="fa-solid fa-check mr-1"></i> Paid</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center">
                            <i class="fa-solid fa-file-invoice text-3xl text-theme-text mb-3 block"></i>
                            <p class="text-sm text-theme-text">No bills generated yet.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($bills->hasPages())
        <div class="mt-6 flex justify-center">
            {{ $bills->links() }}
        </div>
    @endif
</div>
@endsection
