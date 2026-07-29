@extends('layouts.admin')
@section('title', 'Version Management')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-neutral-900 dark:text-white">HQ Versions</h1>
            <p class="text-xs text-neutral-500">Manage software releases and deployment updates.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.platform.hq_central.versions.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition-colors">
                Publish New Version
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-neutral-900 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-neutral-50 dark:bg-neutral-950 border-b border-neutral-100 dark:border-neutral-800 text-neutral-500">
                    <tr>
                        <th class="px-6 py-4 font-black text-xs uppercase tracking-wider">Version</th>
                        <th class="px-6 py-4 font-black text-xs uppercase tracking-wider">Channel</th>
                        <th class="px-6 py-4 font-black text-xs uppercase tracking-wider">Type</th>
                        <th class="px-6 py-4 font-black text-xs uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 font-black text-xs uppercase tracking-wider">Published</th>
                        <th class="px-6 py-4 font-black text-xs uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    @forelse($versions as $v)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors group">
                        <td class="px-6 py-4">
                            <p class="font-bold text-neutral-900 dark:text-white">{{ $v->version }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider 
                                @if($v->channel === 'stable') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                                @elseif($v->channel === 'beta') bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400
                                @else bg-neutral-100 text-neutral-700 dark:bg-neutral-800 dark:text-neutral-400 @endif">
                                {{ $v->channel }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($v->is_mandatory)
                                <span class="text-xs font-bold text-red-600">Mandatory</span>
                            @else
                                <span class="text-xs font-bold text-neutral-500">Optional</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider 
                                @if($v->status === 'published') bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400
                                @elseif($v->status === 'archived') bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400
                                @else bg-neutral-100 text-neutral-700 dark:bg-neutral-800 dark:text-neutral-400 @endif">
                                {{ $v->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-xs font-bold text-neutral-500">
                            {{ $v->published_at ? $v->published_at->format('M d, Y H:i') : '-' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('admin.platform.hq_central.versions.show', $v) }}" class="p-2 bg-neutral-100 dark:bg-neutral-800 hover:bg-neutral-200 dark:hover:bg-neutral-700 rounded-lg text-neutral-600 dark:text-neutral-400 transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-neutral-100 dark:bg-neutral-800 mb-4">
                                <svg class="w-6 h-6 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                            </div>
                            <p class="text-sm font-bold text-neutral-900 dark:text-white">No versions found</p>
                            <p class="text-xs text-neutral-500 mt-1">Publish a new version to get started.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($versions->hasPages())
        <div class="p-4 border-t border-neutral-100 dark:border-neutral-800">
            {{ $versions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
