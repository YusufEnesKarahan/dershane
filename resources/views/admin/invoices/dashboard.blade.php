@extends('layouts.admin')
@section('title', 'Finans & Tahsilat Dashboard')
@section('content')
    <x-admin.crud.index-layout title="Finansal Durum & Tahsilat Dashboard" description="Kurum geneli toplam fatura tutarlarını, yapılan tahsilatları ve kalan öğrenci borçlarını izleyin.">
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Toplam Kesilen Fatura</h4>
                <div class="text-2xl font-bold">₺{{ number_format($summary['total_invoiced'], 2) }}</div>
            </div>
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Toplam Tahsilat</h4>
                <div class="text-2xl font-bold text-green-600">₺{{ number_format($summary['total_collected'], 2) }}</div>
            </div>
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Bekleyen Öğrenci Borcu</h4>
                <div class="text-2xl font-bold text-red-600">₺{{ number_format($summary['total_pending_debt'], 2) }}</div>
            </div>
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Tahsilat Başarı Oranı</h4>
                <div class="text-2xl font-bold text-primary">%{{ $summary['collection_rate'] }}</div>
            </div>
        </div>

    </x-admin.crud.index-layout>
@endsection
