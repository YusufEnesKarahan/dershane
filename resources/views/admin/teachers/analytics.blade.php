@extends('layouts.admin')
@section('title', 'Analitikler')
@section('content')
    <x-admin.crud.index-layout title="Eğitmen Analitik Raporları" description="Kadro genelindeki ortalama performans, ders doluluk oranları ve şubeler arası başarı karşılaştırmaları.">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Genel Memnuniyet Ortalaması</h4>
                <div class="text-2xl font-bold">4.85 / 5.00</div>
                <div class="text-[10px] text-green-500 mt-1">▲ %3.2 (Geçen aya göre)</div>
            </div>
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Ortalama Derse Katılım</h4>
                <div class="text-2xl font-bold">%98.4</div>
                <div class="text-[10px] text-green-500 mt-1">▲ %0.5 (Geçen aya göre)</div>
            </div>
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Toplam Eğitmen Kadrosu</h4>
                <div class="text-2xl font-bold">12 Aktif</div>
                <div class="text-[10px] text-neutral-400 mt-1">Sabit kadro</div>
            </div>
        </div>

        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <h3 class="text-sm font-bold text-neutral-900 dark:text-white mb-4">Karşılaştırmalı Performans Tablosu</h3>
            <x-admin.table.layout>
                <x-slot name="head">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">Eğitmen</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">Toplam İşlenen Ders</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">Öğrenci Memnuniyeti</th>
                </x-slot>
                <x-slot name="body">
                    @forelse($popular as $teacher)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40">
                            <td class="px-6 py-4 text-sm font-semibold text-neutral-900 dark:text-white">{{ $teacher->user->name }}</td>
                            <td class="px-6 py-4 text-sm text-neutral-500">45 Ders</td>
                            <td class="px-6 py-4 text-sm text-neutral-500">★ 4.90</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-sm text-neutral-400">Veri bulunmuyor.</td>
                        </tr>
                    @endforelse
                </x-slot>
            </x-admin.table.layout>
        </div>
    </x-admin.crud.index-layout>
@endsection
