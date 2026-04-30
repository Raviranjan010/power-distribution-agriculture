@extends('layouts.app')

@section('content')
<div class="mb-8">
    <h2 class="text-3xl font-bold text-theme-heading mb-1 flex items-center gap-2">
        Good morning, {{ explode(' ', Auth::user()->name)[0] }} ☀️
    </h2>
    <p class="text-sm text-theme-text">
        Farmer ID: KV-2024-882{{ Auth::user()->farmer->id ?? '1' }} - {{ Auth::user()->farmer->land_address ?? 'Nawanshahr, Punjab' }} - Last updated: {{ now()->format('d M Y, h:i A') }}
    </p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <!-- Active Connections -->
    <div class="utilitarian-card p-5 relative overflow-hidden group border-t border-t-emerald-500 hover:border-emerald-500 transition-colors">
        <div class="absolute top-4 right-4 w-8 h-8 rounded-lg bg-[#16271c] border border-emerald-500/30 text-emerald-500 flex items-center justify-center">
            <i class="fa-solid fa-bolt"></i>
        </div>
        <p class="text-[10px] text-theme-text font-bold tracking-widest uppercase mb-1">Active<br>Connections</p>
        <h3 class="text-4xl font-bold text-emerald-400 mb-2">3</h3>
        <p class="text-xs text-theme-text flex items-center gap-1">
            <i class="fa-solid fa-arrow-up text-emerald-500 text-[10px]"></i> All operational
        </p>
    </div>

    <!-- Units This Month -->
    <div class="utilitarian-card p-5 relative overflow-hidden group border-t border-t-amber-500 hover:border-amber-500 transition-colors">
        <div class="absolute top-4 right-4 w-8 h-8 rounded-lg bg-[#2b1f13] border border-amber-500/30 text-amber-500 flex items-center justify-center">
            <i class="fa-solid fa-chart-line"></i>
        </div>
        <p class="text-[10px] text-theme-text font-bold tracking-widest uppercase mb-1">Units This<br>Month</p>
        <h3 class="text-4xl font-bold text-amber-400 mb-2">2,847</h3>
        <p class="text-xs text-theme-text">kWh consumed</p>
    </div>

    <!-- Active Subsidies -->
    <div class="utilitarian-card p-5 relative overflow-hidden group border-t border-t-indigo-500 hover:border-indigo-500 transition-colors">
        <div class="absolute top-4 right-4 w-8 h-8 rounded-lg bg-[#1a1b36] border border-indigo-500/30 text-indigo-500 flex items-center justify-center">
            <i class="fa-solid fa-percent"></i>
        </div>
        <p class="text-[10px] text-theme-text font-bold tracking-widest uppercase mb-1">Active<br>Subsidies</p>
        <h3 class="text-4xl font-bold text-indigo-400 mb-2">2</h3>
        <p class="text-xs text-theme-text">PM-KUSUM + State</p>
    </div>

    <!-- Outstanding Bills -->
    <div class="utilitarian-card p-5 relative overflow-hidden group border-t border-t-rose-500 hover:border-rose-500 transition-colors">
        <div class="absolute top-4 right-4 w-8 h-8 rounded-lg bg-[#33181e] border border-rose-500/30 text-rose-500 flex items-center justify-center">
            <i class="fa-solid fa-wallet"></i>
        </div>
        <p class="text-[10px] text-theme-text font-bold tracking-widest uppercase mb-1">Outstanding<br>Bills</p>
        <h3 class="text-4xl font-bold text-rose-400 mb-2">₹1,240</h3>
        <p class="text-xs text-theme-text">Due 15 May 2026</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Your Connections -->
    <div class="utilitarian-card p-6 lg:col-span-1">
        <div class="flex justify-between items-start mb-6">
            <h3 class="text-lg font-bold text-theme-heading leading-tight">Your<br>Connections</h3>
            <button onclick="document.getElementById('connectionModal').classList.remove('hidden')" class="border border-theme-border hover:bg-theme-border text-theme-heading text-xs font-bold px-3 py-2 rounded-lg transition-colors flex items-center gap-2">
                <i class="fa-solid fa-plus text-theme-accent"></i> Request New
            </button>
        </div>
        
        <div class="space-y-4">
            <!-- Connection 1 -->
            <div class="flex justify-between items-center group cursor-pointer hover:bg-theme-border/30 p-2 -mx-2 rounded transition-colors">
                <div>
                    <p class="text-[10px] text-theme-accent font-bold mb-0.5">KV-CN-001</p>
                    <p class="text-sm font-medium text-theme-heading leading-tight">Tubewell Pump</p>
                    <p class="text-xs text-theme-text">Field A</p>
                </div>
                <div class="text-right flex items-center gap-3">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-[#16271c] text-emerald-400 border border-emerald-500/20">Active</span>
                    <div class="text-right w-12">
                        <p class="text-sm font-bold text-theme-heading leading-none">840</p>
                        <p class="text-[9px] text-theme-text uppercase">kWh</p>
                    </div>
                </div>
            </div>
            
            <!-- Connection 2 -->
            <div class="flex justify-between items-center group cursor-pointer hover:bg-theme-border/30 p-2 -mx-2 rounded transition-colors border-t border-theme-border/50 pt-4">
                <div>
                    <p class="text-[10px] text-theme-accent font-bold mb-0.5">KV-CN-002</p>
                    <p class="text-sm font-medium text-theme-heading leading-tight">Irrigation Motor</p>
                    <p class="text-xs text-theme-text">Field B</p>
                </div>
                <div class="text-right flex items-center gap-3">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-[#16271c] text-emerald-400 border border-emerald-500/20">Active</span>
                    <div class="text-right w-12">
                        <p class="text-sm font-bold text-theme-heading leading-none">1,120</p>
                        <p class="text-[9px] text-theme-text uppercase">kWh</p>
                    </div>
                </div>
            </div>

            <!-- Connection 3 -->
            <div class="flex justify-between items-center group cursor-pointer hover:bg-theme-border/30 p-2 -mx-2 rounded transition-colors border-t border-theme-border/50 pt-4">
                <div>
                    <p class="text-[10px] text-amber-500 font-bold mb-0.5">KV-CN-003</p>
                    <p class="text-sm font-medium text-theme-heading leading-tight">Grain Dryer</p>
                    <p class="text-xs text-theme-text">Storage</p>
                </div>
                <div class="text-right flex items-center gap-3">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-[#2b1f13] text-amber-500 border border-amber-500/20">Pending</span>
                    <div class="text-right w-12">
                        <p class="text-sm font-bold text-theme-heading leading-none">887</p>
                        <p class="text-[9px] text-theme-text uppercase">kWh</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Complaints -->
    <div class="utilitarian-card p-6 lg:col-span-2">
        <div class="flex justify-between items-start mb-6">
            <h3 class="text-lg font-bold text-theme-heading leading-tight">Recent<br>Complaints</h3>
            <button onclick="document.getElementById('complaintModal').classList.remove('hidden')" class="border border-theme-border hover:bg-theme-border text-theme-heading text-xs font-bold px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                File New
            </button>
        </div>
        
        <div class="space-y-4">
            <!-- Complaint 1 -->
            <div class="border border-theme-border rounded-lg p-4 bg-theme-bg/50">
                <div class="flex justify-between items-center mb-2">
                    <p class="text-[10px] text-theme-text font-bold tracking-wider">#GRV-2026-0341</p>
                    <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-[#2b1f13] text-amber-500">In Review</span>
                </div>
                <h4 class="text-sm font-bold text-theme-heading mb-1">Voltage fluctuation - Field B motor</h4>
                <p class="text-xs text-theme-text">Filed 22 Apr 2026</p>
            </div>
            
            <!-- Complaint 2 -->
            <div class="border border-theme-border rounded-lg p-4 bg-theme-bg/50">
                <div class="flex justify-between items-center mb-2">
                    <p class="text-[10px] text-theme-text font-bold tracking-wider">#GRV-2026-0289</p>
                    <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-[#16271c] text-emerald-400 border border-emerald-500/20">Resolved</span>
                </div>
                <h4 class="text-sm font-bold text-theme-heading mb-1">Meter reading discrepancy</h4>
                <p class="text-xs text-theme-text">Filed 08 Apr 2026 · Closed 14 Apr</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Monthly Usage Breakdown -->
    <div class="utilitarian-card p-6 lg:col-span-2">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-sm font-bold text-theme-heading">Monthly Usage Breakdown</h3>
            <span class="text-xs text-theme-text font-medium">April 2026</span>
        </div>
        
        <div class="space-y-5">
            <!-- Progress Bar 1 -->
            <div class="flex items-center gap-4">
                <div class="w-16 text-xs font-medium text-theme-text">Field A</div>
                <div class="flex-grow h-1.5 bg-[#1F2F24] rounded-full overflow-hidden">
                    <div class="h-full bg-emerald-500 w-[30%]"></div>
                </div>
                <div class="w-16 text-right">
                    <span class="text-xs font-bold text-theme-heading">840</span>
                    <span class="text-[9px] text-theme-text ml-0.5">kWh</span>
                </div>
            </div>
            
            <!-- Progress Bar 2 -->
            <div class="flex items-center gap-4">
                <div class="w-16 text-xs font-medium text-theme-text">Field B</div>
                <div class="flex-grow h-1.5 bg-[#1F2F24] rounded-full overflow-hidden">
                    <div class="h-full bg-amber-500 w-[45%]"></div>
                </div>
                <div class="w-16 text-right">
                    <span class="text-xs font-bold text-theme-heading">1,120</span>
                    <span class="text-[9px] text-theme-text ml-0.5">kWh</span>
                </div>
            </div>

            <!-- Progress Bar 3 -->
            <div class="flex items-center gap-4">
                <div class="w-16 text-xs font-medium text-theme-text">Storage</div>
                <div class="flex-grow h-1.5 bg-[#1F2F24] rounded-full overflow-hidden">
                    <div class="h-full bg-indigo-500 w-[25%]"></div>
                </div>
                <div class="w-16 text-right">
                    <span class="text-xs font-bold text-theme-heading">887</span>
                    <span class="text-[9px] text-theme-text ml-0.5">kWh</span>
                </div>
            </div>
            
            <div class="pt-3 mt-3 border-t border-theme-border flex items-center gap-4 opacity-50">
                <div class="w-16 text-xs font-medium text-theme-text">Previous</div>
                <div class="flex-grow h-1.5 bg-[#1F2F24] rounded-full overflow-hidden">
                    <div class="h-full bg-[#3b4c40] w-[100%]"></div>
                </div>
                <div class="w-16 text-right">
                    <span class="text-xs font-bold text-theme-heading">2,980</span>
                    <span class="text-[9px] text-theme-text ml-0.5">kWh</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Subsidies -->
    <div class="utilitarian-card p-6 lg:col-span-1 flex flex-col">
        <h3 class="text-sm font-bold text-theme-heading mb-6">Active Subsidies</h3>
        
        <div class="space-y-6 flex-grow">
            <div class="flex justify-between items-start">
                <div>
                    <h4 class="text-sm font-bold text-theme-heading mb-1">PM-KUSUM Scheme</h4>
                    <p class="text-[10px] text-theme-text max-w-[140px] leading-snug">Solar pump - Central Govt.</p>
                </div>
                <span class="text-emerald-400 font-bold text-sm bg-[#16271c] px-2 py-0.5 rounded border border-emerald-500/20">₹3,600</span>
            </div>
            
            <div class="flex justify-between items-start">
                <div>
                    <h4 class="text-sm font-bold text-theme-heading mb-1">Punjab State Agri</h4>
                    <p class="text-[10px] text-theme-text max-w-[140px] leading-snug">Flat-rate waiver - 50%</p>
                </div>
                <span class="text-emerald-400 font-bold text-sm bg-[#16271c] px-2 py-0.5 rounded border border-emerald-500/20">₹1,200</span>
            </div>
        </div>
        
        <div class="border-t border-theme-border pt-4 mt-6 flex justify-between items-end">
            <p class="text-xs text-theme-text w-24 leading-snug">Total relief this month</p>
            <span class="text-emerald-400 font-bold text-lg">₹4,800</span>
        </div>
    </div>
</div>

<!-- Connection Modal -->
<div id="connectionModal" class="hidden fixed inset-0 bg-[#0A110D]/80 backdrop-blur-sm z-50 flex items-center justify-center">
    <div class="utilitarian-card p-6 w-full max-w-md border-t-2 border-t-emerald-500 relative">
        <button onclick="document.getElementById('connectionModal').classList.add('hidden')" class="absolute top-4 right-4 text-theme-text hover:text-white"><i class="fa-solid fa-xmark"></i></button>
        <h3 class="text-xl font-bold text-theme-heading mb-4">Request New Connection</h3>
        <form method="POST" action="{{ route('farmer.connection.store') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-bold text-theme-text tracking-widest uppercase mb-2">Requested Load (kW)</label>
                <input type="number" name="requested_load_kw" min="1" max="50" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-4 py-2.5 text-white focus:border-emerald-500 focus:outline-none" required>
            </div>
            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-2.5 px-4 rounded-lg transition-all text-sm">Submit Request</button>
        </form>
    </div>
</div>

<!-- Complaint Modal -->
<div id="complaintModal" class="hidden fixed inset-0 bg-[#0A110D]/80 backdrop-blur-sm z-50 flex items-center justify-center">
    <div class="utilitarian-card p-6 w-full max-w-md border-t-2 border-t-rose-500 relative">
        <button onclick="document.getElementById('complaintModal').classList.add('hidden')" class="absolute top-4 right-4 text-theme-text hover:text-white"><i class="fa-solid fa-xmark"></i></button>
        <h3 class="text-xl font-bold text-theme-heading mb-4">File a Complaint</h3>
        <form method="POST" action="{{ route('farmer.complaint.store') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-bold text-theme-text tracking-widest uppercase mb-2">Subject</label>
                <input type="text" name="subject" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-4 py-2.5 text-white focus:border-rose-500 focus:outline-none" required>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-theme-text tracking-widest uppercase mb-2">Description</label>
                <textarea name="description" rows="4" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-4 py-2.5 text-white focus:border-rose-500 focus:outline-none" required></textarea>
            </div>
            <button type="submit" class="w-full bg-rose-600 hover:bg-rose-500 text-white font-bold py-2.5 px-4 rounded-lg transition-all text-sm">Submit Complaint</button>
        </form>
    </div>
</div>
@endsection
