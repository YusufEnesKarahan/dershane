@extends('admin.layouts.app')

@section('title', 'Backup Job Details')

@section('content')
<div class="py-12">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.platform.hq_central.backups.index') }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    &larr; Back
                </a>
                <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">
                    Job Details
                </h2>
                @if($job->status === 'completed')
                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold uppercase">Completed</span>
                @elseif($job->status === 'failed')
                    <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold uppercase">Failed</span>
                @else
                    <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-bold uppercase">{{ $job->status }}</span>
                @endif
            </div>

            @if($job->status === 'failed')
            <form action="{{ route('admin.platform.hq_central.backups.retry', $job) }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded text-sm transition font-medium">
                    Retry Failed Backup
                </button>
            </form>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-1 space-y-6">
                <!-- Info Card -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                    <h3 class="font-medium text-gray-900 dark:text-white mb-4 border-b pb-2 dark:border-gray-700">Metadata</h3>
                    
                    <div class="space-y-3 text-sm">
                        <div>
                            <span class="block text-gray-500 dark:text-gray-400 text-xs">Instance</span>
                            <span class="font-medium dark:text-gray-200">{{ $job->systemInstance->system_name ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="block text-gray-500 dark:text-gray-400 text-xs">Policy</span>
                            <span class="font-medium dark:text-gray-200">{{ $job->policy->name ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="block text-gray-500 dark:text-gray-400 text-xs">Size</span>
                            <span class="font-medium dark:text-gray-200">{{ $job->size ? number_format($job->size / 1048576, 2) . ' MB' : 'Unknown' }}</span>
                        </div>
                        <div>
                            <span class="block text-gray-500 dark:text-gray-400 text-xs">Started At</span>
                            <span class="font-medium dark:text-gray-200">{{ $job->started_at ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="block text-gray-500 dark:text-gray-400 text-xs">Finished At</span>
                            <span class="font-medium dark:text-gray-200">{{ $job->finished_at ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                @if($job->error_message)
                <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg p-6">
                    <h3 class="font-medium text-red-800 dark:text-red-400 mb-2">Error Message</h3>
                    <p class="text-sm text-red-600 dark:text-red-300 font-mono">{{ $job->error_message }}</p>
                </div>
                @endif
            </div>

            <!-- Logs -->
            <div class="md:col-span-2 bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <h3 class="font-medium text-gray-800 dark:text-gray-200">Execution Logs</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @forelse($job->logs as $log)
                        <div class="flex space-x-3 text-sm">
                            <div class="text-gray-400 w-32 shrink-0 font-mono text-xs mt-0.5">
                                {{ $log->created_at->format('H:i:s.v') }}
                            </div>
                            <div>
                                <span class="uppercase font-bold text-xs px-2 py-0.5 bg-gray-100 dark:bg-gray-700 rounded text-gray-700 dark:text-gray-300">{{ $log->action }}</span>
                                @if($log->payload)
                                <div class="mt-2 bg-gray-900 rounded p-3 text-xs text-green-400 font-mono overflow-x-auto">
                                    {{ json_encode($log->payload, JSON_PRETTY_PRINT) }}
                                </div>
                                @endif
                                @if($log->response)
                                <div class="mt-2 bg-gray-800 rounded p-3 text-xs text-blue-400 font-mono overflow-x-auto">
                                    {{ json_encode($log->response, JSON_PRETTY_PRINT) }}
                                </div>
                                @endif
                            </div>
                        </div>
                        @empty
                        <p class="text-gray-500 text-sm">No logs recorded for this job.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>
@endsection
