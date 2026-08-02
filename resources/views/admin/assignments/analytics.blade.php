@extends('layouts.admin')
@section('title', 'Ödev Analitlikleri')
@section('content')
    <x-admin.crud.index-layout title="Ödev Başarı & Teslim İstatistikleri" description="Kurum geneli ödev teslim oranlarını, geç teslim yüzdelerini ve ortalama ödev puanlarını takip edin.">
        <x-slot name="actions">
            <x-admin.button href="{{ route('admin.assignments.index') }}" variant="secondary" icon="M10 19l-7-7m0 0l7-7m-7 7h18">
                Ödev Listesine Dön
            </x-admin.button>
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Toplam Verilen Ödev</h4>
                <div class="text-2xl font-black text-neutral-900 dark:text-white">{{ $summary['total_assignments'] }} <span class="text-sm font-medium text-neutral-400">Ödev</span></div>
            </div>
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Toplam Teslim</h4>
                <div class="text-2xl font-black text-primary">{{ $summary['total_submissions'] }} <span class="text-sm font-medium text-primary/70">Kayıt</span></div>
            </div>
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Geç Teslim Oranı</h4>
                <div class="text-2xl font-black text-amber-600 dark:text-amber-500">%{{ $summary['late_rate'] }}</div>
            </div>
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Ortalama Ödev Puanı</h4>
                <div class="text-2xl font-black text-emerald-600 dark:text-emerald-500">{{ $summary['avg_score'] }} <span class="text-sm font-medium text-emerald-600/70 dark:text-emerald-500/70">/ 100</span></div>
            </div>
        </div>

    </x-admin.crud.index-layout>
@endsection
