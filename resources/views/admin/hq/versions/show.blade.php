@extends('layouts.admin')
@section('title', 'Version Details')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-neutral-900 dark:text-white">Version {{ $version->version }}</h1>
            <p class="text-xs text-neutral-500">Released {{ $version->published_at ? $version->published_at->diffForHumans() : 'Not published' }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.platform.hq_central.versions.index') }}" class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-neutral-700 dark:text-neutral-300 rounded-xl text-xs font-bold transition-colors">
                Back to Versions
            </a>
            @if($version->status !== 'archived')
            <form action="{{ route('admin.platform.hq_central.versions.archive', $version) }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-xl text-xs font-bold transition-colors">
                    Archive Version
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-1 space-y-6">
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
                <h3 class="text-xs font-black text-neutral-500 uppercase tracking-wider mb-4 border-b border-neutral-100 dark:border-neutral-800 pb-2">Details</h3>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-[10px] font-bold text-neutral-500 uppercase">Channel</dt>
                        <dd class="text-sm font-bold text-neutral-900 dark:text-white mt-1">{{ ucfirst($version->channel) }}</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] font-bold text-neutral-500 uppercase">Status</dt>
                        <dd class="text-sm font-bold text-neutral-900 dark:text-white mt-1">{{ ucfirst($version->status) }}</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] font-bold text-neutral-500 uppercase">Mandatory</dt>
                        <dd class="text-sm font-bold mt-1 {{ $version->is_mandatory ? 'text-red-600' : 'text-neutral-500' }}">{{ $version->is_mandatory ? 'Yes' : 'No' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] font-bold text-neutral-500 uppercase">Min Supported Version</dt>
                        <dd class="text-sm font-bold text-neutral-900 dark:text-white mt-1">{{ $version->minimum_supported_version ?? 'None' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="md:col-span-2 space-y-6">
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
                <h3 class="text-xs font-black text-neutral-500 uppercase tracking-wider mb-4 border-b border-neutral-100 dark:border-neutral-800 pb-2">Release Notes</h3>
                <div class="prose prose-sm dark:prose-invert max-w-none">
                    {!! nl2br(e($version->release_notes)) !!}
                </div>
            </div>

            <div class="bg-white dark:bg-neutral-900 p-6 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
                <h3 class="text-xs font-black text-neutral-500 uppercase tracking-wider mb-4 border-b border-neutral-100 dark:border-neutral-800 pb-2">Deployment Jobs</h3>
                
                @if($version->jobs->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-neutral-50 dark:bg-neutral-950 border-b border-neutral-100 dark:border-neutral-800 text-neutral-500">
                            <tr>
                                <th class="px-4 py-3 font-black text-[10px] uppercase tracking-wider">Target</th>
                                <th class="px-4 py-3 font-black text-[10px] uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 font-black text-[10px] uppercase tracking-wider">Progress</th>
                                <th class="px-4 py-3 font-black text-[10px] uppercase tracking-wider text-right">View</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                            @foreach($version->jobs as $job)
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50">
                                <td class="px-4 py-3">
                                    <span class="font-bold text-neutral-900 dark:text-white uppercase text-xs">{{ $job->target_type }}</span>
                                    <span class="text-xs text-neutral-500 ml-2">
                                        @if($job->target_type === 'single') {{ $job->systemInstance->system_name ?? 'Unknown' }}
                                        @elseif($job->target_type === 'tenant') {{ $job->tenant->name ?? 'Unknown' }}
                                        @endif
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs font-bold">{{ ucfirst($job->status) }}</td>
                                <td class="px-4 py-3 text-xs font-bold">{{ $job->progress }}%</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.platform.hq_central.updates.show', $job) }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-bold">Details</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-sm font-bold text-neutral-500 text-center py-4">No updates deployed for this version yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
