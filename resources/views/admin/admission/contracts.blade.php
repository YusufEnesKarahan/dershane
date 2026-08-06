@extends('layouts.admin')
@section('title', 'Kayıt Sözleşmeleri & Şablonlar')
@section('content')
    <x-admin.crud.index-layout title="Kayıt Sözleşmeleri & Şablon Yönetimi" description="Dinamik değişkenli öğrenci kayıt sözleşmelerini üretin, listeleyin ve imza durumlarını takip edin.">
        <x-slot name="actions">
            <x-admin.button href="{{ route('admin.admission.dashboard') }}" variant="secondary" icon="M10 19l-7-7m0 0l7-7m-7 7h18">
                Özet Panoya Dön
            </x-admin.button>
        </x-slot>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Panel: Aktif Şablonlar -->
            <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                    <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    Aktif Sözleşme Şablonları
                </h3>
                
                <div class="space-y-3">
                    @forelse($templates as $tpl)
                        <div class="p-4 bg-slate-50/50 dark:bg-slate-800/30 rounded-xl border border-slate-100 dark:border-slate-800 hover:border-primary/30 transition-colors group relative overflow-hidden">
                            <div class="absolute top-0 left-0 w-1 h-full bg-primary/80 group-hover:bg-primary transition-colors"></div>
                            
                            <div class="flex justify-between items-start mb-2 pl-2">
                                <span class="text-xs font-bold text-slate-900 dark:text-white leading-tight">{{ $tpl->title }}</span>
                                <span class="px-2 py-0.5 text-[10px] bg-primary/10 text-primary border border-primary/20 rounded-md font-mono tracking-wider shrink-0 ml-2 shadow-sm">{{ $tpl->code }}</span>
                            </div>
                            <div class="pl-2">
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-3 font-mono leading-relaxed bg-white dark:bg-slate-900 p-2 rounded-lg border border-slate-100 dark:border-slate-800 shadow-inner">{!! nl2br(e($tpl->content)) !!}</p>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-8 text-center bg-slate-50 dark:bg-slate-800/20 rounded-xl border border-dashed border-slate-200 dark:border-slate-700">
                            <svg class="w-6 h-6 text-slate-300 dark:text-slate-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">Aktif şablon bulunamadı</span>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Sağ Panel: Üretilen Sözleşmeler -->
            <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden flex flex-col">
                <div class="p-6 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5L18.5 7H20z" /></svg>
                        Üretilen Kayıt Sözleşmeleri
                    </h3>
                </div>
                
                <div class="flex-1">
                    <x-admin.table.layout>
                        <x-slot name="head">
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider bg-slate-50/50 dark:bg-slate-800/30">Sözleşme No</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider bg-slate-50/50 dark:bg-slate-800/30">Öğrenci / Başvuru</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider bg-slate-50/50 dark:bg-slate-800/30">Durum</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider bg-slate-50/50 dark:bg-slate-800/30">İşlem</th>
                        </x-slot>
                        <x-slot name="body">
                            @forelse($contracts as $cnt)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors border-b border-slate-100 dark:border-slate-800/50 last:border-0">
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-bold font-mono bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-md border border-slate-200 dark:border-slate-700 shadow-sm">
                                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                            {{ $cnt->contract_no }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <a href="{{ route('admin.admission.show', $cnt->admission->id) }}" class="text-sm font-bold text-slate-900 dark:text-white hover:text-primary transition-colors">
                                                {{ $cnt->admission->first_name }} {{ $cnt->admission->last_name }}
                                            </a>
                                            <div class="text-[11px] text-slate-500 dark:text-slate-400 font-mono mt-0.5">Başvuru: {{ $cnt->admission->admission_no }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($cnt->status === 'signed')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-400 shadow-sm">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                                İmzalandı
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-400 shadow-sm">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5 animate-pulse"></span>
                                                İmza Bekliyor
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @if($cnt->status !== 'signed')
                                            <form method="POST" action="{{ route('admin.contracts.sign', $cnt->id) }}" class="inline-block">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 dark:text-emerald-400 dark:bg-emerald-500/10 dark:hover:bg-emerald-500/20 rounded-lg transition-colors border border-emerald-200 dark:border-emerald-800">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                                    İmzalattır
                                                </button>
                                            </form>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-xs font-medium text-slate-400 dark:text-slate-500">
                                                <svg class="w-4 h-4 text-emerald-500/50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                İşlem Tamamlandı
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-0 py-0">
                                        <x-admin.empty-state
                                            icon="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5L18.5 7H20z"
                                            title="Sözleşme Bulunamadı"
                                            description="Henüz üretilmiş bir kayıt sözleşmesi bulunmuyor."
                                        />
                                    </td>
                                </tr>
                            @endforelse
                        </x-slot>
                    </x-admin.table.layout>
                </div>
            </div>

        </div>

    </x-admin.crud.index-layout>
@endsection
