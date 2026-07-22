@extends('layouts.admin')
@section('title', 'Ödev Analitlikleri')
@section('content')
    <x-admin.crud.index-layout title="Ödev Başarı & Teslim İstatistikleri" description="Kurum geneli ödev teslim oranlarını, geç teslim yüzdelerini ve ortalama ödev puanlarını takip edin.">
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Toplam Verilen Ödev</h4>
                <div class="text-2xl font-bold">{{ $summary['total_assignments'] }} Ödev</div>
            </div>
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Toplam Teslim</h4>
                <div class="text-2xl font-bold text-primary">{{ $summary['total_submissions'] }} Kayıt</div>
            </div>
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Geç Teslim Oranı</h4>
                <div class="text-2xl font-bold text-amber-600">%{{ $summary['late_rate'] }}</div>
            </div>
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Ortalama Ödev Puanı</h4>
                <div class="text-2xl font-bold text-green-600">{{ $summary['avg_score'] }} / 100</div>
            </div>
        </div>

    </x-admin.crud.index-layout>
@endsection
