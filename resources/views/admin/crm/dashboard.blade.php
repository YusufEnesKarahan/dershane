@extends('layouts.admin')
@section('title', 'CRM Satış & Lead Paneli')
@section('content')
    <div class="space-y-6">
        
        <!-- Üst Başlık Bölümü -->
        <div class="bg-gradient-to-r from-rose-900 to-neutral-900 p-8 rounded-3xl text-white shadow-premium flex flex-col md:flex-row md:items-center md:justify-between gap-6 border border-rose-950">
            <div>
                <span class="px-2.5 py-1 text-[10px] uppercase font-bold tracking-widest bg-rose-500/20 text-rose-300 rounded-full border border-rose-500/30">Lead Management System</span>
                <h1 class="text-2xl font-black mt-2">CRM Aday Öğrenci Satış Paneli</h1>
                <p class="text-xs text-rose-100 mt-1">Aday öğrenci havuzu, danışman performansları ve satış hunisi (funnel) verilerini tek ekrandan yönetin.</p>
            </div>
            
            <div class="flex flex-col sm:flex-row items-center gap-3">
                <x-admin.button href="{{ route('admin.crm.pipeline') }}" variant="primary" icon="M14 5l7 7m0 0l-7 7m7-7H3">
                    Satış Pipeline (Kanban)
                </x-admin.button>
                <x-admin.button href="{{ route('admin.leads.index') }}" variant="secondary" icon="M4 6h16M4 10h16M4 14h16M4 18h16">
                    Aday Öğrenci Listesi
                </x-admin.button>
            </div>
        </div>

        <!-- CRM KPI Özetleri -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <!-- Bugünkü Aramalar -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-neutral-400 uppercase">Bugünkü Takip</span>
                    <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div>
                    <div class="text-3xl font-black text-neutral-900 dark:text-white font-mono">{{ $todayCalls }} Arama</div>
                    <p class="text-[11px] text-neutral-500 mt-1">Bugün planlanmış takip aramaları</p>
                </div>
            </div>

            <!-- Bekleyen Lead -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-neutral-400 uppercase">Bekleyen Aday</span>
                    <div class="p-2 bg-amber-50 text-amber-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                </div>
                <div>
                    <div class="text-3xl font-black text-neutral-900 dark:text-white font-mono">{{ $pendingLeadsCount }} Aday</div>
                    <p class="text-[11px] text-neutral-500 mt-1">Süreci aktif devam eden bekleyen leadler</p>
                </div>
            </div>

            <!-- Kayıt Olan Öğrenci -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-neutral-400 uppercase">Kayıt Olanlar</span>
                    <div class="p-2 bg-green-50 text-green-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div>
                    <div class="text-3xl font-black text-neutral-900 dark:text-white font-mono">{{ $analytics['registered_leads'] }} Kayıt</div>
                    <p class="text-[11px] text-neutral-500 mt-1">Süreç sonunda kaydı tamamlanan öğrenciler</p>
                </div>
            </div>

            <!-- Dönüşüm Oranı -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-neutral-400 uppercase">Dönüşüm Oranı</span>
                    <div class="p-2 bg-rose-50 text-rose-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                </div>
                <div>
                    <div class="text-3xl font-black text-neutral-900 dark:text-white font-mono">%{{ $analytics['conversion_rate'] }}</div>
                    <p class="text-[11px] text-neutral-500 mt-1">Leadlerin kayda dönüşme yüzdesi</p>
                </div>
            </div>

        </div>

        <!-- Danışman Performansı Listesi -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Eğitim Danışmanları & Satış Performansı</h3>
            
            <x-admin.table.layout>
                <x-slot name="head">
                    <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Danışman Adı</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Atanan Toplam Lead</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Kayda Dönüşen</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Dönüşüm Başarı Oranı</th>
                </x-slot>
                <x-slot name="body">
                    @forelse($analytics['advisor_performance'] as $advisor)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40">
                            <td class="px-6 py-4 text-sm font-bold text-neutral-900 dark:text-white">{{ $advisor->name }}</td>
                            <td class="px-6 py-4 text-sm text-neutral-600 dark:text-neutral-400 font-mono">{{ $advisor->total_assigned }} Aday</td>
                            <td class="px-6 py-4 text-sm text-emerald-600 dark:text-emerald-500 font-bold font-mono">{{ $advisor->total_registered }} Kayıt</td>
                            <td class="px-6 py-4 text-sm font-bold">
                                @php
                                    $advRate = $advisor->total_assigned > 0 ? round(($advisor->total_registered / $advisor->total_assigned) * 100, 1) : 0;
                                @endphp
                                <span class="text-primary font-mono">%{{ $advRate }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-xs text-neutral-400">Henüz danışman performansı loglanmadı.</td>
                        </tr>
                    @endforelse
                </x-slot>
            </x-admin.table.layout>
        </div>

    </div>
@endsection
