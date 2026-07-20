@extends('layouts.admin')
@section('title', 'Derslik Analitikleri')
@section('content')
    <x-admin.crud.index-layout title="Derslik & Kapasite Analitiği" description="Derslik kullanım oranlarını ve haftalık yoğunluk verilerini inceleyin.">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Toplam Derslik Sayısı</h4>
                <div class="text-2xl font-bold">{{ $totalClassrooms }} Fiziki Sınıf</div>
            </div>
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Toplam Öğrenci Kapasitesi</h4>
                <div class="text-2xl font-bold">{{ $totalCapacity }} Öğrenci</div>
            </div>
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Çakışma Önleme Durumu</h4>
                <div class="text-2xl font-bold text-green-600">%100 Güvenli</div>
            </div>
        </div>
    </x-admin.crud.index-layout>
@endsection
