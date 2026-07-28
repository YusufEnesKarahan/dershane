@extends('layouts.admin')
@section('title', 'HQ Central Management')

@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-slate-900 to-indigo-900 p-8 rounded-3xl text-white shadow-premium flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <h1 class="text-2xl font-black mt-2">HQ Central Platform</h1>
            <p class="text-xs text-slate-300 mt-1">Manage connected SaaS instances, view telemetry, and dispatch remote commands.</p>
        </div>
    </div>

    <!-- Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-white dark:bg-neutral-900 p-4 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <span class="text-[10px] font-bold text-neutral-500 uppercase tracking-wider">Total Systems</span>
            <p class="text-lg font-black mt-1 text-indigo-600">
                {{ $metrics['total_systems'] }}
            </p>
        </div>
        <div class="bg-white dark:bg-neutral-900 p-4 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <span class="text-[10px] font-bold text-neutral-500 uppercase tracking-wider">Online</span>
            <p class="text-lg font-black mt-1 text-green-600">
                {{ $metrics['online_systems'] }}
            </p>
        </div>
        <div class="bg-white dark:bg-neutral-900 p-4 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <span class="text-[10px] font-bold text-neutral-500 uppercase tracking-wider">Offline</span>
            <p class="text-lg font-black mt-1 text-red-600">
                {{ $metrics['offline_systems'] }}
            </p>
        </div>
        <div class="bg-white dark:bg-neutral-900 p-4 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <span class="text-[10px] font-bold text-neutral-500 uppercase tracking-wider">Pending Cmds</span>
            <p class="text-lg font-black text-neutral-900 dark:text-white mt-1">
                {{ $metrics['pending_commands'] }}
            </p>
        </div>
        <div class="bg-white dark:bg-neutral-900 p-4 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <span class="text-[10px] font-bold text-neutral-500 uppercase tracking-wider">Failed Conn (24h)</span>
            <p class="text-lg font-black mt-1 text-red-600">
                {{ $metrics['failed_connections'] }}
            </p>
        </div>
    </div>

    <!-- Systems List -->
    <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-neutral-100 dark:border-neutral-800 flex justify-between items-center bg-neutral-50/50 dark:bg-neutral-800/50">
            <h3 class="font-bold text-neutral-800 dark:text-neutral-200">Connected Instances</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-neutral-600 dark:text-neutral-400">
                <thead class="bg-neutral-50 dark:bg-neutral-800/50 text-neutral-500 font-bold uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="px-6 py-4">System ID</th>
                        <th class="px-6 py-4">Name</th>
                        <th class="px-6 py-4">Tenant</th>
                        <th class="px-6 py-4">Environment</th>
                        <th class="px-6 py-4">Version</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Last Seen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    @forelse($instances as $instance)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                            <td class="px-6 py-4 font-mono text-xs text-neutral-800 dark:text-neutral-300">
                                {{ Str::limit($instance->system_uuid, 8) }}
                            </td>
                            <td class="px-6 py-4 font-bold text-neutral-900 dark:text-white">
                                {{ $instance->system_name }}
                            </td>
                            <td class="px-6 py-4 text-xs font-bold text-indigo-600">
                                {{ $instance->tenant->name ?? 'Unknown' }}
                            </td>
                            <td class="px-6 py-4 text-xs uppercase font-bold text-neutral-500">
                                {{ $instance->environment }}
                            </td>
                            <td class="px-6 py-4 text-xs font-bold text-neutral-700 dark:text-neutral-300">
                                v{{ $instance->system_version }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded text-[10px] font-bold uppercase 
                                    @if($instance->status === 'online') bg-green-100 text-green-700 
                                    @elseif($instance->status === 'offline') bg-red-100 text-red-700 
                                    @else bg-neutral-100 text-neutral-700 @endif">
                                    {{ $instance->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-xs font-mono">
                                {{ $instance->last_seen_at ? $instance->last_seen_at->diffForHumans() : 'Never' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-neutral-500 font-bold">
                                Hiçbir sistem bağlantısı bulunamadı.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-neutral-100 dark:border-neutral-800">
            {{ $instances->links() }}
        </div>
    </div>
</div>
@endsection
