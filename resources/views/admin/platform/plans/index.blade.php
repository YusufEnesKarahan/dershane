@extends('layouts.admin')
@section('title', 'Planlar')

@section('content')
<div class="space-y-6">
    <div class="bg-white dark:bg-slate-900 p-8 rounded-3xl text-slate-900 dark:text-slate-100 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <h1 class="text-2xl font-black mt-2">Plan Listesi</h1>
            <p class="text-xs text-slate-300 mt-1">Plan bazlı limitler, fiyatlandırma ve tenant dağılımı.</p>
        </div>
        <a href="{{ route('admin.platform.subscriptions.plans.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2 px-4 rounded-lg text-sm">Yeni Plan</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <x-card>
            <span class="text-xs text-slate-500 block mb-1">Plan Sayısı</span>
            <div class="text-2xl font-black text-slate-900 dark:text-white">{{ $plans->count() }}</div>
        </x-card>
        <x-card>
            <span class="text-xs text-slate-500 block mb-1">Aktif Planlar</span>
            <div class="text-2xl font-black text-slate-900 dark:text-white">{{ $plans->where('is_active', true)->count() }}</div>
        </x-card>
        <x-card>
            <span class="text-xs text-slate-500 block mb-1">Toplam Tenant</span>
            <div class="text-2xl font-black text-slate-900 dark:text-white">{{ $plans->sum('tenant_count') }}</div>
        </x-card>
        <x-card>
            <span class="text-xs text-slate-500 block mb-1">Aylık Gelir Tahmini</span>
            <div class="text-2xl font-black text-slate-900 dark:text-white">₺{{ number_format($plans->sum(fn ($plan) => $plan->tenant_count * $plan->price), 2) }}</div>
        </x-card>
    </div>

    <x-card>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-500 dark:text-slate-400">
                <thead class="text-xs text-slate-700 uppercase bg-slate-50 dark:bg-slate-800 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3">Plan</th>
                        <th class="px-4 py-3">Fiyat</th>
                        <th class="px-4 py-3">Kullanıcı Limiti</th>
                        <th class="px-4 py-3">Öğrenci Limiti</th>
                        <th class="px-4 py-3">Aktiflik</th>
                        <th class="px-4 py-3">Tenant Sayısı</th>
                        <th class="px-4 py-3 text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($plans as $plan)
                        <tr class="border-b dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50">
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-900 dark:text-white">{{ $plan->name }}</div>
                                <div class="text-xs text-slate-500">{{ $plan->slug }}</div>
                            </td>
                            <td class="px-4 py-3 font-semibold">₺{{ number_format($plan->price, 2) }}</td>
                            <td class="px-4 py-3">{{ $plan->max_users ?? data_get($plan->limits, 'users', '-') }}</td>
                            <td class="px-4 py-3">{{ $plan->max_students ?? data_get($plan->limits, 'students', '-') }}</td>
                            <td class="px-4 py-3">
                                <x-admin.badge variant="{{ $plan->is_active ? 'success' : 'danger' }}">{{ $plan->is_active ? 'Aktif' : 'Pasif' }}</x-admin.badge>
                            </td>
                            <td class="px-4 py-3">{{ $plan->tenant_count }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.platform.subscriptions.plans.show', $plan) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 font-semibold text-xs">Detay</a>
                                <span class="mx-1 text-slate-300">|</span>
                                <a href="{{ route('admin.platform.subscriptions.plans.edit', $plan) }}" class="text-emerald-600 hover:text-emerald-900 dark:text-emerald-400 font-semibold text-xs">Düzenle</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-slate-500">Plan bulunamadı.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</div>
@endsection