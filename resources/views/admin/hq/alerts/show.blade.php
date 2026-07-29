@extends('layouts.admin')

@section('title', 'Alert Details: ' . $alert->title)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.platform.hq_central.alerts.index') }}" class="w-8 h-8 flex items-center justify-center rounded-full bg-neutral-100 dark:bg-neutral-800 text-neutral-500 hover:bg-neutral-200 dark:hover:bg-neutral-700 transition-colors">
                &larr;
            </a>
            <div>
                <h1 class="text-3xl font-black text-neutral-900 dark:text-white tracking-tight flex items-center gap-3">
                    {{ $alert->title }}
                    <span class="px-2 py-1 rounded text-xs font-black uppercase
                        {{ $alert->severity == 'critical' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : '' }}
                        {{ $alert->severity == 'danger' ? 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400' : '' }}
                        {{ $alert->severity == 'warning' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' : '' }}
                        {{ $alert->severity == 'info' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : '' }}
                    ">
                        {{ $alert->severity }}
                    </span>
                </h1>
                <p class="text-sm font-bold text-neutral-500 mt-1">Alert ID: {{ $alert->uuid }} &bull; Triggered {{ $alert->triggered_at->format('Y-m-d H:i:s') }}</p>
            </div>
        </div>
        <div class="flex gap-2">
            @if($alert->status === 'open')
                <form action="{{ route('admin.platform.hq_central.alerts.acknowledge', $alert) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 rounded-lg text-sm font-bold hover:bg-amber-200 dark:hover:bg-amber-900/50 transition-colors">
                        Acknowledge
                    </button>
                </form>
            @endif
            @if($alert->status !== 'resolved')
                <form action="{{ route('admin.platform.hq_central.alerts.resolve', $alert) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-bold hover:bg-green-700 transition-colors">
                        Mark Resolved
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <!-- Details -->
            <div class="bg-white dark:bg-neutral-900 rounded-3xl p-6 border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
                <h3 class="text-xs font-black text-neutral-500 uppercase tracking-wider mb-4">Message</h3>
                <div class="p-4 bg-neutral-50 dark:bg-neutral-800/50 rounded-xl font-mono text-sm text-neutral-800 dark:text-neutral-200 whitespace-pre-wrap">
{{ $alert->message }}
                </div>
            </div>

            <!-- Metadata -->
            @if($alert->metadata)
            <div class="bg-white dark:bg-neutral-900 rounded-3xl p-6 border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
                <h3 class="text-xs font-black text-neutral-500 uppercase tracking-wider mb-4">Metadata</h3>
                <pre class="p-4 bg-neutral-50 dark:bg-neutral-800/50 rounded-xl font-mono text-xs text-neutral-800 dark:text-neutral-200 overflow-x-auto">{{ json_encode($alert->metadata, JSON_PRETTY_PRINT) }}</pre>
            </div>
            @endif
            
            <!-- Notification History -->
            <div class="bg-white dark:bg-neutral-900 rounded-3xl p-6 border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
                <h3 class="text-xs font-black text-neutral-500 uppercase tracking-wider mb-4">Notification History</h3>
                @if($alert->notificationLogs->count() > 0)
                <div class="space-y-3">
                    @foreach($alert->notificationLogs as $log)
                        <div class="flex items-center justify-between p-3 bg-neutral-50 dark:bg-neutral-800/50 rounded-xl">
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-bold text-neutral-700 dark:text-neutral-300 uppercase">{{ $log->channel }}</span>
                                <span class="text-xs text-neutral-500">{{ $log->recipient }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="px-2 py-1 rounded text-[10px] font-black uppercase
                                    {{ $log->status == 'sent' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}
                                ">
                                    {{ $log->status }}
                                </span>
                                <span class="text-[10px] font-bold text-neutral-400">{{ $log->sent_at ? $log->sent_at->format('H:i:s') : $log->created_at->format('H:i:s') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-neutral-500">No notifications sent.</p>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <!-- Properties -->
            <div class="bg-white dark:bg-neutral-900 rounded-3xl p-6 border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
                <h3 class="text-xs font-black text-neutral-500 uppercase tracking-wider mb-4">Properties</h3>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-[10px] font-bold text-neutral-400 uppercase">Status</dt>
                        <dd class="text-sm font-black mt-1 capitalize">{{ $alert->status }}</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] font-bold text-neutral-400 uppercase">Triggered At</dt>
                        <dd class="text-sm font-bold mt-1">{{ $alert->triggered_at->format('Y-m-d H:i:s') }}</dd>
                    </div>
                    @if($alert->resolved_at)
                    <div>
                        <dt class="text-[10px] font-bold text-neutral-400 uppercase">Resolved At</dt>
                        <dd class="text-sm font-bold mt-1 text-green-600">{{ $alert->resolved_at->format('Y-m-d H:i:s') }}</dd>
                    </div>
                    @endif
                    @if($alert->rule)
                    <div>
                        <dt class="text-[10px] font-bold text-neutral-400 uppercase">Matched Rule</dt>
                        <dd class="text-sm font-bold mt-1 text-indigo-600">{{ $alert->rule->name }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Source Information -->
            <div class="bg-white dark:bg-neutral-900 rounded-3xl p-6 border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
                <h3 class="text-xs font-black text-neutral-500 uppercase tracking-wider mb-4">Source</h3>
                <dl class="space-y-4">
                    @if($alert->tenant)
                    <div>
                        <dt class="text-[10px] font-bold text-neutral-400 uppercase">Tenant</dt>
                        <dd class="text-sm font-black mt-1">{{ $alert->tenant->name }}</dd>
                    </div>
                    @endif
                    @if($alert->systemInstance)
                    <div>
                        <dt class="text-[10px] font-bold text-neutral-400 uppercase">System Instance</dt>
                        <dd class="text-sm font-bold mt-1">{{ $alert->systemInstance->system_name }}</dd>
                        <dd class="text-xs text-neutral-500 mt-0.5">{{ $alert->systemInstance->system_uuid }}</dd>
                    </div>
                    @endif
                    @if(!$alert->tenant && !$alert->systemInstance)
                        <p class="text-sm font-bold text-neutral-500">System Level Event</p>
                    @endif
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
