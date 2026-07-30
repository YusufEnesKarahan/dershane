@extends('layouts.admin')
@section('title', 'Tenant Usage Analytics - ' . $tenant->name)

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-black text-neutral-900 dark:text-white tracking-tight">Tenant Usage: {{ $tenant->name }}</h1>
            <p class="text-sm font-bold text-neutral-500 mt-1">Resource consumption and quota enforcement.</p>
        </div>
        <div>
            <a href="{{ route('admin.platform.hq_central.tenants.index') }}" class="text-indigo-600 hover:text-indigo-800 font-bold text-sm">
                &larr; Back to Tenants
            </a>
        </div>
    </div>

    <!-- Active Subscription & Plan Limits -->
    <div class="bg-white dark:bg-neutral-900 p-6 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
        <h3 class="text-lg font-black text-neutral-900 dark:text-white mb-4">Subscription Quotas</h3>
        @if($subscription)
            <div class="mb-4 text-sm font-bold text-neutral-500">
                Active Plan: <span class="text-indigo-600">{{ $subscription->plan->name }}</span>
            </div>
        @else
            <div class="mb-4 text-sm font-bold text-amber-600">No active subscription found. Defaulting to free limits.</div>
        @endif
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($planLimits as $key => $val)
            <div class="bg-neutral-50 dark:bg-neutral-800/50 p-4 rounded-xl">
                <p class="text-sm font-bold text-neutral-500 uppercase">{{ str_replace('_', ' ', $key) }}</p>
                <p class="text-xl font-black text-neutral-900 dark:text-white">{{ $val }}</p>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Active Violations -->
    @if($activeViolations->isNotEmpty())
    <div class="bg-red-50 dark:bg-red-900/20 p-6 rounded-3xl border border-red-200 dark:border-red-800 shadow-premium-sm">
        <h3 class="text-lg font-black text-red-800 dark:text-red-400 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            Active Quota Violations
        </h3>
        <div class="space-y-2">
            @foreach($activeViolations as $violation)
                <div class="flex justify-between items-center bg-white dark:bg-neutral-900 p-3 rounded-lg border border-red-100 dark:border-red-800">
                    <div>
                        <span class="font-bold text-red-600 uppercase text-xs">{{ $violation->severity }}</span>
                        <span class="ml-2 font-bold text-neutral-900 dark:text-white">{{ str_replace('_', ' ', $violation->metric_key) }}</span>
                    </div>
                    <div class="text-sm font-bold text-neutral-500">
                        Usage: <span class="text-red-600">{{ $violation->actual_value }}</span> / Limit: {{ $violation->limit_value }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Daily Usage Trends -->
    <div class="bg-white dark:bg-neutral-900 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm overflow-hidden p-6">
        <h3 class="text-lg font-black text-neutral-900 dark:text-white mb-4">Usage Trends (Last 30 Days)</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-800">
                <thead class="bg-neutral-50 dark:bg-neutral-800/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-black text-neutral-500 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-black text-neutral-500 uppercase tracking-wider">Students</th>
                        <th class="px-4 py-3 text-left text-xs font-black text-neutral-500 uppercase tracking-wider">Teachers</th>
                        <th class="px-4 py-3 text-left text-xs font-black text-neutral-500 uppercase tracking-wider">Storage (GB)</th>
                        <th class="px-4 py-3 text-left text-xs font-black text-neutral-500 uppercase tracking-wider">API Req</th>
                        <th class="px-4 py-3 text-left text-xs font-black text-neutral-500 uppercase tracking-wider">Emails</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800">
                    @forelse($dailySnapshots as $snap)
                        @php $data = $snap->data_json; @endphp
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50">
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-neutral-900 dark:text-white">
                                {{ $snap->period_start->format('M d, Y') }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-neutral-500 font-medium">{{ $data['students'] ?? 0 }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-neutral-500 font-medium">{{ $data['teachers'] ?? 0 }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-neutral-500 font-medium">{{ number_format(($data['storage_bytes'] ?? 0) / 1073741824, 2) }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-neutral-500 font-medium">{{ number_format($data['api_requests'] ?? 0) }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-neutral-500 font-medium">{{ number_format($data['emails_sent'] ?? 0) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-sm font-bold text-neutral-500">No usage data found for this period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
