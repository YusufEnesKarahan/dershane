@extends('layouts.admin')
@section('title', 'Lisans Yönetimi')

@section('content')
<div class="space-y-6">
    <div class="bg-white dark:bg-slate-900 p-8 rounded-3xl text-slate-900 dark:text-slate-100 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <h1 class="text-2xl font-black mt-2">Lisans Durumu</h1>
            <p class="text-xs text-slate-300 mt-1">SaaS Lisans yönetim ve kontrol paneli.</p>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm">
        <h3 class="text-lg font-bold mb-4">Mevcut Lisans Bilgileri</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-xl">
                <span class="text-xs text-slate-500 block">Durum</span>
                <span class="font-bold text-lg capitalize">
                    @if($license && $license->status == 'trial' && !$licenseStatus['expired'])
                        <span class="text-blue-600">Deneme Sürümü</span>
                    @elseif($licenseStatus['status'] == 'active' && !$licenseStatus['expired'])
                        <span class="text-green-600">Aktif</span>
                    @elseif($licenseStatus['expired'])
                        <span class="text-red-600">Süresi Dolmuş</span>
                    @else
                        <span class="text-yellow-600">{{ $licenseStatus['status'] ?? 'Yok' }}</span>
                    @endif
                </span>
            </div>

            <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-xl">
                <span class="text-xs text-slate-500 block">Domain</span>
                <span class="font-bold text-lg">{{ config('app.url') }}</span>
            </div>

            @if($license)
            <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-xl">
                <span class="text-xs text-slate-500 block">Lisans Anahtarı</span>
                <span class="font-mono text-sm break-all">{{ $license->license_key }}</span>
            </div>

            <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-xl">
                <span class="text-xs text-slate-500 block">Plan</span>
                <span class="font-bold text-lg capitalize">{{ $license->planModel ? $license->planModel->name : ($license->plan ?? 'Belirtilmemiş') }}</span>
            </div>

            <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-xl">
                <span class="text-xs text-slate-500 block">Başlangıç Tarihi</span>
                <span class="font-bold">{{ $license->starts_at ? $license->starts_at->format('d M Y, H:i') : $license->created_at->format('d M Y, H:i') }}</span>
            </div>

            <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-xl">
                <span class="text-xs text-slate-500 block">Bitiş Tarihi</span>
                <span class="font-bold">
                    @if($license->status == 'trial' && $license->trial_ends_at)
                        {{ $license->trial_ends_at->format('d M Y, H:i') }}
                    @else
                        {{ $license->expires_at ? $license->expires_at->format('d M Y, H:i') : 'Süresiz' }}
                    @endif
                </span>
            </div>

            @if(($license->status == 'trial' && $license->trial_ends_at) || $license->expires_at)
            <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-xl">
                <span class="text-xs text-slate-500 block">Kalan Gün</span>
                <span class="font-bold text-lg">
                    @php
                        $endDate = $license->status == 'trial' && $license->trial_ends_at ? $license->trial_ends_at : $license->expires_at;
                        $days = $endDate ? now()->diffInDays($endDate, false) : null;
                    @endphp
                    @if($days !== null)
                        @if($days > 0)
                            <span class="text-green-600">{{ $days }} Gün</span>
                        @elseif($days == 0)
                            <span class="text-yellow-600">Son Gün</span>
                        @else
                            <span class="text-red-600">Süresi Doldu</span>
                        @endif
                    @else
                        Süresiz
                    @endif
                </span>
            </div>
            @endif

            <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-xl md:col-span-2">
                <span class="text-xs text-slate-500 block mb-2">Plan Limitleri</span>
                @php
                    $limitService = app(\App\Domain\Platform\Services\LicenseLimitService::class);
                    $studentLimit = $limitService->getLimit('students', $limitService->getLimit('max_students'));
                    $branchLimit = $limitService->getLimit('branches', $limitService->getLimit('max_branches'));
                    $userLimit = $limitService->getLimit('users');
                @endphp
                <div class="flex gap-4">
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 p-3 rounded-lg flex-1">
                        <span class="text-xs text-slate-500 block">Öğrenci</span>
                        <span class="font-bold">{{ $studentLimit === PHP_INT_MAX ? 'Sınırsız' : $studentLimit }}</span>
                    </div>
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 p-3 rounded-lg flex-1">
                        <span class="text-xs text-slate-500 block">Şube</span>
                        <span class="font-bold">{{ $branchLimit === PHP_INT_MAX ? 'Sınırsız' : $branchLimit }}</span>
                    </div>
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 p-3 rounded-lg flex-1">
                        <span class="text-xs text-slate-500 block">Kullanıcı</span>
                        <span class="font-bold">{{ $userLimit === PHP_INT_MAX ? 'Sınırsız' : $userLimit }}</span>
                    </div>
                </div>
            </div>
            @endif
        </div>
    @if($license)
    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm mt-6">
        <h3 class="text-lg font-bold mb-4">Abonelik Yönetimi</h3>
        
        <div class="flex flex-wrap gap-4 mb-6">
            @if($license->status == 'trial' || ($license->subscription && $license->subscription->payments()->where('status', 'pending')->doesntExist()))
                <form action="{{ route('admin.licenses.activate') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg">
                        Denemeyi Bitir ve Aktifleştir
                    </button>
                </form>
            @endif

            <form action="{{ route('admin.licenses.change-plan') }}" method="POST" class="flex gap-2 items-center">
                @csrf
                <select name="plan_id" class="border border-slate-300 rounded-lg p-2 dark:bg-slate-800 dark:border-slate-700" required>
                    <option value="">Plan Seçin</option>
                    @foreach($plans ?? [] as $planOption)
                        <option value="{{ $planOption->id }}" {{ $license->plan_id == $planOption->id ? 'selected' : '' }}>
                            {{ $planOption->name }} ({{ number_format($planOption->price, 2) }} TL)
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg">
                    Planı Değiştir
                </button>
            </form>
        </div>

        @if($license->subscription && $license->subscription->logs->count() > 0)
        <h4 class="font-bold text-md mb-3 mt-6">İşlem Geçmişi</h4>
        <div class="overflow-x-auto mb-6">
            <table class="w-full text-sm text-left text-slate-500 dark:text-slate-400">
                <thead class="text-xs text-slate-700 uppercase bg-slate-50 dark:bg-slate-800 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3">Tarih</th>
                        <th class="px-4 py-3">İşlem</th>
                        <th class="px-4 py-3">Eski Plan</th>
                        <th class="px-4 py-3">Yeni Plan</th>
                        <th class="px-4 py-3">Not</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($license->subscription->logs()->latest()->get() as $log)
                        <tr class="border-b dark:border-slate-700">
                            <td class="px-4 py-3">{{ $log->created_at->format('d M Y, H:i') }}</td>
                            <td class="px-4 py-3 capitalize">{{ str_replace('_', ' ', $log->action) }}</td>
                            <td class="px-4 py-3">{{ $log->oldPlan ? $log->oldPlan->name : '-' }}</td>
                            <td class="px-4 py-3">{{ $log->newPlan ? $log->newPlan->name : '-' }}</td>
                            <td class="px-4 py-3">{{ $log->notes }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        @if($license->subscription && $license->subscription->payments->count() > 0)
        <h4 class="font-bold text-md mb-3 mt-6">Ödeme ve Fatura Geçmişi</h4>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-500 dark:text-slate-400">
                <thead class="text-xs text-slate-700 uppercase bg-slate-50 dark:bg-slate-800 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3">Tarih</th>
                        <th class="px-4 py-3">Tutar</th>
                        <th class="px-4 py-3">Ağ Geçidi (Gateway)</th>
                        <th class="px-4 py-3">Durum</th>
                        <th class="px-4 py-3">Fatura No</th>
                        <th class="px-4 py-3">İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($license->subscription->payments()->latest()->get() as $payment)
                        <tr class="border-b dark:border-slate-700">
                            <td class="px-4 py-3">{{ $payment->created_at->format('d M Y, H:i') }}</td>
                            <td class="px-4 py-3 font-bold">{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</td>
                            <td class="px-4 py-3 text-xs">{{ $payment->gateway ?? '-' }}</td>
                            <td class="px-4 py-3">
                                @if($payment->status === \App\Domain\Billing\Enums\PaymentStatus::PAID)
                                    <x-admin.badge variant="success">Ödendi</x-admin.badge>
                                @elseif($payment->status === \App\Domain\Billing\Enums\PaymentStatus::PENDING)
                                    <x-admin.badge variant="warning">Bekliyor</x-admin.badge>
                                @elseif($payment->status === \App\Domain\Billing\Enums\PaymentStatus::REFUNDED)
                                    <x-admin.badge variant="info">İade Edildi</x-admin.badge>
                                @else
                                    <x-admin.badge variant="danger" class="capitalize">{{ $payment->status->value }}</x-admin.badge>
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $payment->invoice ? $payment->invoice->invoice_number : '-' }}</td>
                            <td class="px-4 py-3">
                                @if($payment->status === \App\Domain\Billing\Enums\PaymentStatus::PENDING)
                                <form action="{{ route('admin.licenses.pay') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="payment_id" value="{{ $payment->id }}">
                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-xs font-semibold py-1 px-3 rounded">
                                        Ödemeyi Tamamla
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
    @endif
</div>
@endsection
