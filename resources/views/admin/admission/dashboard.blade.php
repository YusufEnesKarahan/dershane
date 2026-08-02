@extends('layouts.admin')
@section('title', 'Kayıt Yönetimi & Ön Kayıt Paneli')
@section('content')
    <x-admin.crud.index-layout title="Ön Kayıt & Kesin Kayıt Paneli" description="Aday öğrencilerin kayıt sürecini, evrak onaylarını, sözleşmelerini ve finansal kesin kayıtlarını tek bir merkezden yönetin.">
        <x-slot name="actions">
            <x-admin.button href="{{ route('admin.admission.workflow') }}" variant="primary" icon="M13 10V3L4 14h7v7l9-11h-7z">
                Kayıt Workflow
            </x-admin.button>
            <x-admin.button href="{{ route('admin.admission.index') }}" variant="secondary" icon="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                Ön Kayıtlar
            </x-admin.button>
        </x-slot>

        <!-- KPI Kartları -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <!-- Toplam Başvuru -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm relative overflow-hidden group hover:shadow-md transition-shadow">
                <div class="absolute top-0 right-0 p-4 opacity-5 dark:opacity-10 transition-transform group-hover:scale-110 group-hover:rotate-12">
                    <svg class="w-24 h-24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <div class="flex items-center justify-between mb-4 relative z-10">
                    <span class="text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Ön Kayıt Başvurusu</span>
                    <div class="p-2.5 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                </div>
                <div class="relative z-10">
                    <div class="text-3xl font-black text-neutral-900 dark:text-white font-mono tracking-tight">{{ $analytics['total_admissions'] }} <span class="text-base font-bold text-neutral-400 dark:text-neutral-500 tracking-normal">Başvuru</span></div>
                    <p class="text-[11px] text-neutral-500 dark:text-neutral-400 mt-2 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                        Sistemdeki toplam aday kaydı
                    </p>
                </div>
            </div>

            <!-- Bekleyen Evraklar -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm relative overflow-hidden group hover:shadow-md transition-shadow">
                <div class="absolute top-0 right-0 p-4 opacity-5 dark:opacity-10 transition-transform group-hover:scale-110 group-hover:-rotate-12">
                    <svg class="w-24 h-24 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div class="flex items-center justify-between mb-4 relative z-10">
                    <span class="text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Onay Bekleyen Evrak</span>
                    <div class="p-2.5 bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 rounded-xl relative">
                        <span class="absolute -top-1 -right-1 w-2.5 h-2.5 rounded-full bg-amber-500 border-2 border-white dark:border-neutral-900"></span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                </div>
                <div class="relative z-10">
                    <div class="text-3xl font-black text-neutral-900 dark:text-white font-mono tracking-tight">{{ $analytics['total_pending_documents'] }} <span class="text-base font-bold text-neutral-400 dark:text-neutral-500 tracking-normal">Belge</span></div>
                    <p class="text-[11px] text-neutral-500 dark:text-neutral-400 mt-2 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        İnceleme bekleyen kayıt evrakları
                    </p>
                </div>
            </div>

            <!-- Kesin Kayıt (Enrolled) -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm relative overflow-hidden group hover:shadow-md transition-shadow">
                <div class="absolute top-0 right-0 p-4 opacity-5 dark:opacity-10 transition-transform group-hover:scale-110 group-hover:rotate-12">
                    <svg class="w-24 h-24 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="flex items-center justify-between mb-4 relative z-10">
                    <span class="text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Kesin Kayıtlı Öğrenci</span>
                    <div class="p-2.5 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="relative z-10">
                    <div class="text-3xl font-black text-neutral-900 dark:text-white font-mono tracking-tight">{{ $analytics['total_enrolled'] }} <span class="text-base font-bold text-neutral-400 dark:text-neutral-500 tracking-normal">Öğrenci</span></div>
                    <p class="text-[11px] text-neutral-500 dark:text-neutral-400 mt-2 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Öğrenci kartı & faturası açılanlar
                    </p>
                </div>
            </div>

            <!-- Tahsil Edilen Kapora/Peşinat -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm relative overflow-hidden group hover:shadow-md transition-shadow">
                <div class="absolute top-0 right-0 p-4 opacity-5 dark:opacity-10 transition-transform group-hover:scale-110 group-hover:-rotate-12">
                    <svg class="w-24 h-24 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="flex items-center justify-between mb-4 relative z-10">
                    <span class="text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Tahsil Edilen Peşinat</span>
                    <div class="p-2.5 bg-primary/10 text-primary rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="relative z-10">
                    <div class="text-3xl font-black text-neutral-900 dark:text-white font-mono tracking-tight text-primary">₺{{ number_format($analytics['total_deposit_collected'], 2) }}</div>
                    <p class="text-[11px] text-neutral-500 dark:text-neutral-400 mt-2 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                        Ön kayıt kapora ve peşinat toplamı
                    </p>
                </div>
            </div>

        </div>

        <!-- Son Başvurular Listesi -->
        <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm overflow-hidden space-y-4">
            <div class="p-6 border-b border-neutral-100 dark:border-neutral-800">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Son Ön Kayıt Başvuruları</h3>
            </div>
            
            <x-admin.table.layout>
                <x-slot name="head">
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Başvuru No / Öğrenci</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Program / Şube</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Aşama</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">İşlem</th>
                </x-slot>
                <x-slot name="body">
                    @forelse($recentAdmissions as $adm)
                        <tr>
                            <td class="px-4 py-3 text-xs">
                                <span class="font-bold text-neutral-900 dark:text-white">{{ $adm::class ? ($adm->first_name . ' ' . $adm->last_name) : '' }}</span>
                                <div class="text-[10px] text-neutral-400 font-mono mt-0.5">{{ $adm->admission_no }}</div>
                            </td>
                            <td class="px-4 py-3 text-xs text-neutral-600 dark:text-neutral-300">
                                <span>{{ $adm->program ?? 'Genel Program' }}</span>
                                <div class="text-[10px] text-neutral-400 mt-0.5">{{ $adm->branch->name ?? 'Merkez' }}</div>
                            </td>
                            <td class="px-4 py-3 text-xs">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-700">
                                    {{ $adm->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs">
                                <a href="{{ route('admin.admission.show', $adm->id) }}" class="text-primary hover:underline font-bold">Detay & Yönet</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-xs text-neutral-400">Henüz başvuru bulunmamaktadır.</td>
                        </tr>
                    @endforelse
                </x-slot>
            </x-admin.table.layout>
        </div>

    </x-admin.crud.index-layout>
@endsection
