@extends('layouts.admin')
@section('title', 'HQ Workflows')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-black text-neutral-900 dark:text-white tracking-tight">Workflows</h1>
            <p class="text-sm font-bold text-neutral-500 mt-1">Manage automated incident response and operations.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.platform.hq_central.workflows.history') }}" class="px-4 py-2 bg-white dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300 font-bold text-sm rounded-xl border border-neutral-200 dark:border-neutral-700 hover:bg-neutral-50 dark:hover:bg-neutral-700 transition-colors">
                Run History
            </a>
            <a href="{{ route('admin.platform.hq_central.workflows.create') }}" class="px-4 py-2 bg-primary text-white font-bold text-sm rounded-xl shadow-premium-sm hover:bg-primary-600 transition-colors">
                + New Workflow
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-neutral-900 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-800">
                <thead class="bg-neutral-50 dark:bg-neutral-800/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-black text-neutral-500 uppercase tracking-wider">Workflow</th>
                        <th class="px-6 py-4 text-left text-xs font-black text-neutral-500 uppercase tracking-wider">Trigger</th>
                        <th class="px-6 py-4 text-left text-xs font-black text-neutral-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-black text-neutral-500 uppercase tracking-wider">Total Runs</th>
                        <th class="px-6 py-4 text-right text-xs font-black text-neutral-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800">
                    @forelse($workflows as $workflow)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-black text-neutral-900 dark:text-white">{{ $workflow->name }}</div>
                                <div class="text-sm font-bold text-neutral-500">{{ str($workflow->description)->limit(40) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 font-bold text-xs rounded-lg border border-indigo-200 dark:border-indigo-800">
                                    {{ class_basename($workflow->trigger_event) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($workflow->is_active)
                                    <span class="px-2.5 py-1 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 font-bold text-xs rounded-lg border border-emerald-200 dark:border-emerald-800">Active</span>
                                @else
                                    <span class="px-2.5 py-1 bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400 font-bold text-xs rounded-lg border border-neutral-200 dark:border-neutral-700">Disabled</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-neutral-500">
                                {{ $workflow->runs_count }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <a href="{{ route('admin.platform.hq_central.workflows.show', $workflow) }}" class="text-indigo-600 hover:text-indigo-900 font-bold text-sm mr-3">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm font-bold text-neutral-500">
                                No workflows defined yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
