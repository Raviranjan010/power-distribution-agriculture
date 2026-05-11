@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-8">
    <div>
        <h2 class="text-3xl font-bold text-theme-heading mb-1">User Management</h2>
        <p class="text-sm text-theme-text">Manage all system users</p>
    </div>
    <button onclick="document.getElementById('create-user-modal').classList.remove('hidden')" class="bg-theme-accent hover:bg-theme-hover text-white text-xs font-bold px-4 py-2.5 rounded-lg transition-colors flex items-center gap-2">
        <i class="fa-solid fa-user-plus"></i> Create User
    </button>
</div>

{{-- Create User Modal --}}
<div id="create-user-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="bg-theme-panel border border-theme-border rounded-xl p-8 w-full max-w-lg mx-4 shadow-2xl">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold text-theme-heading">Create New User</h3>
            <button onclick="document.getElementById('create-user-modal').classList.add('hidden')" class="text-theme-text hover:text-white transition-colors">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] text-theme-text font-bold tracking-widest uppercase mb-1">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-3 py-2.5 text-white text-sm focus:outline-none focus:border-theme-accent" required>
                </div>
                <div>
                    <label class="block text-[10px] text-theme-text font-bold tracking-widest uppercase mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-3 py-2.5 text-white text-sm focus:outline-none focus:border-theme-accent" required>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] text-theme-text font-bold tracking-widest uppercase mb-1">Password</label>
                    <input type="password" name="password" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-3 py-2.5 text-white text-sm focus:outline-none focus:border-theme-accent" required>
                </div>
                <div>
                    <label class="block text-[10px] text-theme-text font-bold tracking-widest uppercase mb-1">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" maxlength="15" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-3 py-2.5 text-white text-sm focus:outline-none focus:border-theme-accent" required>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] text-theme-text font-bold tracking-widest uppercase mb-1">Role</label>
                    <select name="role" id="create-user-role" onchange="document.getElementById('zone-field').style.display = (this.value === 'admin') ? 'none' : 'block'" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-3 py-2.5 text-white text-sm focus:outline-none focus:border-theme-accent" required>
                        <option value="">Select Role</option>
                        <option value="sdo" {{ old('role') == 'sdo' ? 'selected' : '' }}>SDO (Officer)</option>
                        <option value="lineman" {{ old('role') == 'lineman' ? 'selected' : '' }}>Lineman</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
                <div id="zone-field">
                    <label class="block text-[10px] text-theme-text font-bold tracking-widest uppercase mb-1">Zone</label>
                    <select name="zone_id" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-3 py-2.5 text-white text-sm focus:outline-none focus:border-theme-accent">
                        <option value="">Select Zone</option>
                        @foreach($zones as $zone)
                            <option value="{{ $zone->id }}" {{ old('zone_id') == $zone->id ? 'selected' : '' }}>{{ $zone->name }} ({{ $zone->district }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <button type="submit" class="w-full bg-theme-accent hover:bg-theme-hover text-white font-bold py-2.5 rounded-lg transition-colors text-sm mt-2">
                Create User
            </button>
        </form>
    </div>
</div>

<div class="utilitarian-card p-6">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[10px] text-theme-text font-bold tracking-widest uppercase border-b border-theme-border">
                    <th class="pb-3 pr-4">Name</th>
                    <th class="pb-3 pr-4">Email</th>
                    <th class="pb-3 pr-4">Role</th>
                    <th class="pb-3 pr-4">Farmer ID</th>
                    <th class="pb-3 pr-4">District</th>
                    <th class="pb-3 pr-4">Status</th>
                    <th class="pb-3">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr class="border-b border-theme-border/50 hover:bg-theme-border/20 transition-colors">
                        <td class="py-3 pr-4 font-medium text-theme-heading">{{ $user->name }}</td>
                        <td class="py-3 pr-4 text-theme-text text-xs">{{ $user->email }}</td>
                        <td class="py-3 pr-4">
                            @php
                                $roleColors = ['admin'=>'bg-rose-500/20 text-rose-400','sdo'=>'bg-indigo-500/20 text-indigo-400','lineman'=>'bg-amber-500/20 text-amber-500','farmer'=>'bg-emerald-500/20 text-emerald-400'];
                            @endphp
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $roleColors[$user->role] ?? '' }}">{{ strtoupper($user->role) }}</span>
                        </td>
                        <td class="py-3 pr-4 text-theme-text text-xs">{{ $user->farmer_id_number ?? '—' }}</td>
                        <td class="py-3 pr-4 text-theme-text text-xs">{{ $user->district ?? '—' }}</td>
                        <td class="py-3 pr-4">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $user->is_active ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="py-3">
                            <form method="POST" action="{{ route('admin.users.toggle', $user->id) }}">
                                @csrf
                                <button type="submit" class="text-xs font-bold px-3 py-1.5 rounded-lg transition-colors {{ $user->is_active ? 'bg-red-500/20 text-red-400 hover:bg-red-500 hover:text-white' : 'bg-emerald-500/20 text-emerald-400 hover:bg-emerald-500 hover:text-white' }}">
                                    {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $users->links() }}</div>
</div>

@if($errors->any())
<script>document.getElementById('create-user-modal').classList.remove('hidden');</script>
@endif
@endsection
