@extends('layouts.admin')
@section('title', 'Derslik Analitikleri')
@section('content')
    <x-admin.crud.index-layout title="Derslik & Kapasite Analitiği" description="Derslik kullanım oranlarını ve haftalık yoğunluk verilerini inceleyin.">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Toplam Derslik Sayısı -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm relative overflow-hidden group hover:border-emerald-500/30 transition-colors">
                <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-500/5 dark:bg-emerald-500/10 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                <div class="relative z-10 flex items-start justify-between">
                    <div>
                        <h4 class="text-[10px] font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-widest mb-1.5 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Toplam Derslik
                        </h4>
                        <div class="text-3xl font-black text-neutral-900 dark:text-white flex items-baseline gap-1">
                            {{ $totalClassrooms }} <span class="text-xs font-semibold text-neutral-500 dark:text-neutral-400">Fiziki Sınıf</span>
                        </div>
                    </div>
                    <div class="p-3 bg-neutral-50 dark:bg-neutral-800/50 rounded-xl text-neutral-400 dark:text-neutral-500">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    </div>
                </div>
            </div>

            <!-- Toplam Öğrenci Kapasitesi -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm relative overflow-hidden group hover:border-indigo-500/30 transition-colors">
                <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-500/5 dark:bg-indigo-500/10 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                <div class="relative z-10 flex items-start justify-between">
                    <div>
                        <h4 class="text-[10px] font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-widest mb-1.5 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                            Toplam Kapasite
                        </h4>
                        <div class="text-3xl font-black text-neutral-900 dark:text-white flex items-baseline gap-1">
                            {{ $totalCapacity }} <span class="text-xs font-semibold text-neutral-500 dark:text-neutral-400">Öğrenci</span>
                        </div>
                    </div>
                    <div class="p-3 bg-neutral-50 dark:bg-neutral-800/50 rounded-xl text-neutral-400 dark:text-neutral-500">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    </div>
                </div>
            </div>

            <!-- Çakışma Önleme Durumu -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm relative overflow-hidden group hover:border-emerald-500/30 transition-colors">
                <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-500/5 dark:bg-emerald-500/10 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                <div class="relative z-10 flex items-start justify-between">
                    <div>
                        <h4 class="text-[10px] font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-widest mb-1.5 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Çakışma Kontrolü
                        </h4>
                        <div class="text-3xl font-black text-emerald-600 dark:text-emerald-400 flex items-baseline gap-1">
                            %100 <span class="text-xs font-semibold text-emerald-500/70">Güvenli</span>
                        </div>
                    </div>
                    <div class="p-3 bg-emerald-50 dark:bg-emerald-500/10 rounded-xl text-emerald-500">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                </div>
            </div>
        </div>
    </x-admin.crud.index-layout>
@endsection
