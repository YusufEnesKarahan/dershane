@extends('layouts.admin')

@section('title', 'System Alerts & Notifications')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-black text-neutral-900 dark:text-white tracking-tight">Enterprise Alerts</h1>
            <p class="text-sm font-bold text-neutral-500 mt-1">Monitor, acknowledge, and resolve critical system alerts.</p>
        </div>
    </div>

    <!-- Alert Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm">
            <p class="text-[10px] font-black text-neutral-500 uppercase tracking-wider">Open Alerts</p>
            <p class="text-3xl font-black text-neutral-900 dark:text-white mt-2">{{ $statistics['open_count'] }}</p>
        </div>
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-red-100 dark:border-red-900/30 shadow-sm">
            <p class="text-[10px] font-black text-red-500 uppercase tracking-wider">Critical Open</p>
            <p class="text-3xl font-black text-red-600 mt-2">{{ $statistics['critical_count'] }}</p>
        </div>
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm">
            <p class="text-[10px] font-black text-amber-500 uppercase tracking-wider">Acknowledged</p>
            <p class="text-3xl font-black text-amber-600 mt-2">{{ $statistics['acknowledged_count'] }}</p>
        </div>
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm">
            <p class="text-[10px] font-black text-green-500 uppercase tracking-wider">Resolved Today</p>
            <p class="text-3xl font-black text-green-600 mt-2">{{ $statistics['resolved_today'] }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-neutral-900 p-4 rounded-2xl border border-neutral-100 dark:border-neutral-800 flex flex-wrap gap-4 items-center">
        <form method="GET" action="{{ route('admin.platform.hq_central.alerts.index') }}" class="flex gap-4 items-center w-full">
            <select name="severity" class="text-sm font-bold border-neutral-200 dark:border-neutral-700 rounded-lg dark:bg-neutral-800">
                <option value="">All Severities</option>
                <option value="critical" {{ request('severity') == 'critical' ? 'selected' : '' }}>Critical</option>
                <option value="danger" {{ request('severity') == 'danger' ? 'selected' : '' }}>Danger</option>
                <option value="warning" {{ request('severity') == 'warning' ? 'selected' : '' }}>Warning</option>
                <option value="info" {{ request('severity') == 'info' ? 'selected' : '' }}>Info</option>
            </select>
            <select name="status" class="text-sm font-bold border-neutral-200 dark:border-neutral-700 rounded-lg dark:bg-neutral-800">
                <option value="">All Statuses</option>
                <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                <option value="acknowledged" {{ request('status') == 'acknowledged' ? 'selected' : '' }}>Acknowledged</option>
                <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
            </select>
            <button type="submit" class="bg-neutral-900 dark:bg-white text-white dark:text-neutral-900 px-4 py-2 rounded-lg text-xs font-bold hover:bg-neutral-800 transition-colors">
                Filter
            </button>
            <a href="{{ route('admin.platform.hq_central.alerts.index') }}" class="text-xs font-bold text-neutral-500 hover:text-neutral-700">Clear</a>
        </form>
    </div>

    <!-- List -->
    <div class="bg-white dark:bg-neutral-900 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-neutral-50 dark:bg-neutral-900/50 border-b border-neutral-100 dark:border-neutral-800">
                        <th class="p-4 text-[10px] font-black text-neutral-500 uppercase tracking-wider">Status & Severity</th>
                        <th class="p-4 text-[10px] font-black text-neutral-500 uppercase tracking-wider">Alert</th>
                        <th class="p-4 text-[10px] font-black text-neutral-500 uppercase tracking-wider">Source</th>
                        <th class="p-4 text-[10px] font-black text-neutral-500 uppercase tracking-wider">Time</th>
                        <th class="p-4 text-[10px] font-black text-neutral-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    @forelse($alerts as $alert)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                        <td class="p-4">
                            <div class="flex items-center gap-2">
                                @if($alert->status == 'open')
                                    <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                                @elseif($alert->status == 'acknowledged')
                                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                @else
                                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                @endif
                                
                                <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase
                                    {{ $alert->severity == 'critical' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : '' }}
                                    {{ $alert->severity == 'danger' ? 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400' : '' }}
                                    {{ $alert->severity == 'warning' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' : '' }}
                                    {{ $alert->severity == 'info' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : '' }}
                                ">
                                    {{ $alert->severity }}
                                </span>
                            </div>
                        </td>
                        <td class="p-4">
                            <p class="text-sm font-bold text-neutral-900 dark:text-white">{{ $alert->title }}</p>
                            <p class="text-xs text-neutral-500 truncate max-w-md">{{ $alert->message }}</p>
                        </td>
                        <td class="p-4">
                            @if($alert->tenant)
                                <p class="text-xs font-bold text-neutral-700 dark:text-neutral-300">{{ $alert->tenant->name }}</p>
                            @endif
                            @if($alert->systemInstance)
                                <p class="text-[10px] font-bold text-neutral-500">{{ $alert->systemInstance->system_name }}</p>
                            @endif
                            @if(!$alert->tenant && !$alert->systemInstance)
                                <p class="text-xs font-bold text-neutral-500">System Level</p>
                            @endif
                        </td>
                        <td class="p-4 text-xs font-bold text-neutral-500">
                            {{ $alert->triggered_at->diffForHumans() }}
                        </td>
                        <td class="p-4 text-right">
                            <a href="{{ route('admin.platform.hq_central.alerts.show', $alert) }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors">View Details</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-sm font-bold text-neutral-500">
                            No alerts found matching your criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-neutral-100 dark:border-neutral-800">
            {{ $alerts->links() }}
        </div>
    </div>
</div>
@endsection
