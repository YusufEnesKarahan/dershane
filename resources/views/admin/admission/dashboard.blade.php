@extends('layouts.admin')
@section('title', 'Kayıt Yönetimi & Ön Kayıt Paneli')
@section('content')
    <div class="space-y-6">
        
        <!-- Üst Banner -->
        <div class="bg-gradient-to-r from-emerald-900 to-slate-900 p-8 rounded-3xl text-white shadow-premium flex flex-col md:flex-row md:items-center md:justify-between gap-6 border border-emerald-950">
            <div>
                <span class="px-2.5 py-1 text-[10px] uppercase font-bold tracking-widest bg-emerald-500/20 text-emerald-300 rounded-full border border-emerald-500/30">Student Admission & Enrollment</span>
                <h1 class="text-2xl font-black mt-2">Ön Kayıt & Kesin Kayıt Paneli</h1>
                <p class="text-xs text-emerald-100 mt-1">Aday öğrencilerin kayıt sürecini, evrak onaylarını, sözleşmelerini ve finansal kesin kayıtlarını yönetin.</p>
            </div>
            
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.admission.workflow') }}" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-xs font-bold rounded-xl transition shadow-lg shadow-emerald-950 flex items-center gap-1.5 border border-emerald-500">
                    Kayıt Workflow (Aşamalar)
                </a>
                <a href="{{ route('admin.admission.index') }}" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-xs font-bold rounded-xl transition border border-slate-700">
                    Ön Kayıt Başvuruları
                </a>
            </div>
        </div>

        <!-- KPI Kartları -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <!-- Toplam Başvuru -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-neutral-400 uppercase">Ön Kayıt Başvurusu</span>
                    <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                </div>
                <div>
                    <div class="text-3xl font-black text-neutral-900 dark:text-white font-mono">{{ $analytics['total_admissions'] }} Başvuru</div>
                    <p class="text-[11px] text-neutral-500 mt-1">Sistemdeki toplam aday kaydı</p>
                </div>
            </div>

            <!-- Bekleyen Evraklar -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-neutral-400 uppercase">Onay Bekleyen Evrak</span>
                    <div class="p-2 bg-amber-50 text-amber-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                </div>
                <div>
                    <div class="text-3xl font-black text-neutral-900 dark:text-white font-mono">{{ $analytics['total_pending_documents'] }} Belge</div>
                    <p class="text-[11px] text-neutral-500 mt-1">İnceleme bekleyen kayıt evrakları</p>
                </div>
            </div>

            <!-- Kesin Kayıt (Enrolled) -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-neutral-400 uppercase">Kesin Kayıtlı Öğrenci</span>
                    <div class="p-2 bg-green-50 text-green-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div>
                    <div class="text-3xl font-black text-neutral-900 dark:text-white font-mono">{{ $analytics['total_enrolled'] }} Öğrenci</div>
                    <p class="text-[11px] text-neutral-500 mt-1">Öğrenci kartı & faturası açılanlar</p>
                </div>
            </div>

            <!-- Tahsil Edilen Kapora/Peşinat -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-neutral-400 uppercase">Tahsil Edilen Peşinat</span>
                    <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div>
                    <div class="text-3xl font-black text-neutral-900 dark:text-white font-mono">₺{{ number_format($analytics['total_deposit_collected'], 2) }}</div>
                    <p class="text-[11px] text-neutral-500 mt-1">Ön kayıt kapora ve peşinat toplamı</p>
                </div>
            </div>

        </div>

        <!-- Son Başvurular Listesi -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
            <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Son Ön Kayıt Başvuruları</h3>
            
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

    </div>
@endsection
