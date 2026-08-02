@extends('layouts.admin')
@section('title', 'Tenant Detay: ' . $tenant->name)

@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-slate-900 to-indigo-900 p-8 rounded-3xl text-white shadow-md flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <h1 class="text-2xl font-black mt-2">{{ $tenant->name }}</h1>
            <p class="text-xs text-slate-300 mt-1">Tenant operasyon detayları ve kullanım istatistikleri.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.saas.system-health.index') }}" class="bg-white/20 hover:bg-white/30 text-white font-semibold py-2 px-4 rounded-lg text-sm">
                Sistem Sağlığı
            </a>
            @if($license && $license->status != 'suspended')
                <form action="{{ route('admin.saas.tenants.suspend', $tenant->id) }}" method="POST" onsubmit="return confirm('Sistem lisansını askıya almak istediğinize emin misiniz?');">
                    @csrf
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg text-sm">
                        Lisansı Askıya Al
                    </button>
                </form>
            @elseif($license && $license->status == 'suspended')
                <form action="{{ route('admin.saas.tenants.activate', $tenant->id) }}" method="POST" onsubmit="return confirm('Lisansı tekrar aktifleştirmek istediğinize emin misiniz?');">
                    @csrf
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg text-sm">
                        Lisansı Aktifleştir
                    </button>
                </form>
            @endif
            <a href="{{ route('admin.saas.tenants.index') }}" class="bg-white/20 hover:bg-white/30 text-white font-semibold py-2 px-4 rounded-lg text-sm">
                Geri Dön
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sol Kolon -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Genel Bilgiler -->
            <x-card>
                <h3 class="text-lg font-bold mb-4 border-b pb-2 dark:border-neutral-700">Genel Bilgiler</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-xs text-gray-500 block">Dershane Adı</span>
                        <span class="font-bold">{{ $tenant->name }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 block">Slug / Domain</span>
                        <span class="font-bold">{{ $tenant->slug }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 block">Telefon</span>
                        <span class="font-bold">{{ $tenant->phone ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 block">E-posta</span>
                        <span class="font-bold">{{ $tenant->email ?? '-' }}</span>
                    </div>
                    <div class="col-span-2">
                        <span class="text-xs text-gray-500 block">Adres</span>
                        <span class="font-bold">{{ $tenant->address ?? '-' }}</span>
                    </div>
                </div>
            </x-card>

            <!-- Kullanım İstatistikleri -->
            <x-card>
                <h3 class="text-lg font-bold mb-4 border-b pb-2 dark:border-neutral-700">Kullanım İstatistikleri</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div class="bg-slate-50 dark:bg-neutral-800 p-4 rounded-2xl border border-neutral-100 dark:border-neutral-700">
                        <span class="text-xs text-gray-500 block mb-1">Son Aktivite</span>
                        <span class="text-sm font-bold text-slate-900 dark:text-white">{{ $stats['last_active_at'] ? $stats['last_active_at']->diffForHumans() : '-' }}</span>
                    </div>
                    <div class="bg-slate-50 dark:bg-neutral-800 p-4 rounded-2xl border border-neutral-100 dark:border-neutral-700">
                        <span class="text-xs text-gray-500 block mb-1">Son Giriş</span>
                        <span class="text-sm font-bold text-slate-900 dark:text-white">{{ $stats['last_login_user']['name'] ?? '-' }}</span>
                    </div>
                    <div class="bg-slate-50 dark:bg-neutral-800 p-4 rounded-2xl border border-neutral-100 dark:border-neutral-700">
                        <span class="text-xs text-gray-500 block mb-1">Veri Tahmini</span>
                        <span class="text-sm font-bold text-slate-900 dark:text-white">{{ $stats['estimated_data_size_human'] }}</span>
                    </div>
                </div>
            </x-card>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-neutral-900 p-4 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm text-center">
                    <span class="text-xs text-gray-500 block mb-1">Kullanıcılar</span>
                    <span class="text-2xl font-black text-indigo-600">{{ $stats['users_count'] }}</span>
                </div>
                <div class="bg-white dark:bg-neutral-900 p-4 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm text-center">
                    <span class="text-xs text-gray-500 block mb-1">Öğrenciler</span>
                    <span class="text-2xl font-black text-green-600">{{ $stats['students_count'] }}</span>
                </div>
                <div class="bg-white dark:bg-neutral-900 p-4 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm text-center">
                    <span class="text-xs text-gray-500 block mb-1">Öğretmenler</span>
                    <span class="text-2xl font-black text-blue-600">{{ $stats['teachers_count'] }}</span>
                </div>
                <div class="bg-white dark:bg-neutral-900 p-4 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm text-center">
                    <span class="text-xs text-gray-500 block mb-1">Sınıflar</span>
                    <span class="text-2xl font-black text-purple-600">{{ $stats['classrooms_count'] }}</span>
                </div>
            </div>

            <x-card>
                <h3 class="text-lg font-bold mb-4 border-b pb-2 dark:border-neutral-700">Sistem Sağlık Durumu</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-neutral-800 border border-neutral-100 dark:border-neutral-700">
                        <span class="text-xs text-gray-500 block mb-1">Genel Durum</span>
                        <x-admin.badge variant="{{ $systemHealth['overall_status'] === 'healthy' ? 'success' : ($systemHealth['overall_status'] === 'warning' ? 'warning' : 'danger') }}">
                            {{ ucfirst($systemHealth['overall_status']) }}
                        </x-admin.badge>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-neutral-800 border border-neutral-100 dark:border-neutral-700">
                        <span class="text-xs text-gray-500 block mb-1">Database</span>
                        <span class="font-bold">{{ ucfirst($systemHealth['database_status']) }}</span>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-neutral-800 border border-neutral-100 dark:border-neutral-700">
                        <span class="text-xs text-gray-500 block mb-1">Storage</span>
                        <span class="font-bold">{{ ucfirst($systemHealth['storage_status']) }}</span>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-neutral-800 border border-neutral-100 dark:border-neutral-700">
                        <span class="text-xs text-gray-500 block mb-1">Queue</span>
                        <span class="font-bold">{{ ucfirst($systemHealth['queue_status']) }}</span>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-neutral-800 border border-neutral-100 dark:border-neutral-700">
                        <span class="text-xs text-gray-500 block mb-1">Son Başarılı Cron</span>
                        <span class="font-bold">{{ $systemHealth['last_successful_cron_at'] ? $systemHealth['last_successful_cron_at']->diffForHumans() : '-' }}</span>
                    </div>
                </div>
            </x-card>

            <!-- Fatura Geçmişi -->
            @if($license && $license->subscription && $license->subscription->payments && $license->subscription->payments->count() > 0)
            <x-card>
                <h3 class="text-lg font-bold mb-4 border-b pb-2 dark:border-neutral-700">Fatura Geçmişi</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-neutral-800 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-2">Tarih</th>
                                <th class="px-4 py-2">Tutar</th>
                                <th class="px-4 py-2">Durum</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($license->subscription->payments as $payment)
                            <tr class="border-b dark:border-neutral-700">
                                <td class="px-4 py-2">{{ $payment->created_at->format('d M Y') }}</td>
                                <td class="px-4 py-2 font-bold">{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</td>
                                <td class="px-4 py-2">
                                    <x-admin.badge variant="{{ $payment->status === \App\Domain\Billing\Enums\PaymentStatus::PAID ? 'success' : 'warning' }}">
                                        {{ $payment->status->value ?? $payment->status }}
                                    </x-admin.badge>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>
            @endif
        </div>

        <!-- Sağ Kolon -->
        <div class="space-y-6">
            <!-- Lisans Durumu -->
            <x-card>
                <h3 class="text-lg font-bold mb-4 border-b pb-2 dark:border-neutral-700">Lisans Bilgileri</h3>
                @if($license)
                    <div class="space-y-3">
                        <div>
                            <span class="text-xs text-gray-500 block">Durum</span>
                            @if($license->status == 'active')
                                <x-admin.badge variant="success">Aktif</x-admin.badge>
                            @elseif($license->status == 'trial')
                                <x-admin.badge variant="info">Deneme Sürümü</x-admin.badge>
                            @elseif($license->status == 'suspended')
                                <x-admin.badge variant="danger">Askıda</x-admin.badge>
                            @else
                                <x-admin.badge variant="warning">{{ $license->status }}</x-admin.badge>
                            @endif
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 block">Mevcut Plan</span>
                            <span class="font-bold">{{ $license->planModel ? $license->planModel->name : '-' }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 block">Başlangıç Tarihi</span>
                            <span class="font-bold">{{ $license->starts_at ? $license->starts_at->format('d M Y') : '-' }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 block">Bitiş Tarihi</span>
                            <span class="font-bold">{{ $license->expires_at ? $license->expires_at->format('d M Y') : 'Süresiz' }}</span>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-gray-500">Sistem lisans kaydı bulunamadı.</p>
                @endif
            </x-card>

            <!-- Son Aktiviteler -->
            <x-card>
                <h3 class="text-lg font-bold mb-4 border-b pb-2 dark:border-neutral-700">Son Aktiviteler</h3>
                @if($tenantActivities->count() > 0)
                    <div class="relative border-l border-gray-200 dark:border-gray-700 ml-3">
                        @foreach($tenantActivities as $activity)
                            <div class="mb-4 ml-6">
                                <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -left-3 ring-8 ring-white dark:ring-neutral-900 dark:bg-blue-900">
                                    <svg class="w-2.5 h-2.5 text-blue-800 dark:text-blue-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                                    </svg>
                                </span>
                                <h3 class="mb-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $activity['title'] }}</h3>
                                <p class="mb-2 text-xs font-normal text-gray-500 dark:text-gray-400">{{ $activity['description'] }}</p>
                                <p class="mb-2 text-[11px] font-semibold uppercase tracking-wide text-indigo-500">{{ $activity['actor'] }}</p>
                                <time class="block mb-2 text-xs font-normal leading-none text-gray-400 dark:text-gray-500">{{ $activity['timestamp']->diffForHumans() }} ({{ $activity['timestamp']->format('d M Y, H:i') }})</time>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">Kayıtlı aktivite bulunamadı.</p>
                @endif
            </x-card>

            @if($subscriptionHistory->count() > 0)
            <x-card>
                <h3 class="text-lg font-bold mb-4 border-b pb-2 dark:border-neutral-700">Subscription Geçmişi</h3>
                <div class="space-y-3">
                    @foreach($subscriptionHistory as $log)
                        <div class="rounded-2xl border border-neutral-100 dark:border-neutral-700 bg-slate-50 dark:bg-neutral-800 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <span class="font-bold text-sm text-slate-900 dark:text-white">{{ ucfirst($log->action) }}</span>
                                <span class="text-xs text-gray-500">{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ $log->notes ?? '-' }}</p>
                        </div>
                    @endforeach
                </div>
            </x-card>
            @endif
        </div>
    </div>
</div>
@endsection
