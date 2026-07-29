@extends('layouts.admin')
@section('title', 'Connected Systems')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-neutral-900 dark:text-white">Connected Systems</h1>
            <p class="text-xs text-neutral-500">Monitor all connected SaaS ERP instances</p>
        </div>
        <div>
            <a href="{{ route('admin.platform.hq_central.index') }}" class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-neutral-700 dark:text-neutral-300 rounded-xl text-xs font-bold transition-colors">
                &larr; Back to Dashboard
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-neutral-600 dark:text-neutral-400">
                <thead class="bg-neutral-50 dark:bg-neutral-800/50 text-neutral-500 font-bold uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="px-6 py-4">System ID</th>
                        <th class="px-6 py-4">Name</th>
                        <th class="px-6 py-4">Tenant</th>
                        <th class="px-6 py-4">Environment</th>
                        <th class="px-6 py-4">Version</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Last Seen</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    @forelse($systems as $instance)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                            <td class="px-6 py-4 font-mono text-xs text-neutral-800 dark:text-neutral-300">
                                {{ Str::limit($instance->system_uuid, 8) }}
                            </td>
                            <td class="px-6 py-4 font-bold text-neutral-900 dark:text-white">
                                {{ $instance->system_name }}
                            </td>
                            <td class="px-6 py-4 text-xs font-bold text-indigo-600">
                                {{ $instance->tenant->name ?? 'Unknown' }}
                            </td>
                            <td class="px-6 py-4 text-xs uppercase font-bold text-neutral-500">
                                {{ $instance->environment }}
                            </td>
                            <td class="px-6 py-4 text-xs font-bold text-neutral-700 dark:text-neutral-300">
                                v{{ $instance->system_version }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded text-[10px] font-bold uppercase 
                                    @if($instance->status === 'online') bg-green-100 text-green-700 
                                    @elseif($instance->status === 'offline') bg-red-100 text-red-700 
                                    @else bg-neutral-100 text-neutral-700 @endif">
                                    {{ $instance->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-xs font-mono">
                                {{ $instance->last_seen_at ? $instance->last_seen_at->diffForHumans() : 'Never' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.platform.hq_central.systems.show', $instance->id) }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-neutral-500 font-bold">
                                Hiçbir sistem bağlantısı bulunamadı.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-neutral-100 dark:border-neutral-800">
            {{ $systems->links() }}
        </div>
    </div>
</div>
@endsection
