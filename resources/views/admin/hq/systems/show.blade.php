@extends('layouts.admin')
@section('title', 'System Details - ' . $system->system_name)

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-neutral-900 dark:text-white">{{ $system->system_name }}</h1>
            <p class="text-xs text-neutral-500 font-mono">{{ $system->system_uuid }}</p>
        </div>
        <div>
            <a href="{{ route('admin.platform.hq_central.systems.index') }}" class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-neutral-700 dark:text-neutral-300 rounded-xl text-xs font-bold transition-colors">
                &larr; Back to Systems
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        <!-- Identity -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <h3 class="text-xs font-black text-neutral-500 uppercase tracking-wider mb-4 border-b border-neutral-100 dark:border-neutral-800 pb-2">Identity</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-[10px] uppercase font-bold text-neutral-500">Tenant</span>
                    <span class="text-sm font-bold text-indigo-600">{{ $system->tenant->name ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[10px] uppercase font-bold text-neutral-500">Version</span>
                    <span class="text-sm font-bold text-neutral-900 dark:text-white">v{{ $system->system_version }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[10px] uppercase font-bold text-neutral-500">Environment</span>
                    <span class="text-sm uppercase font-bold text-neutral-900 dark:text-white">{{ $system->environment }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[10px] uppercase font-bold text-neutral-500">Status</span>
                    <span class="px-2 py-1 rounded text-[10px] font-bold uppercase 
                        @if($system->status === 'online') bg-green-100 text-green-700 
                        @elseif($system->status === 'offline') bg-red-100 text-red-700 
                        @else bg-neutral-100 text-neutral-700 @endif">
                        {{ $system->status }}
                    </span>
                </div>
            </div>
            
            @php $currentLicense = $system->currentLicense; @endphp
            <div class="mt-6 pt-4 border-t border-neutral-100 dark:border-neutral-800">
                <h4 class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-3">Active License</h4>
                @if($currentLicense)
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold text-neutral-500">Plan</span>
                            <span class="text-xs font-bold text-indigo-600">{{ $currentLicense->plan }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold text-neutral-500">Expires</span>
                            <span class="text-xs font-bold {{ $currentLicense->expires_at && $currentLicense->expires_at->isPast() ? 'text-red-600' : 'text-green-600' }}">
                                {{ $currentLicense->expires_at ? $currentLicense->expires_at->format('Y-m-d') : 'Lifetime' }}
                            </span>
                        </div>
                        <div class="mt-2 text-right">
                            <a href="{{ route('admin.platform.hq_central.licenses.show', $currentLicense->id) }}" class="text-[10px] font-bold text-indigo-600 hover:text-indigo-800">View License Details &rarr;</a>
                        </div>
                    </div>
                @else
                    <p class="text-xs text-red-500 font-bold">No active license found.</p>
                @endif
            </div>
        </div>

        <!-- Latest Telemetry -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm xl:col-span-2">
            <h3 class="text-xs font-black text-neutral-500 uppercase tracking-wider mb-4 border-b border-neutral-100 dark:border-neutral-800 pb-2">Latest Telemetry</h3>
            @if($system->telemetry->count() > 0)
                @php $latest = $system->telemetry->first(); @endphp
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-[10px] font-bold text-neutral-500 uppercase">Received At</p>
                        <p class="text-sm font-bold text-neutral-900 dark:text-white font-mono">{{ $latest->received_at->format('Y-m-d H:i:s') }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-neutral-500 uppercase">Type</p>
                        <p class="text-sm font-bold text-neutral-900 dark:text-white">{{ $latest->type }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-neutral-500 uppercase">Memory Usage</p>
                        <p class="text-sm font-bold text-indigo-600">{{ $latest->payload['memory_usage_percent'] ?? 'N/A' }}%</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-neutral-500 uppercase">Storage Usage</p>
                        <p class="text-sm font-bold text-indigo-600">{{ $latest->payload['storage_usage_percent'] ?? 'N/A' }}%</p>
                    </div>
                    <div class="col-span-2 md:col-span-4 mt-2">
                        <p class="text-[10px] font-bold text-neutral-500 uppercase mb-1">Raw Payload</p>
                        <pre class="text-[10px] text-neutral-600 dark:text-neutral-400 bg-neutral-50 dark:bg-neutral-800 p-2 rounded-lg overflow-x-auto">{{ json_encode($latest->payload, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
            @else
                <p class="text-xs text-neutral-500 italic">No telemetry data available.</p>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Commands -->
        <div class="bg-white dark:bg-neutral-900 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm overflow-hidden">
            <div class="p-6 border-b border-neutral-100 dark:border-neutral-800">
                <h3 class="text-xs font-black text-neutral-500 uppercase tracking-wider">Recent Commands</h3>
            </div>
            <div class="p-0">
                <ul class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    @forelse($system->commands as $command)
                        <li class="p-4 hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-bold text-neutral-900 dark:text-white">{{ $command->command_type }}</p>
                                    <p class="text-[10px] text-neutral-500 font-mono">{{ $command->created_at->diffForHumans() }}</p>
                                </div>
                                <span class="px-2 py-1 rounded text-[10px] font-bold uppercase 
                                    @if($command->status === 'completed') bg-green-100 text-green-700 
                                    @elseif($command->status === 'failed') bg-red-100 text-red-700 
                                    @elseif($command->status === 'pending') bg-amber-100 text-amber-700
                                    @else bg-blue-100 text-blue-700 @endif">
                                    {{ $command->status }}
                                </span>
                            </div>
                        </li>
                    @empty
                        <li class="p-4 text-center text-xs text-neutral-500 italic">No commands found.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Sync Logs (Communication) -->
        <div class="bg-white dark:bg-neutral-900 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm overflow-hidden">
            <div class="p-6 border-b border-neutral-100 dark:border-neutral-800">
                <h3 class="text-xs font-black text-neutral-500 uppercase tracking-wider">Communication Logs</h3>
            </div>
            <div class="p-0">
                <ul class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    @forelse($logs as $log)
                        <li class="p-4 hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-bold text-neutral-900 dark:text-white">{{ $log->event }}</p>
                                    <p class="text-[10px] text-neutral-500 font-mono">{{ $log->created_at->format('Y-m-d H:i:s') }} ({{ $log->duration_ms }}ms)</p>
                                </div>
                                <span class="text-xs font-bold {{ $log->status === 'success' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ strtoupper($log->status) }}
                                </span>
                            </div>
                        </li>
                    @empty
                        <li class="p-4 text-center text-xs text-neutral-500 italic">No communication logs found.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
