@extends('layouts.admin')
@section('title', 'Billing Plans')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-black text-neutral-900 dark:text-white tracking-tight">Subscription Plans</h1>
            <p class="text-sm font-bold text-neutral-500 mt-1">Manage SaaS subscription plans, features and limits.</p>
        </div>
        <div>
            <!-- Create Button Placeholder -->
            <button class="bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm font-bold shadow-premium-sm hover:bg-indigo-700 transition">
                Create Plan
            </button>
        </div>
    </div>

    <div class="bg-white dark:bg-neutral-900 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm overflow-hidden">
        <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-800">
            <thead class="bg-neutral-50 dark:bg-neutral-800/50">
                <tr>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-black text-neutral-500 uppercase tracking-wider">Plan Name</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-black text-neutral-500 uppercase tracking-wider">Price</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-black text-neutral-500 uppercase tracking-wider">Billing Period</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-black text-neutral-500 uppercase tracking-wider">Subscribers</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-black text-neutral-500 uppercase tracking-wider">Status</th>
                    <th scope="col" class="relative px-6 py-4"><span class="sr-only">Actions</span></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800">
                @forelse($plans as $plan)
                <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-bold text-neutral-900 dark:text-white">{{ $plan->name }}</div>
                        <div class="text-xs text-neutral-500">{{ $plan->slug }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm font-bold text-neutral-900 dark:text-white">{{ $plan->price }} {{ $plan->currency }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 inline-flex text-[10px] leading-4 font-bold rounded-full bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400 capitalize">
                            {{ $plan->billing_period }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 font-bold">
                        {{ $plan->subscriptions_count }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($plan->is_active)
                            <span class="px-2 py-1 inline-flex text-[10px] leading-4 font-bold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Active</span>
                        @else
                            <span class="px-2 py-1 inline-flex text-[10px] leading-4 font-bold rounded-full bg-neutral-100 text-neutral-800 dark:bg-neutral-800 dark:text-neutral-400">Inactive</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="#" class="text-indigo-600 hover:text-indigo-900 dark:hover:text-indigo-400 font-bold">Edit</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-sm font-bold text-neutral-500">
                        No subscription plans found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
