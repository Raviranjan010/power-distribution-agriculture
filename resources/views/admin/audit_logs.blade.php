@extends('layouts.app')

@section('content')
<div class="mb-8">
    <h2 class="text-3xl font-bold text-theme-heading mb-1">Audit Logs</h2>
    <p class="text-sm text-theme-text">System activity log</p>
</div>

<div class="utilitarian-card p-6">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[10px] text-theme-text font-bold tracking-widest uppercase border-b border-theme-border">
                    <th class="pb-3 pr-4">Date</th>
                    <th class="pb-3 pr-4">User</th>
                    <th class="pb-3 pr-4">Action</th>
                    <th class="pb-3 pr-4">Model</th>
                    <th class="pb-3">IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr class="border-b border-theme-border/50 hover:bg-theme-border/20 transition-colors">
                        <td class="py-3 pr-4 text-theme-text text-xs">{{ $log->created_at->format('d M Y H:i') }}</td>
                        <td class="py-3 pr-4 font-medium text-theme-heading">{{ $log->user->name ?? 'System' }}</td>
                        <td class="py-3 pr-4 text-theme-text">{{ $log->action }}</td>
                        <td class="py-3 pr-4 text-theme-text text-xs">{{ $log->model_type }} #{{ $log->model_id }}</td>
                        <td class="py-3 text-theme-text text-xs">{{ $log->ip_address }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-6 text-center text-theme-text">No audit logs yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $logs->links() }}</div>
</div>
@endsection
