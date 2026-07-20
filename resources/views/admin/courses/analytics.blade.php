@extends('layouts.admin')
@section('title', 'Kurs Analitikleri')
@section('content')
    <x-admin.crud.index-layout title="Kurs Analitik Raporları" description="Kurs doluluk oranlarını, eğitmen dağılım istatistiklerini ve şube performans grafiklerini analiz edin.">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Toplam Kurs Sayısı</h4>
                <div class="text-2xl font-bold">{{ $analytics['total_courses'] }} Program</div>
                <div class="text-[10px] text-green-500 mt-1">▲ %2.4 (Geçen haftaya göre)</div>
            </div>
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Aktif Satışta</h4>
                <div class="text-2xl font-bold">{{ $analytics['active_courses'] }} Kurs</div>
                <div class="text-[10px] text-green-500 mt-1">İstikrarlı dağılım</div>
            </div>
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Toplam Kapasite Hacmi</h4>
                <div class="text-2xl font-bold">{{ $analytics['total_capacity'] }} Öğrenci</div>
                <div class="text-[10px] text-neutral-400 mt-1">Kayıt kontenjan havuzu</div>
            </div>
        </div>

        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <h3 class="text-sm font-bold text-neutral-900 dark:text-white mb-4">En Çok Tercih Edilen Kurs Programları</h3>
            <x-admin.table.layout>
                <x-slot name="head">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">Kurs Adı</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">Kontenjan Limiti</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">Seviye</th>
                </x-slot>
                <x-slot name="body">
                    @forelse($popular as $c)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40">
                            <td class="px-6 py-4 text-sm font-semibold text-neutral-900 dark:text-white">{{ $c->name }}</td>
                            <td class="px-6 py-4 text-sm text-neutral-500">{{ $c->capacity }} Kişi</td>
                            <td class="px-6 py-4 text-sm text-neutral-500">{{ $c->level ? $c->level->name : 'Genel' }}</td>
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
