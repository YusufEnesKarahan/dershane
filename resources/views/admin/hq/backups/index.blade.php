@extends('admin.layouts.app')

@section('title', 'Backup & Disaster Recovery')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">Backup Dashboard</h2>
            <div class="space-x-3">
                <a href="{{ route('admin.platform.hq_central.backups.policies') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm transition">
                    Manage Policies
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Policies</div>
                <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total_policies'] }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Successful Jobs</div>
                <div class="mt-2 text-3xl font-bold text-green-600 dark:text-green-400">{{ $stats['successful_jobs'] }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Failed Jobs</div>
                <div class="mt-2 text-3xl font-bold text-red-600 dark:text-red-400">{{ $stats['failed_jobs'] }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Storage</div>
                <div class="mt-2 text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ number_format($stats['total_storage'] / 1048576, 2) }} MB</div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <h3 class="font-medium text-gray-800 dark:text-gray-200">Recent Backup Jobs</h3>
            </div>
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 text-sm">
                        <th class="p-4 font-medium border-b dark:border-gray-700">Instance</th>
                        <th class="p-4 font-medium border-b dark:border-gray-700">Policy</th>
                        <th class="p-4 font-medium border-b dark:border-gray-700">Status</th>
                        <th class="p-4 font-medium border-b dark:border-gray-700">Size</th>
                        <th class="p-4 font-medium border-b dark:border-gray-700">Finished At</th>
                        <th class="p-4 font-medium border-b dark:border-gray-700 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y dark:divide-gray-700">
                    @forelse($recentJobs as $job)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                        <td class="p-4 text-gray-900 dark:text-gray-200">
                            {{ $job->systemInstance->system_name ?? 'N/A' }}
                        </td>
                        <td class="p-4 text-gray-600 dark:text-gray-400">
                            {{ $job->policy->name ?? 'N/A' }}
                            <span class="text-xs uppercase px-1 py-0.5 bg-gray-100 dark:bg-gray-700 rounded">{{ $job->policy->backup_type ?? '' }}</span>
                        </td>
                        <td class="p-4">
                            @if($job->status === 'completed')
                                <span class="px-2 py-1 bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 rounded-full text-xs font-medium">Completed</span>
                            @elseif($job->status === 'failed')
                                <span class="px-2 py-1 bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 rounded-full text-xs font-medium">Failed</span>
                            @elseif($job->status === 'running')
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 rounded-full text-xs font-medium">Running</span>
                            @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 rounded-full text-xs font-medium">Pending</span>
                            @endif
                        </td>
                        <td class="p-4 text-gray-600 dark:text-gray-400">
                            {{ $job->size ? number_format($job->size / 1048576, 2) . ' MB' : '-' }}
                        </td>
                        <td class="p-4 text-gray-600 dark:text-gray-400">
                            {{ $job->finished_at ? $job->finished_at->format('M d, H:i') : '-' }}
                        </td>
                        <td class="p-4 text-right">
                            <a href="{{ route('admin.platform.hq_central.backups.show', $job) }}" class="text-indigo-500 hover:underline text-xs font-medium">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-500">No backup jobs executed yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
    </div>
</div>
@endsection
