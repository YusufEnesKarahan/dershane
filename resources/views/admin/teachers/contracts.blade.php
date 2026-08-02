@extends('layouts.admin')
@section('title', 'Sözleşmeler')
@section('content')
    <x-admin.crud.index-layout title="İstihdam Sözleşmeleri" description="Eğitmenlerinizin işe başlama/bitiş tarihlerini ve çalışma sözleşmelerini tanımlayın.">
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Panel: Yeni Sözleşme Atama Formu -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm flex flex-col">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    Sözleşme Oluştur
                </h3>
                <x-admin.form.layout :action="route('admin.teachers.contracts.store')" method="POST">
                    <x-admin.form.field-group label="Eğitmen" id="teacher_id">
                        <select name="teacher_id" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 focus:ring-violet-500 focus:border-violet-500 dark:text-white transition-colors">
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}">{{ $t->user->name }}</option>
                            @endforeach
                        </select>
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Sözleşme Başlangıç Tarihi" id="start_date">
                        <input type="date" name="start_date" required value="{{ date('Y-m-d') }}" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 focus:ring-violet-500 focus:border-violet-500 dark:text-white transition-colors">
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Sözleşme Bitiş Tarihi" id="end_date">
                        <input type="date" name="end_date" value="{{ date('Y-m-d', strtotime('+1 year')) }}" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 focus:ring-violet-500 focus:border-violet-500 dark:text-white transition-colors">
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="İstihdam Türü" id="employment_type">
                        <select name="employment_type" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 focus:ring-violet-500 focus:border-violet-500 dark:text-white transition-colors">
                            <option value="Full-time">Full-time (Tam Zamanlı)</option>
                            <option value="Part-time">Part-time (Yarı Zamanlı)</option>
                            <option value="Contract">Freelance / Sözleşmeli</option>
                        </select>
                    </x-admin.form.field-group>

                    <div class="pt-4 mt-auto">
                        <button type="submit" class="w-full py-2.5 bg-violet-600 text-white text-sm font-bold rounded-xl hover:bg-violet-500 transition-colors shadow-lg shadow-violet-900/20 border border-violet-500/50 flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                            Sözleşmeyi İmzala
                        </button>
                    </div>
                </x-admin.form.layout>
            </div>

            <!-- Sağ Panel: Sözleşme Geçmişi -->
            <div class="lg:col-span-2 space-y-6 flex flex-col h-full">
                <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm flex-1 overflow-hidden flex flex-col">
                    <div class="p-6 border-b border-neutral-100 dark:border-neutral-800/50 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-neutral-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                            Aktif Sözleşmeler
                        </h3>
                    </div>
                    
                    <div class="p-0 overflow-y-auto flex-1">
                        <div class="divide-y divide-neutral-100 dark:divide-neutral-800/50">
                            @foreach($teachers as $t)
                                <div class="px-6 py-4 flex items-center justify-between hover:bg-neutral-50 dark:hover:bg-neutral-800/40 transition-colors group">
                                    <div>
                                        <div class="text-sm font-bold text-neutral-900 dark:text-white">{{ $t->user->name }}</div>
                                        @if($t->contracts->count() > 0)
                                            <div class="text-[11px] font-medium text-neutral-500 dark:text-neutral-400 mt-1 flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                                Başlangıç: {{ $t->contracts->last()->start_date->format('d.m.Y') }}
                                                @if($t->contracts->last()->end_date)
                                                    | Bitiş: {{ $t->contracts->last()->end_date->format('d.m.Y') }}
                                                @endif
                                            </div>
                                        @else
                                            <div class="text-[11px] font-medium text-neutral-500 dark:text-neutral-400 mt-1 italic">Sözleşme kaydı bulunmuyor.</div>
                                        @endif
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded bg-neutral-100 dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300 text-xs font-bold border border-neutral-200 dark:border-neutral-700">
                                        {{ $t->contracts->count() > 0 ? $t->contracts->last()->employment_type : 'Yok' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </x-admin.crud.index-layout>
@endsection
