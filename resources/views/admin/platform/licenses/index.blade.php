@extends('layouts.admin')
@section('title', 'Lisans Yönetimi')

@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-indigo-900 to-slate-900 p-8 rounded-3xl text-white shadow-md flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <h1 class="text-2xl font-black mt-2">Lisans Durumu</h1>
            <p class="text-xs text-slate-300 mt-1">SaaS Lisans yönetim ve kontrol paneli.</p>
        </div>
    </div>

    <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm">
        <h3 class="text-lg font-bold mb-4">Mevcut Lisans Bilgileri</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-4 bg-gray-50 dark:bg-neutral-800 rounded-xl">
                <span class="text-xs text-gray-500 block">Durum</span>
                <span class="font-bold text-lg capitalize">
                    @if($licenseStatus['status'] == 'active' && !$licenseStatus['expired'])
                        <span class="text-green-600">Aktif</span>
                    @elseif($licenseStatus['expired'])
                        <span class="text-red-600">Süresi Dolmuş</span>
                    @else
                        <span class="text-yellow-600">{{ $licenseStatus['status'] ?? 'Yok' }}</span>
                    @endif
                </span>
            </div>

            <div class="p-4 bg-gray-50 dark:bg-neutral-800 rounded-xl">
                <span class="text-xs text-gray-500 block">Domain</span>
                <span class="font-bold text-lg">{{ config('app.url') }}</span>
            </div>

            @if($license)
            <div class="p-4 bg-gray-50 dark:bg-neutral-800 rounded-xl">
                <span class="text-xs text-gray-500 block">Lisans Anahtarı</span>
                <span class="font-mono text-sm break-all">{{ $license->license_key }}</span>
            </div>

            <div class="p-4 bg-gray-50 dark:bg-neutral-800 rounded-xl">
                <span class="text-xs text-gray-500 block">Plan</span>
                <span class="font-bold text-lg capitalize">{{ $license->plan ?? 'Belirtilmemiş' }}</span>
            </div>

            <div class="p-4 bg-gray-50 dark:bg-neutral-800 rounded-xl">
                <span class="text-xs text-gray-500 block">Başlangıç Tarihi</span>
                <span class="font-bold">{{ $license->created_at->format('d M Y, H:i') }}</span>
            </div>

            <div class="p-4 bg-gray-50 dark:bg-neutral-800 rounded-xl">
                <span class="text-xs text-gray-500 block">Bitiş Tarihi</span>
                <span class="font-bold">
                    {{ $license->expires_at ? $license->expires_at->format('d M Y, H:i') : 'Süresiz' }}
                </span>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
