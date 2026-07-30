@extends('layouts.admin')
@section('title', 'Workflow Run History')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-black text-neutral-900 dark:text-white tracking-tight">Run History</h1>
            <p class="text-sm font-bold text-neutral-500 mt-1">Audit log of all workflow executions across tenants.</p>
        </div>
        <div>
            <a href="{{ route('admin.platform.hq_central.workflows.index') }}" class="text-indigo-600 hover:text-indigo-800 font-bold text-sm">
                &larr; Back to Workflows
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-neutral-900 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-800">
                <thead class="bg-neutral-50 dark:bg-neutral-800/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-black text-neutral-500 uppercase tracking-wider">Run ID</th>
                        <th class="px-6 py-4 text-left text-xs font-black text-neutral-500 uppercase tracking-wider">Workflow</th>
                        <th class="px-6 py-4 text-left text-xs font-black text-neutral-500 uppercase tracking-wider">Tenant Context</th>
                        <th class="px-6 py-4 text-left text-xs font-black text-neutral-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-black text-neutral-500 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800">
                    @forelse($runs as $run)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-neutral-900 dark:text-white">
                                #{{ $run->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('admin.platform.hq_central.workflows.show', $run->workflow) }}" class="font-black text-indigo-600 hover:text-indigo-800">{{ $run->workflow->name }}</a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($run->tenant)
                                    <span class="text-sm font-bold text-neutral-700 dark:text-neutral-300">{{ $run->tenant->name }}</span>
                                @else
                                    <span class="text-sm font-bold text-neutral-400">Global</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($run->status === 'completed')
                                    <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 font-bold text-xs rounded-lg border border-emerald-200">Completed</span>
                                @elseif($run->status === 'failed')
                                    <span class="px-2.5 py-1 bg-red-50 text-red-700 font-bold text-xs rounded-lg border border-red-200">Failed (Retry: {{ $run->retry_count }})</span>
                                @elseif($run->status === 'timeout')
                                    <span class="px-2.5 py-1 bg-amber-50 text-amber-700 font-bold text-xs rounded-lg border border-amber-200">Timeout</span>
                                @else
                                    <span class="px-2.5 py-1 bg-blue-50 text-blue-700 font-bold text-xs rounded-lg border border-blue-200">{{ ucfirst($run->status) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-neutral-500">
                                {{ $run->created_at->diffForHumans() }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm font-bold text-neutral-500">
                                No history found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-neutral-100 dark:border-neutral-800">
            {{ $runs->links() }}
        </div>
    </div>
</div>
@endsection
