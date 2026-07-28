@extends('layouts.admin')
@section('title', 'Sistem Güncellemeleri')

@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-indigo-900 to-slate-900 p-8 rounded-3xl text-white shadow-premium flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <h1 class="text-2xl font-black mt-2">Sistem Güncellemeleri</h1>
            <p class="text-xs text-slate-300 mt-1">SaaS sürüm yönetimini ve sistem güncellemelerini buradan takip edin.</p>
        </div>
        <div class="flex items-center gap-4">
            <div class="text-right">
                <span class="block text-[10px] text-indigo-300 uppercase tracking-wider font-bold">Mevcut Sürüm</span>
                <span class="block text-xl font-mono font-bold">{{ $currentVersion }}</span>
            </div>
        </div>
    </div>

    @if($isUpdateAvailable && $latest)
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <div class="flex items-start justify-between">
                <div class="space-y-2">
                    <div class="flex items-center gap-3">
                        <span class="px-2 py-1 bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 rounded text-xs font-bold uppercase tracking-wider border border-amber-200 dark:border-amber-800/50">
                            Yeni Sürüm Mevcut
                        </span>
                        @if($latest->is_mandatory)
                            <span class="px-2 py-1 bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 rounded text-xs font-bold uppercase tracking-wider border border-red-200 dark:border-red-800/50">
                                Zorunlu Güncelleme
                            </span>
                        @endif
                    </div>
                    
                    <h2 class="text-2xl font-bold text-neutral-900 dark:text-white mt-2">{{ $latest->name }} <span class="text-indigo-500 font-mono">{{ $latest->version }}</span></h2>
                    <p class="text-xs text-neutral-500">Yayın Tarihi: {{ $latest->release_date ? $latest->release_date->format('d M Y, H:i') : 'Bilinmiyor' }}</p>
                    
                    <div class="pt-4 text-sm text-neutral-700 dark:text-neutral-300 leading-relaxed max-w-3xl">
                        {{ $latest->description }}
                    </div>
                </div>
                
                <div class="text-right">
                    <!-- Install button is passive as per requirements -->
                    <button disabled class="px-6 py-3 bg-neutral-200 dark:bg-neutral-800 text-neutral-400 dark:text-neutral-500 font-bold rounded-xl shadow-sm text-sm flex items-center gap-2 cursor-not-allowed border border-neutral-300 dark:border-neutral-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Yükle (Bakımda)
                    </button>
                    <p class="text-[10px] text-neutral-400 mt-2">OTA Servisi yapılandırılıyor...</p>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-neutral-900 p-12 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm text-center flex flex-col items-center justify-center">
            <div class="w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mb-4 border border-green-200 dark:border-green-800/50">
                <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-neutral-900 dark:text-white">Sisteminiz Güncel</h3>
            <p class="text-sm text-neutral-500 mt-2">Dershane ERP'nin en son sürümünü kullanıyorsunuz ({{ $currentVersion }}). Şu anda yeni bir güncelleme bulunmuyor.</p>
        </div>
    @endif
</div>
@endsection
