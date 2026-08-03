@extends('layouts.admin')
@section('title', 'SaaS Abonelik Yönetimi')

@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-slate-900 to-indigo-900 p-8 rounded-3xl text-white shadow-md flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <h1 class="text-2xl font-black mt-2">SaaS Abonelik & Plan Yönetimi</h1>
            <p class="text-xs text-slate-300 mt-1">Planlar, tenant atamaları, trial durumları ve abonelik yaşam döngüsü burada yönetilir.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.platform.subscriptions.plans') }}" class="bg-white/20 hover:bg-white/30 text-white font-semibold py-2 px-4 rounded-lg text-sm">Planlar</a>
            <a href="{{ route('admin.platform.subscriptions.plans.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2 px-4 rounded-lg text-sm">Yeni Plan</a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <x-card>
            <span class="text-xs text-gray-500 block mb-1">Total Plans</span>
            <div class="text-2xl font-black text-slate-900 dark:text-white">{{ $metrics['total_plans'] }}</div>
        </x-card>
        <x-card>
            <span class="text-xs text-gray-500 block mb-1">Active Subscriptions</span>
            <div class="text-2xl font-black text-slate-900 dark:text-white">{{ $metrics['active_subscriptions'] }}</div>
        </x-card>
        <x-card>
            <span class="text-xs text-gray-500 block mb-1">Trial Tenants</span>
            <div class="text-2xl font-black text-slate-900 dark:text-white">{{ $metrics['trial_tenants'] }}</div>
        </x-card>
        <x-card>
            <span class="text-xs text-gray-500 block mb-1">Monthly Revenue Estimate</span>
            <div class="text-2xl font-black text-slate-900 dark:text-white">₺{{ number_format($metrics['monthly_revenue_estimate'], 2) }}</div>
        </x-card>
    </div>

    <x-card>
        <div class="flex items-center justify-between mb-4 gap-4">
            <div>
                <h3 class="text-lg font-bold">Son Abonelikler</h3>
                <p class="text-xs text-gray-500">Tenant bazlı aboneliklerin son durumları.</p>
            </div>
            <a href="{{ route('admin.platform.subscriptions.plans') }}" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400">Plan listesine git</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-neutral-800 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-3">Tenant</th>
                        <th class="px-4 py-3">Plan</th>
                        <th class="px-4 py-3">Durum</th>
                        <th class="px-4 py-3">Bitiş</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentSubscriptions as $subscription)
                        <tr class="border-b dark:border-neutral-700">
                            <td class="px-4 py-3 font-semibold text-slate-900 dark:text-white">{{ $subscription->branch?->name ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $subscription->plan?->name ?? '-' }}</td>
                            <td class="px-4 py-3 uppercase text-xs font-bold">{{ $subscription->status }}</td>
                            <td class="px-4 py-3">{{ $subscription->expires_at?->format('d M Y') ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-gray-500">Kayıtlı abonelik bulunamadı.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</div>
@endsection