@extends('layouts.admin')
@section('title', 'HQ Central Management')

@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-slate-900 to-indigo-900 p-8 rounded-3xl text-white shadow-premium flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <h1 class="text-2xl font-black mt-2">HQ Central Platform</h1>
            <p class="text-xs text-slate-300 mt-1">Manage connected SaaS instances, view telemetry, and dispatch remote commands.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.platform.hq_central.systems.index') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition-colors">
                View Systems
            </a>
            <a href="{{ route('admin.platform.hq_central.tenants.index') }}" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-xl text-xs font-bold transition-colors border border-white/20">
                Manage Tenants
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- System Overview -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <h3 class="text-xs font-black text-neutral-500 uppercase tracking-wider mb-4 border-b border-neutral-100 dark:border-neutral-800 pb-2">System Overview</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-2xl font-black text-neutral-900 dark:text-white">{{ $metrics['systems']['total'] }}</p>
                    <p class="text-[10px] font-bold text-neutral-500 uppercase">Total Systems</p>
                </div>
                <div>
                    <p class="text-2xl font-black text-green-600">{{ $metrics['systems']['online'] }}</p>
                    <p class="text-[10px] font-bold text-neutral-500 uppercase">Online</p>
                </div>
                <div>
                    <p class="text-2xl font-black text-red-600">{{ $metrics['systems']['offline'] }}</p>
                    <p class="text-[10px] font-bold text-neutral-500 uppercase">Offline</p>
                </div>
                <div>
                    <p class="text-2xl font-black text-amber-600">{{ $metrics['systems']['unknown'] }}</p>
                    <p class="text-[10px] font-bold text-neutral-500 uppercase">Unknown</p>
                </div>
            </div>
        </div>

        <!-- Tenant Overview -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <h3 class="text-xs font-black text-neutral-500 uppercase tracking-wider mb-4 border-b border-neutral-100 dark:border-neutral-800 pb-2">Tenant Overview</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-2xl font-black text-neutral-900 dark:text-white">{{ $metrics['tenants']['total'] }}</p>
                    <p class="text-[10px] font-bold text-neutral-500 uppercase">Total Tenants</p>
                </div>
                <div>
                    <p class="text-2xl font-black text-indigo-600">{{ $metrics['tenants']['active'] }}</p>
                    <p class="text-[10px] font-bold text-neutral-500 uppercase">Active</p>
                </div>
                <div>
                    <p class="text-2xl font-black text-red-600">{{ $metrics['tenants']['suspended'] }}</p>
                    <p class="text-[10px] font-bold text-neutral-500 uppercase">Suspended</p>
                </div>
            </div>
        </div>

        <!-- License Overview -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm lg:col-span-1">
            <div class="flex justify-between items-center mb-4 border-b border-neutral-100 dark:border-neutral-800 pb-2">
                <h3 class="text-xs font-black text-neutral-500 uppercase tracking-wider">License Status</h3>
                <a href="{{ route('admin.platform.hq_central.licenses.index') }}" class="text-[10px] font-bold text-indigo-600 hover:text-indigo-800">Manage</a>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-2xl font-black text-neutral-900 dark:text-white">{{ $metrics['licenses']['total'] }}</p>
                    <p class="text-[10px] font-bold text-neutral-500 uppercase">Total Licenses</p>
                </div>
                <div>
                    <p class="text-2xl font-black text-green-600">{{ $metrics['licenses']['active'] }}</p>
                    <p class="text-[10px] font-bold text-neutral-500 uppercase">Active</p>
                </div>
                <div>
                    <p class="text-2xl font-black text-red-600">{{ $metrics['licenses']['expired'] }}</p>
                    <p class="text-[10px] font-bold text-neutral-500 uppercase">Expired</p>
                </div>
                <div>
                    <p class="text-2xl font-black text-amber-600">{{ $metrics['licenses']['expiring_soon'] }}</p>
                    <p class="text-[10px] font-bold text-neutral-500 uppercase">Expiring (<30d)</p>
                </div>
            </div>
        </div>

        <!-- Communication Health -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <h3 class="text-xs font-black text-neutral-500 uppercase tracking-wider mb-4 border-b border-neutral-100 dark:border-neutral-800 pb-2">Communication Health</h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <p class="text-lg font-black text-neutral-900 dark:text-white font-mono">
                        {{ $metrics['communication']['last_heartbeat'] ? \Carbon\Carbon::parse($metrics['communication']['last_heartbeat'])->diffForHumans() : 'Never' }}
                    </p>
                    <p class="text-[10px] font-bold text-neutral-500 uppercase">Last Heartbeat</p>
                </div>
                <div>
                    <p class="text-2xl font-black text-red-600">{{ $metrics['communication']['failed_requests'] }}</p>
                    <p class="text-[10px] font-bold text-neutral-500 uppercase">Failed (24h)</p>
                </div>
                <div>
                    <p class="text-2xl font-black text-indigo-600">{{ $metrics['communication']['avg_response_time'] }}ms</p>
                    <p class="text-[10px] font-bold text-neutral-500 uppercase">Avg Response</p>
                </div>
            </div>
        </div>

        <!-- Command Queue -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <div class="flex justify-between items-center mb-4 border-b border-neutral-100 dark:border-neutral-800 pb-2">
                <h3 class="text-xs font-black text-neutral-500 uppercase tracking-wider">Command Queue</h3>
                <a href="{{ route('admin.hq.commands.index') }}" class="text-[10px] font-bold text-indigo-600 hover:text-indigo-800">Manage</a>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <p class="text-2xl font-black text-amber-500">{{ $metrics['commands']['pending'] }}</p>
                    <p class="text-[10px] font-bold text-neutral-500 uppercase">Pending</p>
                </div>
                <div>
                    <p class="text-2xl font-black text-green-600">{{ $metrics['commands']['completed'] }}</p>
                    <p class="text-[10px] font-bold text-neutral-500 uppercase">Completed</p>
                </div>
                <div>
                    <p class="text-2xl font-black text-red-600">{{ $metrics['commands']['failed'] }}</p>
                    <p class="text-[10px] font-bold text-neutral-500 uppercase">Failed</p>
                </div>
            </div>
        </div>

        <!-- Update Overview -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm lg:col-span-1">
            <div class="flex justify-between items-center mb-4 border-b border-neutral-100 dark:border-neutral-800 pb-2">
                <h3 class="text-xs font-black text-neutral-500 uppercase tracking-wider">Updates & Deployments</h3>
                <div class="flex gap-2">
                    <a href="{{ route('admin.platform.hq_central.versions.index') }}" class="text-[10px] font-bold text-indigo-600 hover:text-indigo-800">Versions</a>
                    <a href="{{ route('admin.platform.hq_central.updates.index') }}" class="text-[10px] font-bold text-indigo-600 hover:text-indigo-800">Queue</a>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-2xl font-black text-neutral-900 dark:text-white">{{ $metrics['updates']['latest_version'] }}</p>
                    <p class="text-[10px] font-bold text-neutral-500 uppercase">Latest Version</p>
                </div>
                <div>
                    <p class="text-2xl font-black text-amber-600">{{ $metrics['updates']['behind_latest'] }}</p>
                    <p class="text-[10px] font-bold text-neutral-500 uppercase">Instances Behind</p>
                </div>
                <div>
                    <p class="text-2xl font-black text-red-600">{{ $metrics['updates']['mandatory_updates'] }}</p>
                    <p class="text-[10px] font-bold text-neutral-500 uppercase">Mandatory Releases</p>
                </div>
                <div>
                    <p class="text-2xl font-black text-indigo-600">{{ $metrics['updates']['running'] }}</p>
                    <p class="text-[10px] font-bold text-neutral-500 uppercase">Running Deployments</p>
                </div>
            </div>
        </div>

        <!-- Telemetry Overview -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm lg:col-span-2">
            <h3 class="text-xs font-black text-neutral-500 uppercase tracking-wider mb-4 border-b border-neutral-100 dark:border-neutral-800 pb-2">Telemetry Insights (Avg of Last 10)</h3>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <p class="text-2xl font-black text-indigo-600">{{ $metrics['telemetry']['avg_memory'] }}{{ is_numeric($metrics['telemetry']['avg_memory']) ? '%' : '' }}</p>
                    <p class="text-[10px] font-bold text-neutral-500 uppercase">Avg Memory</p>
                </div>
                <div>
                    <p class="text-2xl font-black text-indigo-600">{{ $metrics['telemetry']['avg_storage'] }}{{ is_numeric($metrics['telemetry']['avg_storage']) ? '%' : '' }}</p>
                    <p class="text-[10px] font-bold text-neutral-500 uppercase">Avg Storage</p>
                </div>
                <div>
                    <p class="text-2xl font-black text-green-600">{{ $metrics['telemetry']['db_health'] }}</p>
                    <p class="text-[10px] font-bold text-neutral-500 uppercase">Global DB Health</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
