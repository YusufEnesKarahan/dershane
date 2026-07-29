@extends('layouts.admin')
@section('title', 'Deployment Details')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-neutral-900 dark:text-white">Deployment #{{ substr($update->uuid, 0, 8) }}</h1>
            <p class="text-xs text-neutral-500">Deploying Version {{ $update->version->version }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.platform.hq_central.updates.index') }}" class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-neutral-700 dark:text-neutral-300 rounded-xl text-xs font-bold transition-colors">
                Back to Deployments
            </a>
            @if(in_array($update->status, ['pending', 'scheduled', 'sent']))
            <form action="{{ route('admin.platform.hq_central.updates.cancel', $update) }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-xl text-xs font-bold transition-colors">
                    Cancel Deployment
                </button>
            </form>
            @endif
            @if(in_array($update->status, ['failed', 'cancelled']))
            <form action="{{ route('admin.platform.hq_central.updates.retry', $update) }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition-colors shadow-lg shadow-indigo-600/30">
                    Retry Deployment
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-6">
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
                <h3 class="text-xs font-black text-neutral-500 uppercase tracking-wider mb-4 border-b border-neutral-100 dark:border-neutral-800 pb-2">Target Info</h3>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-[10px] font-bold text-neutral-500 uppercase">Target Scope</dt>
                        <dd class="text-sm font-bold text-neutral-900 dark:text-white mt-1 uppercase">{{ $update->target_type }}</dd>
                    </div>
                    @if($update->target_type === 'single')
                    <div>
                        <dt class="text-[10px] font-bold text-neutral-500 uppercase">System Instance</dt>
                        <dd class="text-sm font-bold text-neutral-900 dark:text-white mt-1">{{ $update->systemInstance->system_name ?? 'N/A' }}</dd>
                    </div>
                    @endif
                    @if(in_array($update->target_type, ['single', 'tenant']))
                    <div>
                        <dt class="text-[10px] font-bold text-neutral-500 uppercase">Tenant</dt>
                        <dd class="text-sm font-bold text-neutral-900 dark:text-white mt-1">{{ $update->tenant->name ?? 'N/A' }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
                <h3 class="text-xs font-black text-neutral-500 uppercase tracking-wider mb-4 border-b border-neutral-100 dark:border-neutral-800 pb-2">Execution Status</h3>
                
                <div class="mb-4">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-xs font-bold uppercase text-neutral-500">Progress</span>
                        <span class="text-xs font-black">{{ $update->progress }}%</span>
                    </div>
                    <div class="w-full bg-neutral-200 dark:bg-neutral-700 rounded-full h-2.5">
                        <div class="h-2.5 rounded-full {{ $update->status === 'failed' ? 'bg-red-500' : 'bg-indigo-600' }}" style="width: {{ $update->progress }}%"></div>
                    </div>
                </div>

                <dl class="grid grid-cols-2 gap-4">
                    <div>
                        <dt class="text-[10px] font-bold text-neutral-500 uppercase">Current Status</dt>
                        <dd class="text-sm font-bold text-neutral-900 dark:text-white mt-1">{{ ucfirst($update->status) }}</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] font-bold text-neutral-500 uppercase">Scheduled At</dt>
                        <dd class="text-xs font-bold text-neutral-500 mt-1">{{ $update->scheduled_at ? $update->scheduled_at->format('M d, Y H:i:s') : '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] font-bold text-neutral-500 uppercase">Started At</dt>
                        <dd class="text-xs font-bold text-neutral-500 mt-1">{{ $update->started_at ? $update->started_at->format('M d, Y H:i:s') : '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] font-bold text-neutral-500 uppercase">Completed At</dt>
                        <dd class="text-xs font-bold text-neutral-500 mt-1">{{ $update->completed_at ? $update->completed_at->format('M d, Y H:i:s') : '-' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        @if($update->error_message)
        <div class="md:col-span-2 space-y-6">
            <div class="bg-red-50 dark:bg-red-900/10 p-6 rounded-3xl border border-red-200 dark:border-red-900 shadow-premium-sm">
                <h3 class="text-xs font-black text-red-600 dark:text-red-400 uppercase tracking-wider mb-2">Error Log</h3>
                <pre class="text-xs text-red-700 dark:text-red-300 whitespace-pre-wrap overflow-x-auto">{{ $update->error_message }}</pre>
            </div>
        </div>
        @endif
        
        @if($update->result)
        <div class="md:col-span-2 space-y-6">
            <div class="bg-neutral-50 dark:bg-neutral-950 p-6 rounded-3xl border border-neutral-200 dark:border-neutral-800 shadow-premium-sm">
                <h3 class="text-xs font-black text-neutral-500 uppercase tracking-wider mb-2">Final Result Payload</h3>
                <pre class="text-xs text-neutral-700 dark:text-neutral-300 whitespace-pre-wrap overflow-x-auto">{{ json_encode($update->result, JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
