@extends('layouts.admin')
@section('title', 'Envanter ve Demirbaş Paneli')
@section('content')
    <div class="space-y-6">
        
        <!-- Üst Banner -->
        <div class="bg-gradient-to-r from-teal-900 to-slate-900 p-8 rounded-3xl text-white shadow-premium flex flex-col md:flex-row md:items-center md:justify-between gap-6 border border-teal-950">
            <div>
                <span class="px-2.5 py-1 text-[10px] uppercase font-bold tracking-widest bg-teal-500/20 text-teal-300 rounded-full border border-teal-500/30 font-mono">Inventory Suite</span>
                <h1 class="text-2xl font-black mt-2">Envanter & Demirbaş Yönetimi</h1>
                <p class="text-xs text-teal-100 mt-1">Dershane demirbaşlarını, stok sarf malzemelerini, satın alma süreçlerini ve zimmetleri kontrol edin.</p>
            </div>
            
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.assets.index') }}" class="px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-xs font-bold rounded-xl transition shadow-lg shadow-teal-950 flex items-center gap-1.5 border border-teal-500">
                    Demirbaş Listesi
                </a>
                <a href="{{ route('admin.inventory.analytics') }}" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-xs font-bold rounded-xl transition border border-slate-700">
                    Envanter Analitiği
                </a>
            </div>
        </div>

        <!-- KPI Kartları -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-neutral-400 uppercase">Toplam Demirbaş</span>
                    <div class="p-2 bg-teal-50 dark:bg-teal-950 text-teal-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                </div>
                <div>
                    <div class="text-3xl font-black text-neutral-900 dark:text-white font-mono">{{ $analytics['total_assets'] }} / {{ $analytics['active_assets'] }}</div>
                    <p class="text-[11px] text-neutral-500 mt-1">Toplam / Aktif Demirbaş</p>
                </div>
            </div>

            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-neutral-400 uppercase">Bakımda / Zimmette</span>
                    <div class="p-2 bg-amber-50 dark:bg-amber-950 text-amber-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                </div>
                <div>
                    <div class="text-3xl font-black text-neutral-900 dark:text-white font-mono">{{ $analytics['maintenance_assets'] }} / {{ $analytics['assigned_assets'] }}</div>
                    <p class="text-[11px] text-neutral-500 mt-1">Bakımdaki / Çalışan Zimmetleri</p>
                </div>
            </div>

            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-neutral-400 uppercase">Stok Adedi / Kritik</span>
                    <div class="p-2 bg-emerald-50 dark:bg-emerald-950 text-emerald-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                </div>
                <div>
                    <div class="text-3xl font-black text-neutral-900 dark:text-white font-mono">{{ $analytics['total_stock'] }} / <span class="text-red-500">{{ $analytics['critical_stock'] }}</span></div>
                    <p class="text-[11px] text-neutral-500 mt-1">Stok Malzemeleri / Kritik Seviyedekiler</p>
                </div>
            </div>

            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-neutral-400 uppercase">Aylık Alım / Bakım</span>
                    <div class="p-2 bg-blue-50 dark:bg-blue-950 text-blue-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div>
                    <div class="text-3xl font-black text-neutral-900 dark:text-white font-mono">₺{{ number_format($analytics['monthly_purchase_sum'] + $analytics['maintenance_cost'], 2) }}</div>
                    <p class="text-[11px] text-neutral-500 mt-1">Aylık satınalma & bakım toplam giderleri</p>
                </div>
            </div>

        </div>

        <!-- Dağılımlar -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Depo / Şube Bazlı Demirbaş Dağılımı</h3>
                
                <div class="space-y-4">
                    @forelse($analytics['location_distribution'] as $loc)
                        <div class="space-y-1">
                            <div class="flex justify-between items-center text-xs font-bold">
                                <span class="text-neutral-700 dark:text-neutral-300">{{ $loc['name'] }}</span>
                                <span class="text-neutral-500 font-mono">{{ $loc['count'] }} Demirbaş</span>
                            </div>
                            <div class="w-full bg-neutral-100 dark:bg-neutral-800 h-2 rounded-full overflow-hidden">
                                <div class="bg-teal-600 h-full rounded-full" style="width: {{ $analytics['total_assets'] > 0 ? ($loc['count'] / $analytics['total_assets']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-xs text-neutral-400 py-6">Lokasyon verisi bulunmamaktadır.</div>
                    @endforelse
                </div>
            </div>

            <!-- Hızlı Kısayollar -->
            <div class="lg:col-span-2 bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Envanter Operasyonları</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('admin.inventory.categories.index') }}" class="p-4 bg-teal-50 dark:bg-teal-950/20 border border-teal-100 dark:border-teal-900/50 rounded-xl hover:scale-102 transition duration-200">
                        <span class="text-xs font-bold text-teal-800 dark:text-teal-300 block">Kategoriler & Konumlar</span>
                        <p class="text-[10px] text-teal-600 dark:text-teal-400 mt-1">Sınıf, şube ve lokasyon tanımları.</p>
                    </a>

                    <a href="{{ route('admin.inventory.index') }}" class="p-4 bg-blue-50 dark:bg-blue-950/20 border border-blue-100 dark:border-blue-900/50 rounded-xl hover:scale-102 transition duration-200">
                        <span class="text-xs font-bold text-blue-800 dark:text-blue-300 block">Stok & Malzemeler</span>
                        <p class="text-[10px] text-blue-600 dark:text-blue-400 mt-1">Sarf malzemesi ve stok giriş-çıkışları.</p>
                    </a>

                    <a href="{{ route('admin.purchase.index') }}" class="p-4 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/50 rounded-xl hover:scale-102 transition duration-200">
                        <span class="text-xs font-bold text-emerald-800 dark:text-emerald-300 block">Satın Alma Siparişleri</span>
                        <p class="text-[10px] text-emerald-600 dark:text-emerald-400 mt-1">Tedarikçi siparişleri ve onay süreçleri.</p>
                    </a>
                </div>
            </div>

        </div>

    </div>
@endsection
