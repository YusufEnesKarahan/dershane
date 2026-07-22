@extends('layouts.admin')
@section('title', 'Öğretmen Analitiği')
@section('content')
    <x-admin.crud.index-layout title="Öğretmen Performans & İstatistik Analizi" description="Eğitmenin aktif atandığı sınıfları, ortalama başarı puanlarını ve yoklama takip istatistiklerini izleyin.">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Eğitmen</h4>
                <div class="text-xl font-bold text-neutral-900 dark:text-white">{{ $analytics['teacher']->user->name }}</div>
                <div class="text-xs text-neutral-400 mt-1">{{ $analytics['teacher']->title }}</div>
            </div>
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Aktif Atanan Sınıflar</h4>
                <div class="text-2xl font-bold text-primary">{{ $analytics['assigned_classes_count'] }} Sınıf</div>
            </div>
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Ortalama Performans Skoru</h4>
                <div class="text-2xl font-bold text-green-600">%{{ $analytics['average_performance_score'] }}</div>
            </div>
        </div>

        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm space-y-4">
            <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Genel Değerlendirme Raporu</h3>
            <p class="text-xs text-neutral-500 leading-relaxed">
                Öğretmenimizin ders anlatım verimliliği, veli geri bildirimleri ve TYT/AYT deneme başarı oranları incelendiğinde zümre standartlarını karşılamaktadır. Haftalık ders programı ve ders yoklaması alım düzeni performans skorunu desteklemektedir.
            </p>
        </div>

    </x-admin.crud.index-layout>
@endsection
