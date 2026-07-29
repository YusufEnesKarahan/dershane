@extends('layouts.admin')
@section('title', 'Command Detail')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-neutral-900 dark:text-white">Command #{{ $command->id }}</h1>
            <p class="text-xs text-neutral-500">Details for {{ $command->command_type }}</p>
        </div>
        <a href="{{ route('admin.hq.commands.index') }}" class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-neutral-700 dark:text-neutral-300 rounded-xl text-xs font-bold transition-colors">
            &larr; Back to Queue
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Overview -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
                <h3 class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-4 border-b border-neutral-100 dark:border-neutral-800 pb-2">Status</h3>
                
                <div class="mb-4">
                    <span class="px-3 py-1.5 text-xs font-black rounded-lg uppercase tracking-wider
                        {{ $command->status === 'completed' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : '' }}
                        {{ $command->status === 'failed' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : '' }}
                        {{ $command->status === 'pending' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' : '' }}
                        {{ !in_array($command->status, ['completed', 'failed', 'pending']) ? 'bg-neutral-100 text-neutral-700 dark:bg-neutral-800 dark:text-neutral-400' : '' }}
                    ">
                        {{ $command->status }}
                    </span>
                </div>

                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-xs text-neutral-500">Priority:</span>
                        <span class="text-xs font-bold">{{ $command->priority }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-xs text-neutral-500">Retries:</span>
                        <span class="text-xs font-bold">{{ $command->retry_count }} / {{ $command->max_retry }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-xs text-neutral-500">Created:</span>
                        <span class="text-xs font-bold">{{ $command->created_at->format('Y-m-d H:i:s') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-xs text-neutral-500">Executed:</span>
                        <span class="text-xs font-bold">{{ $command->executed_at ? $command->executed_at->format('Y-m-d H:i:s') : 'Pending' }}</span>
                    </div>
                    @if($command->executed_at && $command->created_at)
                        <div class="flex justify-between">
                            <span class="text-xs text-neutral-500">Execution Time:</span>
                            <span class="text-xs font-bold">{{ $command->executed_at->diffForHumans($command->created_at, true) }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white dark:bg-neutral-900 p-6 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
                <h3 class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-4 border-b border-neutral-100 dark:border-neutral-800 pb-2">Target</h3>
                @if($command->instance)
                    <p class="text-sm font-bold text-neutral-900 dark:text-white">{{ $command->instance->system_name }}</p>
                    <p class="text-xs text-neutral-500 mt-1">Tenant: {{ $command->instance->tenant->name ?? 'Unknown' }}</p>
                    <p class="text-xs text-neutral-500 mt-1">UUID: {{ substr($command->instance->system_uuid, 0, 12) }}...</p>
                @else
                    <p class="text-sm text-neutral-500">Unknown Instance</p>
                @endif
            </div>
        </div>

        <!-- Details -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
                <h3 class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-4 border-b border-neutral-100 dark:border-neutral-800 pb-2">Command Payload</h3>
                <pre class="bg-neutral-50 dark:bg-neutral-950 p-4 rounded-xl text-xs font-mono text-neutral-700 dark:text-neutral-300 overflow-x-auto">{{ json_encode($command->payload, JSON_PRETTY_PRINT) }}</pre>
            </div>

            @if($command->response)
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
                <h3 class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-4 border-b border-neutral-100 dark:border-neutral-800 pb-2">ERP Response</h3>
                <pre class="bg-neutral-50 dark:bg-neutral-950 p-4 rounded-xl text-xs font-mono text-neutral-700 dark:text-neutral-300 overflow-x-auto">{{ json_encode($command->response, JSON_PRETTY_PRINT) }}</pre>
            </div>
            @endif

            @if($command->error_message)
            <div class="bg-red-50 dark:bg-red-900/10 p-6 rounded-3xl border border-red-100 dark:border-red-900/30">
                <h3 class="text-[10px] font-black text-red-500 uppercase tracking-wider mb-4 border-b border-red-100 dark:border-red-900/30 pb-2">Error Message</h3>
                <p class="text-xs text-red-700 dark:text-red-400 whitespace-pre-wrap">{{ $command->error_message }}</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
