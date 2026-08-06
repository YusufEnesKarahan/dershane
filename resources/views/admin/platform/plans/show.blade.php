@extends('layouts.admin')
@section('title', $plan->exists ? $plan->name . ' Planı' : 'Yeni Plan Oluştur')

@section('content')
<div class="space-y-6">
    @php
        $featureOptions = [
            'sms' => 'SMS Bildirimleri',
            'advanced_reports' => 'Advanced Reports',
            'api_access' => 'API Access',
            'online_payment' => 'Online Payment',
            'attendance' => 'Attendance',
        ];
        $selectedFeatures = old('features', is_array($plan->features) ? $plan->features : []);
    @endphp

    <div class="bg-white dark:bg-slate-900 p-8 rounded-3xl text-slate-900 dark:text-slate-100 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <h1 class="text-2xl font-black mt-2">{{ $plan->exists ? $plan->name : 'Yeni Plan' }}</h1>
            <p class="text-xs text-slate-300 mt-1">Plan özellikleri, tenant dağılımı ve limitler.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.platform.subscriptions.plans') }}" class="bg-white/20 hover:bg-white/30 text-white font-semibold py-2 px-4 rounded-lg text-sm">Plan Listesi</a>
            @if($plan->exists)
                <a href="{{ route('admin.platform.subscriptions.plans.edit', $plan) }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2 px-4 rounded-lg text-sm">Düzenle</a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <x-card class="xl:col-span-2">
            <h3 class="text-lg font-bold mb-4 border-b pb-2 dark:border-slate-700">{{ $plan->exists ? 'Plan Detayı' : 'Plan Oluştur' }}</h3>
            <form method="POST" action="{{ $plan->exists ? route('admin.platform.subscriptions.plans.update', $plan) : route('admin.platform.subscriptions.plans.store') }}" class="space-y-4">
                @csrf
                @if($plan->exists)
                    @method('PUT')
                @endif
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-bold text-slate-500 block mb-1">Plan Adı</label>
                        <input name="name" value="{{ old('name', $plan->name) }}" class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800" />
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 block mb-1">Slug</label>
                        <input name="slug" value="{{ old('slug', $plan->slug) }}" class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800" />
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 block mb-1">Fiyat</label>
                        <input name="price" type="number" step="0.01" value="{{ old('price', $plan->price) }}" class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800" />
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 block mb-1">Billing Cycle</label>
                        <select name="billing_cycle" class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800">
                            <option value="monthly" @selected(old('billing_cycle', $plan->billing_cycle ?? 'monthly') === 'monthly')>Monthly</option>
                            <option value="yearly" @selected(old('billing_cycle', $plan->billing_cycle ?? 'monthly') === 'yearly')>Yearly</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 block mb-1">Trial Days</label>
                        <input name="trial_days" type="number" min="0" value="{{ old('trial_days', $plan->trial_days ?? 0) }}" class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800" />
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 block mb-1">Aktiflik</label>
                        <select name="is_active" class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800">
                            <option value="1" @selected(old('is_active', $plan->is_active ?? true))>Aktif</option>
                            <option value="0" @selected(!old('is_active', $plan->is_active ?? true))>Pasif</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 block mb-1">Maks. Kullanıcı</label>
                        <input name="max_users" type="number" min="0" value="{{ old('max_users', $plan->max_users ?? data_get($plan->limits, 'users')) }}" class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800" />
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 block mb-1">Maks. Öğrenci</label>
                        <input name="max_students" type="number" min="0" value="{{ old('max_students', $plan->max_students ?? data_get($plan->limits, 'students')) }}" class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800" />
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 block mb-1">Maks. Öğretmen</label>
                        <input name="max_teachers" type="number" min="0" value="{{ old('max_teachers', $plan->max_teachers ?? data_get($plan->limits, 'teachers')) }}" class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800" />
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 block mb-1">Grace Period (Days)</label>
                        <input name="grace_days" type="number" min="0" value="{{ old('grace_days', data_get($plan->limits, 'grace_days', 0)) }}" class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800" />
                    </div>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 block mb-1">Açıklama</label>
                    <textarea name="description" rows="4" class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800">{{ old('description', $plan->description) }}</textarea>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 block mb-3">Features</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($featureOptions as $featureKey => $featureLabel)
                            <label class="flex items-center gap-3 rounded-2xl border border-slate-200 dark:border-slate-700 px-4 py-3 bg-white dark:bg-slate-800">
                                <input type="checkbox" name="features[]" value="{{ $featureKey }}" @checked(in_array($featureKey, (array) $selectedFeatures, true)) class="rounded border-slate-300 text-blue-600 focus:ring-blue-500" />
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ $featureLabel }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg text-sm">{{ $plan->exists ? 'Güncelle' : 'Oluştur' }}</button>
                    <a href="{{ route('admin.platform.subscriptions.plans') }}" class="text-sm font-semibold text-slate-500">Vazgeç</a>
                </div>
            </form>
        </x-card>

        <x-card>
            <h3 class="text-lg font-bold mb-4 border-b pb-2 dark:border-slate-700">Kullanım Özeti</h3>
            @if($plan->exists)
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-xs text-slate-500 block">Tenant Sayısı</span>
                        <span class="font-bold">{{ $plan->tenant_count ?? $plan->subscriptions()->whereNotNull('branch_id')->count() }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-500 block">Kullanıcı Limiti</span>
                        <span class="font-bold">{{ $plan->max_users ?? data_get($plan->limits, 'users', '-') }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-500 block">Öğrenci Limiti</span>
                        <span class="font-bold">{{ $plan->max_students ?? data_get($plan->limits, 'students', '-') }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-500 block">Öğretmen Limiti</span>
                        <span class="font-bold">{{ $plan->max_teachers ?? data_get($plan->limits, 'teachers', '-') }}</span>
                    </div>
                    <div class="md:col-span-2">
                        <span class="text-xs text-slate-500 block mb-2">Aktif Özellikler</span>
                        <div class="flex flex-wrap gap-2">
                            @forelse(($plan->features ?? []) as $feature)
                                <span class="inline-flex items-center rounded-full bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-200 px-3 py-1 text-xs font-semibold">
                                    {{ $featureOptions[$feature] ?? $feature }}
                                </span>
                            @empty
                                <span class="text-sm text-slate-500">Aktif özellik tanımlı değil.</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            @else
                <p class="text-sm text-slate-500">Plan oluşturulduktan sonra kullanım ve tenant dağılımı burada gösterilir.</p>
            @endif
        </x-card>
    </div>

    @if($plan->exists)
    <x-card>
        <h3 class="text-lg font-bold mb-4 border-b pb-2 dark:border-slate-700">Bağlı Tenantlar</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-500 dark:text-slate-400">
                <thead class="text-xs text-slate-700 uppercase bg-slate-50 dark:bg-slate-800 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-2">Tenant</th>
                        <th class="px-4 py-2">Durum</th>
                        <th class="px-4 py-2">Başlangıç</th>
                        <th class="px-4 py-2">Bitiş</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($plan->subscriptions as $subscription)
                        @continue(!$subscription->branch)
                        <tr class="border-b dark:border-slate-700">
                            <td class="px-4 py-2 font-semibold text-slate-900 dark:text-white">{{ $subscription->branch->name }}</td>
                            <td class="px-4 py-2">{{ $subscription->status }}</td>
                            <td class="px-4 py-2">{{ $subscription->started_at?->format('d M Y') ?? $subscription->starts_at?->format('d M Y') ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $subscription->expires_at?->format('d M Y') ?? $subscription->ends_at?->format('d M Y') ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-slate-500">Bağlı tenant bulunamadı.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
    @endif
</div>
@endsection