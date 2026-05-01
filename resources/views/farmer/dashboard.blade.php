@extends('layouts.app')

@section('content')
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-theme-heading mb-1 flex items-center gap-2">
            @php
                $hour = now()->format('H');
                $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
            @endphp
            {{ $greeting }}, {{ explode(' ', Auth::user()->name)[0] }} ☀️
        </h2>
        <p class="text-sm text-theme-text">
            Farmer ID: {{ Auth::user()->farmer_id_number ?? 'Pending' }} —
            {{ Auth::user()->village ?? '' }}{{ Auth::user()->district ? ', ' . Auth::user()->district : '' }} —
            Last updated: {{ now()->format('d M Y, h:i A') }}
        </p>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        {{-- Active Connections --}}
        <div class="utilitarian-card p-5 relative overflow-hidden group border-t border-t-emerald-500 hover:border-emerald-500 transition-colors">
            <div class="absolute top-4 right-4 w-8 h-8 rounded-lg bg-[#16271c] border border-emerald-500/30 text-emerald-500 flex items-center justify-center">
                <i class="fa-solid fa-bolt"></i>
            </div>
            <p class="text-[10px] text-theme-text font-bold tracking-widest uppercase mb-1">Active<br>Connections</p>
            <h3 class="text-4xl font-bold text-emerald-400 mb-2">{{ $activeConnections }}</h3>
            <p class="text-xs text-theme-text flex items-center gap-1">
                @if($pendingConnections > 0)
                    <i class="fa-solid fa-clock text-amber-500 text-[10px]"></i> {{ $pendingConnections }} pending
                @else
                    <i class="fa-solid fa-arrow-up text-emerald-500 text-[10px]"></i> All operational
                @endif
            </p>
        </div>

        {{-- Units This Month --}}
        <div class="utilitarian-card p-5 relative overflow-hidden group border-t border-t-amber-500 hover:border-amber-500 transition-colors">
            <div class="absolute top-4 right-4 w-8 h-8 rounded-lg bg-[#2b1f13] border border-amber-500/30 text-amber-500 flex items-center justify-center">
                <i class="fa-solid fa-chart-line"></i>
            </div>
            <p class="text-[10px] text-theme-text font-bold tracking-widest uppercase mb-1">Units This<br>Month</p>
            <h3 class="text-4xl font-bold text-amber-400 mb-2">{{ number_format($unitsThisMonth) }}</h3>
            <p class="text-xs text-theme-text">kWh consumed</p>
        </div>

        {{-- Active Subsidies --}}
        <div class="utilitarian-card p-5 relative overflow-hidden group border-t border-t-indigo-500 hover:border-indigo-500 transition-colors">
            <div class="absolute top-4 right-4 w-8 h-8 rounded-lg bg-[#1a1b36] border border-indigo-500/30 text-indigo-500 flex items-center justify-center">
                <i class="fa-solid fa-percent"></i>
            </div>
            <p class="text-[10px] text-theme-text font-bold tracking-widest uppercase mb-1">Active<br>Subsidies</p>
            <h3 class="text-4xl font-bold text-indigo-400 mb-2">{{ $activeSubsidies }}</h3>
            <p class="text-xs text-theme-text">
                @if($subsidies->count() > 0)
                    {{ $subsidies->first()->scheme->scheme_name ?? 'Schemes applied' }}
                @else
                    No active schemes
                @endif
            </p>
        </div>

        {{-- Outstanding Bills --}}
        <div class="utilitarian-card p-5 relative overflow-hidden group border-t border-t-rose-500 hover:border-rose-500 transition-colors">
            <div class="absolute top-4 right-4 w-8 h-8 rounded-lg bg-[#33181e] border border-rose-500/30 text-rose-500 flex items-center justify-center">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <p class="text-[10px] text-theme-text font-bold tracking-widest uppercase mb-1">Outstanding<br>Bills</p>
            <h3 class="text-4xl font-bold text-rose-400 mb-2">
                @if($latestBill)
                    ₹{{ number_format($latestBill->net_payable, 0) }}
                @else
                    ₹0
                @endif
            </h3>
            <p class="text-xs text-theme-text">
                @if($latestBill)
                    Due {{ $latestBill->due_date->format('d M Y') }}
                @else
                    All bills paid
                @endif
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- Your Connections --}}
        <div class="utilitarian-card p-6 lg:col-span-1">
            <div class="flex justify-between items-start mb-6">
                <h3 class="text-lg font-bold text-theme-heading leading-tight">Your<br>Connections</h3>
                <button onclick="document.getElementById('connectionModal').classList.remove('hidden')"
                    class="border border-theme-border hover:bg-theme-border text-theme-heading text-xs font-bold px-3 py-2 rounded-lg transition-colors flex items-center gap-2">
                    <i class="fa-solid fa-plus text-theme-accent"></i> Request New
                </button>
            </div>

            <div class="space-y-4">
                @forelse($connections as $conn)
                    @php
                        $latestReading = $conn->meterReadings->first();
                        $currentUnits = $latestReading ? $latestReading->units_consumed : 0;
                    @endphp
                    <div class="flex justify-between items-center group cursor-pointer hover:bg-theme-border/30 p-2 -mx-2 rounded transition-colors {{ !$loop->first ? 'border-t border-theme-border/50 pt-4' : '' }}">
                        <div>
                            <p class="text-[10px] {{ $conn->status === 'active' ? 'text-theme-accent' : 'text-amber-500' }} font-bold mb-0.5">{{ $conn->connection_number }}</p>
                            <p class="text-sm font-medium text-theme-heading leading-tight">{{ ucwords(str_replace('_', ' ', $conn->connection_type)) }}</p>
                            <p class="text-xs text-theme-text">{{ $conn->field_name }}</p>
                        </div>
                        <div class="text-right flex items-center gap-3">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $conn->status === 'active' ? 'bg-[#16271c] text-emerald-400 border border-emerald-500/20' : ($conn->status === 'pending' ? 'bg-[#2b1f13] text-amber-500 border border-amber-500/20' : 'bg-red-500/20 text-red-400 border border-red-500/20') }}">{{ ucfirst($conn->status) }}</span>
                            <div class="text-right w-12">
                                <p class="text-sm font-bold text-theme-heading leading-none">{{ number_format($currentUnits) }}</p>
                                <p class="text-[9px] text-theme-text uppercase">kWh</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-theme-text">No connections yet. Request your first connection!</p>
                @endforelse
            </div>
        </div>

        {{-- Recent Complaints --}}
        <div class="utilitarian-card p-6 lg:col-span-2">
            <div class="flex justify-between items-start mb-6">
                <h3 class="text-lg font-bold text-theme-heading leading-tight">Recent<br>Complaints</h3>
                <button onclick="document.getElementById('complaintModal').classList.remove('hidden')"
                    class="border border-theme-border hover:bg-theme-border text-theme-heading text-xs font-bold px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                    File New
                </button>
            </div>

            <div class="space-y-4">
                @forelse($complaints as $complaint)
                    @php
                        $statusColors = [
                            'filed' => 'bg-blue-500/20 text-blue-400',
                            'assigned' => 'bg-amber-500/20 text-amber-500',
                            'in_review' => 'bg-indigo-500/20 text-indigo-400',
                            'resolved' => 'bg-emerald-500/20 text-emerald-400',
                            'closed' => 'bg-theme-border text-theme-text',
                        ];
                    @endphp
                    <div class="border border-theme-border rounded-lg p-4 bg-theme-bg/50">
                        <div class="flex justify-between items-center mb-2">
                            <p class="text-[10px] text-theme-text font-bold tracking-wider">#{{ $complaint->grv_number }}</p>
                            <span class="px-2.5 py-1 rounded-md text-[10px] font-bold {{ $statusColors[$complaint->status] ?? 'bg-theme-border text-theme-text' }}">{{ ucwords(str_replace('_', ' ', $complaint->status)) }}</span>
                        </div>
                        <h4 class="text-sm font-bold text-theme-heading mb-1">{{ ucwords(str_replace('_', ' ', $complaint->complaint_type)) }}</h4>
                        <p class="text-xs text-theme-text">
                            Filed {{ $complaint->filed_at->format('d M Y') }}
                            @if($complaint->resolved_at)
                                · Resolved {{ $complaint->resolved_at->format('d M Y') }}
                            @endif
                        </p>
                    </div>
                @empty
                    <div class="border border-theme-border rounded-lg p-6 bg-theme-bg/50 text-center">
                        <i class="fa-regular fa-comment-dots text-2xl text-theme-text mb-2"></i>
                        <p class="text-sm text-theme-text">No complaints filed yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Monthly Usage Breakdown --}}
        <div class="utilitarian-card p-6 lg:col-span-2">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-sm font-bold text-theme-heading">Monthly Usage Breakdown</h3>
                <span class="text-xs text-theme-text font-medium">{{ now()->format('F Y') }}</span>
            </div>

            <div class="space-y-5">
                @php
                    $maxUnits = collect($connectionUsage)->max('units') ?: 1;
                    $barColors = ['bg-emerald-500', 'bg-amber-500', 'bg-indigo-500', 'bg-cyan-500', 'bg-rose-500'];
                @endphp
                @foreach($connectionUsage as $index => $cu)
                    <div class="flex items-center gap-4">
                        <div class="w-20 text-xs font-medium text-theme-text truncate">{{ $cu['name'] }}</div>
                        <div class="flex-grow h-1.5 bg-[#1F2F24] rounded-full overflow-hidden">
                            <div class="h-full {{ $barColors[$index % count($barColors)] }}" style="width: {{ ($cu['units'] / $maxUnits) * 100 }}%"></div>
                        </div>
                        <div class="w-16 text-right">
                            <span class="text-xs font-bold text-theme-heading">{{ number_format($cu['units']) }}</span>
                            <span class="text-[9px] text-theme-text ml-0.5">kWh</span>
                        </div>
                    </div>
                @endforeach

                @if(count($connectionUsage) > 0)
                    <div class="pt-3 mt-3 border-t border-theme-border flex items-center gap-4 opacity-50">
                        <div class="w-20 text-xs font-medium text-theme-text">Previous</div>
                        <div class="flex-grow h-1.5 bg-[#1F2F24] rounded-full overflow-hidden">
                            <div class="h-full bg-[#3b4c40] w-full"></div>
                        </div>
                        <div class="w-16 text-right">
                            <span class="text-xs font-bold text-theme-heading">{{ number_format($previousMonthUnits) }}</span>
                            <span class="text-[9px] text-theme-text ml-0.5">kWh</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Active Subsidies --}}
        <div class="utilitarian-card p-6 lg:col-span-1 flex flex-col">
            <h3 class="text-sm font-bold text-theme-heading mb-6">Active Subsidies</h3>

            <div class="space-y-6 flex-grow">
                @forelse($subsidies as $sub)
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="text-sm font-bold text-theme-heading mb-1">{{ $sub->scheme->scheme_name }}</h4>
                            <p class="text-[10px] text-theme-text max-w-[140px] leading-snug">{{ Str::limit($sub->scheme->description, 40) }}</p>
                        </div>
                        <span class="text-emerald-400 font-bold text-sm bg-[#16271c] px-2 py-0.5 rounded border border-emerald-500/20">{{ number_format($sub->scheme->discount_percentage) }}%</span>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <i class="fa-solid fa-percent text-2xl text-theme-text mb-2"></i>
                        <p class="text-xs text-theme-text">No active subsidies</p>
                        <a href="{{ route('farmer.subsidies') }}" class="text-xs text-theme-accent hover:underline mt-1 inline-block">Apply for schemes →</a>
                    </div>
                @endforelse
            </div>

            @if($subsidies->count() > 0)
                <div class="border-t border-theme-border pt-4 mt-6">
                    <a href="{{ route('farmer.subsidies') }}" class="text-xs text-theme-accent hover:underline">View all schemes →</a>
                </div>
            @endif
        </div>
    </div>

    {{-- Connection Request Modal --}}
    <div id="connectionModal" class="hidden fixed inset-0 bg-[#0A110D]/80 backdrop-blur-sm z-50 flex items-center justify-center">
        <div class="utilitarian-card p-6 w-full max-w-md border-t-2 border-t-emerald-500 relative">
            <button onclick="document.getElementById('connectionModal').classList.add('hidden')"
                class="absolute top-4 right-4 text-theme-text hover:text-white"><i class="fa-solid fa-xmark"></i></button>
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
                <button type="submit"
                    class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-2.5 px-4 rounded-lg transition-all text-sm">Submit Request</button>
            </form>
        </div>
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
                        @foreach($connections as $conn)
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
                <button type="submit"
                    class="w-full bg-rose-600 hover:bg-rose-500 text-white font-bold py-2.5 px-4 rounded-lg transition-all text-sm">Submit Complaint</button>
            </form>
        </div>
    </div>
@endsection