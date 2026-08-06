@extends('layouts.admin')
@section('title', 'Kayıt Workflow Pipeline')
@section('content')
    <x-admin.crud.index-layout title="Kayıt Workflow Pipeline" description="Başvuruların ön kayıttan kesin kayda kadarki tüm süreç aşamalarını Kanban görünümünde takip edin ve yönetin.">
        <x-slot name="actions">
            <x-admin.button href="{{ route('admin.admission.dashboard') }}" variant="secondary" icon="M10 19l-7-7m0 0l7-7m-7 7h18">
                Özet Panoya Dön
            </x-admin.button>
        </x-slot>

        @php
            $stages = [
                'pre_registration' => '1. Ön Kayıt Başvuruları',
                'document_pending' => '2. Evrak Bekleyenler',
                'document_completed' => '3. Evrak Onaylananlar',
                'contract_ready' => '4. Sözleşme İmzası',
                'payment_pending' => '5. Ödeme / Finans',
                'enrolled' => '6. Kesin Kayıtlılar'
            ];
        @endphp

        <div class="flex overflow-x-auto gap-4 pb-6 snap-x">
            @foreach($stages as $stageKey => $stageTitle)
                <div class="snap-start shrink-0 w-[280px] bg-slate-50/50 dark:bg-slate-800/20 p-4 rounded-2xl border border-slate-100 dark:border-slate-800 flex flex-col max-h-[calc(100vh-200px)]">
                    
                    @php
                        $stageAdmissions = $admissions->filter(fn($a) => $a->status === $stageKey);
                    @endphp

                    <!-- Kolon Başlığı -->
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3 mb-4 sticky top-0 bg-slate-50/90 dark:bg-slate-900/90 backdrop-blur z-10">
                        <span class="text-xs font-black text-slate-800 dark:text-slate-200 truncate pr-2">{{ $stageTitle }}</span>
                        <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 text-[10px] font-black bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-full text-slate-600 dark:text-slate-400 shadow-sm">{{ $stageAdmissions->count() }}</span>
                    </div>

                    <!-- Kartlar -->
                    <div class="space-y-3 overflow-y-auto flex-1 pr-1 custom-scrollbar">
                        @forelse($stageAdmissions as $adm)
                            <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm hover:shadow-md transition-all group relative">
                                
                                <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('admin.admission.show', $adm->id) }}" class="p-1 text-primary hover:bg-primary/10 rounded-md transition-colors tooltip-trigger" data-tooltip="Detaya Git">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                    </a>
                                </div>

                                <div class="text-sm font-bold text-slate-900 dark:text-white pr-6">
                                    <a href="{{ route('admin.admission.show', $adm->id) }}" class="hover:text-primary transition-colors">
                                        {{ $adm->first_name }} {{ $adm->last_name }}
                                    </a>
                                </div>
                                <div class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $adm->admission_no }}</div>
                                
                                <div class="mt-3 flex items-center justify-between">
                                    <div class="inline-flex items-center gap-1 text-[10px] font-bold text-slate-500 bg-slate-50 dark:bg-slate-800 px-2 py-1 rounded-md">
                                        <svg class="w-3 h-3 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        ₺{{ number_format($adm->total_amount, 2) }}
                                    </div>
                                </div>
                                
                                <form method="POST" action="{{ route('admin.admission.status.update', $adm->id) }}" class="pt-3 border-t border-slate-50 dark:border-slate-800/60 mt-3">
                                    @csrf
                                    <select name="status" onchange="this.form.submit()" class="w-full text-[10px] font-medium bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg shadow-sm focus:ring-primary focus:border-primary text-slate-600 dark:text-slate-300 transition-colors">
                                        <option value="">Aşama Değiştir</option>
                                        @foreach($stages as $k => $v)
                                            <option value="{{ $k }}" {{ $adm->status === $k ? 'selected' : '' }}>{{ $v }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </div>
                        @empty
                            <div class="flex flex-col items-center justify-center py-8 text-center bg-white/50 dark:bg-slate-900/50 rounded-xl border border-dashed border-slate-200 dark:border-slate-700">
                                <svg class="w-6 h-6 text-slate-300 dark:text-slate-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                                <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">Bu aşamada başvuru yok</span>
                            </div>
                        @endforelse
                    </div>

                </div>
            @endforeach
        </div>

    </x-admin.crud.index-layout>
@endsection
