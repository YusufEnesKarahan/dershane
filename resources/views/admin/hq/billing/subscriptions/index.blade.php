@extends('layouts.admin')
@section('title', 'Subscriptions')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-black text-neutral-900 dark:text-white tracking-tight">Subscriptions</h1>
            <p class="text-sm font-bold text-neutral-500 mt-1">Manage tenant SaaS subscriptions.</p>
        </div>
    </div>

    <div class="bg-white dark:bg-neutral-900 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm overflow-hidden">
        <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-800">
            <thead class="bg-neutral-50 dark:bg-neutral-800/50">
                <tr>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-black text-neutral-500 uppercase tracking-wider">Tenant</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-black text-neutral-500 uppercase tracking-wider">Plan</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-black text-neutral-500 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-black text-neutral-500 uppercase tracking-wider">Dates</th>
                    <th scope="col" class="relative px-6 py-4"><span class="sr-only">Actions</span></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800">
                @forelse($subscriptions as $sub)
                <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-bold text-neutral-900 dark:text-white">{{ $sub->tenant->name ?? 'Unknown' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-bold text-neutral-900 dark:text-white">{{ $sub->plan->name ?? 'N/A' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 inline-flex text-[10px] leading-4 font-bold rounded-full 
                            @if($sub->status === 'active') bg-green-100 text-green-800
                            @elseif($sub->status === 'past_due') bg-amber-100 text-amber-800
                            @elseif($sub->status === 'cancelled') bg-red-100 text-red-800
                            @else bg-neutral-100 text-neutral-800 @endif capitalize">
                            {{ $sub->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-xs text-neutral-500 font-bold">
                        <div>Start: {{ $sub->starts_at?->format('M d, Y') }}</div>
                        <div>End: {{ $sub->ends_at?->format('M d, Y') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <!-- Action placeholders -->
                        <a href="#" class="text-indigo-600 hover:text-indigo-900 mr-2">Manage</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-sm font-bold text-neutral-500">
                        No subscriptions found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($subscriptions->hasPages())
        <div class="px-6 py-4 border-t border-neutral-200 dark:border-neutral-800">
            {{ $subscriptions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
