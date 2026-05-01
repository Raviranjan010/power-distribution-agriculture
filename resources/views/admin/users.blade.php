@extends('layouts.app')

@section('content')
<div class="mb-8">
    <h2 class="text-3xl font-bold text-theme-heading mb-1">User Management</h2>
    <p class="text-sm text-theme-text">Manage all system users</p>
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
                            <form method="POST" action="{{ route('admin.user.toggle', $user->id) }}">
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
@endsection
