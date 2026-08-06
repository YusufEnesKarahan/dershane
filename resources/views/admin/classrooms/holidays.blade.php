@extends('layouts.admin')
@section('title', 'Tatil Günleri')
@section('content')
    <x-admin.crud.index-layout title="Resmi & Kurumsal Tatiller" description="Tatil tarihlerinde çakışan ders yapılmasını engelleyen tatil günlerini tanımlayın.">
        <x-slot name="actions">
            <x-admin.button href="{{ route('admin.classrooms.index') }}" variant="secondary" icon="M10 19l-7-7m0 0l7-7m-7 7h18">
                Dersliklere Geri Dön
            </x-admin.button>
        </x-slot>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="space-y-6">
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden flex flex-col h-full">
                    <div class="p-6 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            Yeni Tatil Tanımla
                        </h3>
                    </div>
                    
                    <div class="p-6 flex-1">
                        <x-admin.form.layout :action="route('admin.classrooms.holidays.store')" method="POST">
                            <x-admin.form.field-group label="Tatil Adı" id="name" required>
                                <input type="text" name="name" id="name" required placeholder="Örn: 29 Ekim Cumhuriyet Bayramı" class="w-full bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-slate-900 dark:text-white transition-colors">
                            </x-admin.form.field-group>
                            
                            <div class="grid grid-cols-2 gap-4 mt-6">
                                <x-admin.form.field-group label="Başlangıç" id="start_date" required>
                                    <input type="date" name="start_date" id="start_date" required class="w-full bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-slate-900 dark:text-white transition-colors">
                                </x-admin.form.field-group>
                                <x-admin.form.field-group label="Bitiş" id="end_date" required>
                                    <input type="date" name="end_date" id="end_date" required class="w-full bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-slate-900 dark:text-white transition-colors">
                                </x-admin.form.field-group>
                            </div>

                            <div class="pt-6 mt-6 border-t border-slate-100 dark:border-slate-800">
                                <x-admin.button type="submit" variant="primary" icon="M5 13l4 4L19 7" class="w-full justify-center">
                                    Tatili Kaydet
                                </x-admin.button>
                            </div>
                        </x-admin.form.layout>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden flex flex-col h-full">
                    <div class="p-6 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            Kayıtlı Tatil Takvimi
                        </h3>
                    </div>
                    
                    <div class="p-0 flex-1">
                        @if($holidays->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                                    <thead class="bg-slate-50/80 dark:bg-slate-900/80 backdrop-blur-sm">
                                        <tr>
                                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider bg-slate-50/50 dark:bg-slate-800/30">Tatil Adı</th>
                                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider bg-slate-50/50 dark:bg-slate-800/30">Tarih Aralığı</th>
                                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider bg-slate-50/50 dark:bg-slate-800/30 w-24">Durum</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50 bg-white dark:bg-slate-900">
                                        @foreach($holidays as $holiday)
                                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors border-b border-slate-100 dark:border-slate-800/50 last:border-0 group">
                                                <td class="px-6 py-4">
                                                <div class="text-sm font-bold text-slate-900 dark:text-white">{{ $holiday->name }}</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-medium text-slate-700 dark:text-slate-300">
                                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                                    {{ \Carbon\Carbon::parse($holiday->start_date)->format('d.m.Y') }} - {{ \Carbon\Carbon::parse($holiday->end_date)->format('d.m.Y') }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-400">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                                                    Ders Yapılamaz
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <x-admin.empty-state
                            icon="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                            title="Tatil Günü Bulunamadı"
                            description="Sistemde henüz bir tatil günü tanımlanmamış. Sol taraftaki formu kullanarak yeni bir tatil (Örn: Resmi Tatiller) ekleyebilirsiniz."
                        />
                    @endif
                </div>
            </div>

        </div>
    </x-admin.crud.index-layout>
@endsection
