@extends('layouts.admin')
@section('title', 'Ders Oturumları & Yoklama')
@section('content')
    <x-admin.crud.index-layout title="Yoklama Yönetimi" description="Ders oturumlarını planlayın, toplu yoklama girin ve devamsızlık durumlarını takip edin.">
        <x-slot name="actions">
            <a href="{{ route('admin.attendances.analytics') }}" class="px-4 py-2 bg-neutral-100 text-neutral-700 text-xs font-semibold rounded-xl hover:bg-neutral-200 transition">
                Devamsızlık Analizleri & Riskli Öğrenciler
            </a>
        </x-slot>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Panel: Yeni Oturum Başlatma -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Yeni Yoklama Oturumu Başlat</h3>
                
                <x-admin.form.layout :action="route('admin.attendances.sessions.store')" method="POST">
                    
                    <x-admin.form.field-group label="Derslik" id="classroom_id">
                        <select name="classroom_id" required class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                            @foreach($classrooms as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->code }})</option>
                            @endforeach
                        </select>
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Kurs / Ders" id="course_id">
                        <select name="course_id" required class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                            @foreach($courses as $co)
                                <option value="{{ $co->id }}">{{ $co->name }}</option>
                            @endforeach
                        </select>
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Eğitmen" id="teacher_id">
                        <select name="teacher_id" required class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}">{{ $t->user->name }}</option>
                            @endforeach
                        </select>
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Tarih" id="session_date">
                        <input type="date" name="session_date" required value="{{ date('Y-m-d') }}" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                    </x-admin.form.field-group>

                    <div class="grid grid-cols-2 gap-3">
                        <x-admin.form.field-group label="Başlangıç" id="start_time">
                            <input type="time" name="start_time" required value="09:00" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                        </x-admin.form.field-group>

                        <x-admin.form.field-group label="Bitiş" id="end_time">
                            <input type="time" name="end_time" required value="10:30" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                        </x-admin.form.field-group>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full py-2.5 bg-primary text-white text-xs font-semibold rounded-xl hover:bg-primary-dark transition shadow-sm">
                            Oturumu Başlat & Yoklama Al
                        </button>
                    </div>

                </x-admin.form.layout>
            </div>

            <!-- Sağ Panel: Oturumlar Listesi -->
            <div class="lg:col-span-2 bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Son Ders Oturumları</h3>
                
                <x-admin.table.layout>
                    <x-slot name="head">
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Tarih / Saat</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Derslik / Kurs</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Eğitmen</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Yoklama İşlemi</th>
                    </x-slot>
                    <x-slot name="body">
                        @forelse($sessions as $session)
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40">
                                <td class="px-4 py-3 text-xs font-medium text-neutral-900">
                                    {{ \Carbon\Carbon::parse($session->session_date)->format('d.m.Y') }}
                                    <div class="text-[10px] text-neutral-400 font-mono">{{ substr($session->start_time, 0, 5) }} - {{ substr($session->end_time, 0, 5) }}</div>
                                </td>
                                <td class="px-4 py-3 text-xs text-neutral-800">
                                    <div class="font-bold">{{ $session->classroom->name }}</div>
                                    <div class="text-[10px] text-neutral-500">{{ $session->course->name }}</div>
                                </td>
                                <td class="px-4 py-3 text-xs text-neutral-600">
                                    {{ $session->teacher->user->name }}
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    <a href="{{ route('admin.attendances.sessions.take', $session->id) }}" class="px-3 py-1 bg-primary/10 text-primary text-[11px] font-bold rounded-lg hover:bg-primary/20 transition">
                                        Yoklamaya Git ({{ $session->attendances_count ?? $session->attendances->count() }})
                                    </a>
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
