@extends('layouts.admin')
@section('title', 'Toplu Yoklama Girişi')
@section('content')
    <x-admin.crud.index-layout title="Toplu Yoklama Alma Formu" description="{{ $session->classroom->name }} — {{ $session->course->name }} ({{ \Carbon\Carbon::parse($session->session_date)->format('d.m.Y') }})">
        <x-slot name="actions">
            <a href="{{ route('admin.attendances.sessions.index') }}" class="px-4 py-2 bg-neutral-100 text-neutral-700 text-xs font-semibold rounded-xl hover:bg-neutral-200 transition">
                Oturumlara Dön
            </a>
        </x-slot>

        <x-admin.form.layout :action="route('admin.attendances.sessions.store-bulk', $session->id)" method="POST">
            <div class="bg-white dark:bg-neutral-900 p-8 rounded-2xl border border-neutral-100 shadow-premium-sm space-y-6">
                <div class="flex items-center justify-between border-b pb-4">
                    <div>
                        <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Ders Öğrenci Listesi</h3>
                        <p class="text-xs text-neutral-500">Öğrencilerin derse katılım durumlarını işaretleyip kaydedin.</p>
                    </div>
                    <div class="text-xs font-bold text-neutral-600 bg-neutral-100 px-3 py-1.5 rounded-lg">
                        Eğitmen: {{ $session->teacher->user->name }}
                    </div>
                </div>

                <div class="space-y-3">
                    @forelse($students as $index => $student)
                        @php
                            $existingAttendance = $session->attendances->where('student_id', $student->id)->first();
                            $currentStatusId = $existingAttendance ? $existingAttendance->attendance_status_id : $statuses->firstWhere('code', 'PRESENT')?->id;
                        @endphp
                        <div class="p-4 bg-neutral-50 dark:bg-neutral-800/50 rounded-xl flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-primary/10 text-primary text-xs font-bold flex items-center justify-center">
                                    {{ $index + 1 }}
                                </span>
                                <div>
                                    <div class="text-sm font-bold text-neutral-900 dark:text-white">{{ $student->full_name }}</div>
                                    <div class="text-[11px] text-neutral-400 font-mono">{{ $student->student_number }}</div>
                                </div>
                            </div>

                            <input type="hidden" name="attendances[{{ $index }}][student_id]" value="{{ $student->id }}">

                            <div class="flex items-center gap-2">
                                @foreach($statuses as $st)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="attendances[{{ $index }}][attendance_status_id]" value="{{ $st->id }}" {{ $currentStatusId == $st->id ? 'checked' : '' }} class="sr-only peer">
                                        <span class="px-3 py-1.5 rounded-lg text-xs font-bold border peer-checked:ring-2 peer-checked:ring-primary transition" style="border-color: {{ $st->color_code }}; color: {{ $st->color_code }}">
                                            {{ $st->name }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-xs text-neutral-400 py-8">Bu sınıfa / kursa kayıtlı öğrenci bulunamadı.</div>
                    @endforelse
                </div>

                <div class="pt-4 border-t flex items-center justify-between">
                    <button type="submit" class="px-6 py-2.5 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary-dark transition shadow-sm">
                        Yoklama Kaydını Tamamla
                    </button>
                </div>
            </div>
        </x-admin.form.layout>
    </x-admin.crud.index-layout>
@endsection
