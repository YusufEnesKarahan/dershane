@extends('layouts.admin')
@section('title', 'Ders Oturumları & Yoklama')
@section('content')
    <x-admin.crud.index-layout title="Yoklama Yönetimi" description="Ders oturumlarını planlayın, toplu yoklama girin ve devamsızlık durumlarını takip edin.">
        <x-slot name="actions">
            <x-admin.button href="{{ route('admin.attendances.analytics') }}" variant="secondary" icon="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                Devamsızlık Analizleri & Riskli Öğrenciler
            </x-admin.button>
        </x-slot>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Panel: Yeni Oturum Başlatma -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Yeni Yoklama Oturumu Başlat</h3>
                
                <x-admin.form.layout :action="route('admin.attendances.sessions.store')" method="POST">
                    
                    <x-admin.form.field-group label="Derslik" id="classroom_id">
                        <select name="classroom_id" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                            @foreach($classrooms as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->code }})</option>
                            @endforeach
                        </select>
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Kurs / Ders" id="course_id">
                        <select name="course_id" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                            @foreach($courses as $co)
                                <option value="{{ $co->id }}">{{ $co->name }}</option>
                            @endforeach
                        </select>
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Eğitmen" id="teacher_id">
                        <select name="teacher_id" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}">{{ $t->user->name }}</option>
                            @endforeach
                        </select>
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Tarih" id="session_date">
                        <input type="date" name="session_date" required value="{{ date('Y-m-d') }}" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                    </x-admin.form.field-group>

                    <div class="grid grid-cols-2 gap-3">
                        <x-admin.form.field-group label="Başlangıç" id="start_time">
                            <input type="time" name="start_time" required value="09:00" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                        </x-admin.form.field-group>

                        <x-admin.form.field-group label="Bitiş" id="end_time">
                            <input type="time" name="end_time" required value="10:30" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                        </x-admin.form.field-group>
                    </div>

                    <div class="pt-4 border-t border-neutral-100 dark:border-neutral-800 mt-4">
                        <x-admin.button type="submit" variant="primary" icon="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z M21 12a9 9 0 11-18 0 9 9 0 0118 0z" class="w-full justify-center">
                            Oturumu Başlat & Yoklama Al
                        </x-admin.button>
                    </div>

                </x-admin.form.layout>
            </div>

            <!-- Sağ Panel: Oturumlar Listesi -->
            <div class="lg:col-span-2 bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Son Ders Oturumları</h3>
                
                <x-admin.table.layout>
                    <x-slot name="head">
                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Tarih / Saat</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Derslik / Kurs</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Eğitmen</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Yoklama İşlemi</th>
                    </x-slot>
                    <x-slot name="body">
                        @forelse($sessions as $session)
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40">
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-neutral-50 dark:bg-neutral-800/50 border border-neutral-100 dark:border-neutral-700/50 text-[11px] font-medium text-neutral-600 dark:text-neutral-300 font-mono w-fit mb-1">
                                            <svg class="w-3.5 h-3.5 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                            {{ \Carbon\Carbon::parse($session->session_date)->format('d.m.Y') }}
                                        </span>
                                        <div class="text-[11px] text-neutral-500 dark:text-neutral-400 font-mono">{{ substr($session->start_time, 0, 5) }} - {{ substr($session->end_time, 0, 5) }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-bold text-sm text-neutral-900 dark:text-white">{{ $session->classroom->name }}</span>
                                    <div class="text-[11px] text-neutral-500 dark:text-neutral-400 mt-0.5">{{ $session->course->name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ $session->teacher->user->name }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <x-admin.button href="{{ route('admin.attendances.sessions.take', $session->id) }}" variant="secondary" class="!px-3 !py-1.5 !text-[11px]">
                                        Yoklamaya Git ({{ $session->attendances_count ?? $session->attendances->count() }})
                                    </x-admin.button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-xs text-neutral-400">Henüz yoklama oturumu başlatılmadı.</td>
                            </tr>
                        @endforelse
                    </x-slot>
                </x-admin.table.layout>
            </div>

        </div>
    </x-admin.crud.index-layout>
@endsection
