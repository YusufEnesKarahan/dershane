@extends('layouts.admin')
@section('title', 'Öğretmen Analitiği')
@section('content')
    <x-admin.crud.index-layout title="Öğretmen Performans & İstatistik Analizi" description="Eğitmenin aktif atandığı sınıfları, ortalama başarı puanlarını ve yoklama takip istatistiklerini izleyin.">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Eğitmen</h4>
                <div class="text-xl font-bold text-neutral-900 dark:text-white">{{ $analytics['teacher']->user->name }}</div>
                <div class="text-xs font-medium text-neutral-500 mt-1">{{ $analytics['teacher']->title }}</div>
            </div>
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Aktif Atanan Sınıflar</h4>
                <div class="text-2xl font-bold text-violet-600 dark:text-violet-500">{{ $analytics['assigned_classes_count'] }} Sınıf</div>
            </div>
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Ortalama Performans Skoru</h4>
                <div class="text-2xl font-bold text-green-600 dark:text-green-500">%{{ $analytics['average_performance_score'] }}</div>
            </div>
        </div>

        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-neutral-900 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                Genel Değerlendirme Raporu
            </h3>
            <p class="text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                Öğretmenimizin ders anlatım verimliliği, veli geri bildirimleri ve TYT/AYT deneme başarı oranları incelendiğinde zümre standartlarını karşılamaktadır. Haftalık ders programı ve ders yoklaması alım düzeni performans skorunu desteklemektedir.
            </p>
        </div>

    </x-admin.crud.index-layout>
@endsection
