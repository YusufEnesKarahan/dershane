@extends('layouts.admin')
@section('title', 'Akademik Takvim')
@section('content')
    <x-admin.crud.index-layout title="Akademik Dönem Yönetimi" description="Güz, Bahar ve Yaz akademik dönem tarihlerini tanımlayın.">
        <x-slot name="actions">
            <x-admin.button href="{{ route('admin.classrooms.index') }}" variant="secondary" icon="M10 19l-7-7m0 0l7-7m-7 7h18">
                Dersliklere Geri Dön
            </x-admin.button>
        </x-slot>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="space-y-6">
                <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm overflow-hidden flex flex-col h-full">
                    <div class="p-6 border-b border-neutral-100 dark:border-neutral-800 bg-neutral-50/50 dark:bg-neutral-800/30">
                        <h3 class="text-sm font-bold text-neutral-900 dark:text-white flex items-center gap-2">
                            <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                            Yeni Dönem Tanımla
                        </h3>
                    </div>
                    
                    <div class="p-6 flex-1">
                        <x-admin.form.layout :action="route('admin.classrooms.academic-calendar.store')" method="POST">
                            <x-admin.form.field-group label="Dönem Adı" id="name" required>
                                <input type="text" name="name" id="name" required placeholder="Örn: 2026-2027 Güz Dönemi" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-neutral-900 dark:text-white transition-colors">
                            </x-admin.form.field-group>
                            
                            <div class="grid grid-cols-2 gap-4 mt-6">
                                <x-admin.form.field-group label="Başlangıç" id="start_date" required>
                                    <input type="date" name="start_date" id="start_date" required class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-neutral-900 dark:text-white transition-colors">
                                </x-admin.form.field-group>
                                <x-admin.form.field-group label="Bitiş" id="end_date" required>
                                    <input type="date" name="end_date" id="end_date" required class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-neutral-900 dark:text-white transition-colors">
                                </x-admin.form.field-group>
                            </div>

                            <div class="pt-6 mt-6 border-t border-neutral-100 dark:border-neutral-800">
                                <x-admin.button type="submit" variant="primary" icon="M12 4v16m8-8H4" class="w-full justify-center">
                                    Dönemi Oluştur
                                </x-admin.button>
                            </div>
                        </x-admin.form.layout>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm overflow-hidden flex flex-col h-full">
                    <div class="p-6 border-b border-neutral-100 dark:border-neutral-800 bg-neutral-50/50 dark:bg-neutral-800/30">
                        <h3 class="text-sm font-bold text-neutral-900 dark:text-white flex items-center gap-2">
                            <svg class="w-4 h-4 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            Tanımlı Akademik Dönemler
                        </h3>
                    </div>
                    
                    <div class="p-0 flex-1">
                        @if($terms->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-800">
                                    <thead class="bg-neutral-50/80 dark:bg-neutral-900/80 backdrop-blur-sm">
                                        <tr>
                                            <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider bg-neutral-50/50 dark:bg-neutral-800/30">Dönem Adı</th>
                                            <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider bg-neutral-50/50 dark:bg-neutral-800/30">Tarih Aralığı</th>
                                            <th class="px-6 py-4 text-right text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider bg-neutral-50/50 dark:bg-neutral-800/30 w-24">Durum</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800/50 bg-white dark:bg-neutral-900">
                                        @foreach($terms as $term)
                                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40 transition-colors border-b border-neutral-100 dark:border-neutral-800/50 last:border-0 group">
                                                <td class="px-6 py-4">
                                                    <div class="text-sm font-bold text-neutral-900 dark:text-white">{{ $term->name }}</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-neutral-100 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 text-xs font-medium text-neutral-700 dark:text-neutral-300">
                                                    <svg class="w-3.5 h-3.5 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                                    {{ \Carbon\Carbon::parse($term->start_date)->format('d.m.Y') }} - {{ \Carbon\Carbon::parse($term->end_date)->format('d.m.Y') }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                @if($term->is_active)
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-400">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                                        Aktif Dönem
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-neutral-100 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-400">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-neutral-400 mr-1.5"></span>
                                                        Geçmiş
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <x-admin.empty-state
                            icon="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"
                            title="Akademik Dönem Bulunamadı"
                            description="Sistemde henüz bir akademik dönem tanımlanmamış. Sol taraftaki formu kullanarak yeni bir dönem (Örn: Güz Dönemi) ekleyebilirsiniz."
                        />
                    @endif
                </div>
            </div>

        </div>
    </x-admin.crud.index-layout>
@endsection
