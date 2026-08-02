@extends('layouts.admin')
@section('title', 'Ders Programları')
@section('content')
    <x-admin.crud.index-layout title="Ders Programı & Çakışma Yönetimi" description="Eğitmenlerinizin haftalık/günlük ders planlarını tanımlayın. Çakışma tespiti otomatik olarak yapılır.">
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Panel: Program Atama Formu -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm flex flex-col">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    Yeni Ders Programı Ekle
                </h3>
                
                @if($errors->has('start_time'))
                    <div class="p-4 mb-4 text-xs text-red-700 bg-red-100 rounded-xl border border-red-200">
                        ⚠️ {{ $errors->first('start_time') }}
                    </div>
                @endif

                <x-admin.form.layout :action="route('admin.teachers.schedules.store')" method="POST">
                    <x-admin.form.field-group label="Eğitmen" id="teacher_id">
                        <select name="teacher_id" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 focus:ring-violet-500 focus:border-violet-500 dark:text-white transition-colors">
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}" {{ (isset($teacher) && $teacher->id === $t->id) ? 'selected' : '' }}>{{ $t->user->name }}</option>
                            @endforeach
                        </select>
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Sınıf" id="classroom_id">
                        <select name="classroom_id" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 focus:ring-violet-500 focus:border-violet-500 dark:text-white transition-colors">
                            @foreach($classrooms as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Ders / Kurs" id="course_id">
                        <select name="course_id" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 focus:ring-violet-500 focus:border-violet-500 dark:text-white transition-colors">
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->name }}</option>
                            @endforeach
                        </select>
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Tarih" id="date">
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 focus:ring-violet-500 focus:border-violet-500 dark:text-white transition-colors">
                    </x-admin.form.field-group>

                    <div class="grid grid-cols-2 gap-4">
                        <x-admin.form.field-group label="Başlangıç Saati" id="start_time">
                            <input type="time" name="start_time" required value="09:00" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 focus:ring-violet-500 focus:border-violet-500 dark:text-white transition-colors">
                        </x-admin.form.field-group>

                        <x-admin.form.field-group label="Bitiş Saati" id="end_time">
                            <input type="time" name="end_time" required value="10:00" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 focus:ring-violet-500 focus:border-violet-500 dark:text-white transition-colors">
                        </x-admin.form.field-group>
                    </div>

                    <div class="pt-4 mt-auto">
                        <button type="submit" class="w-full py-2.5 bg-violet-600 text-white text-sm font-bold rounded-xl hover:bg-violet-500 transition-colors shadow-lg shadow-violet-900/20 border border-violet-500/50 flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            Programı Kaydet & Doğrula
                        </button>
                    </div>
                </x-admin.form.layout>
            </div>

            <!-- Sağ Panel: Seçilen Eğitmen Programı -->
            <div class="lg:col-span-2 space-y-6 flex flex-col h-full">
                <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm flex-1 overflow-hidden flex flex-col">
                    <div class="p-6 border-b border-neutral-100 dark:border-neutral-800/50 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-neutral-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            Eğitmen Takvim Kayıtları
                        </h3>
                        <form action="{{ route('admin.teachers.schedules.index') }}" method="GET" class="flex gap-2">
                            <select name="teacher_id" onchange="this.form.submit()" class="text-xs bg-neutral-50 dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 rounded-lg p-2 dark:text-white transition-colors">
                                <option value="">Eğitmen Seçiniz</option>
                                @foreach($teachers as $t)
                                    <option value="{{ $t->id }}" {{ (isset($teacher) && $teacher->id === $t->id) ? 'selected' : '' }}>{{ $t->user->name }}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                    
                    <div class="p-0 overflow-y-auto flex-1">
                        <div class="divide-y divide-neutral-100 dark:divide-neutral-800/50">
                            @forelse($schedules as $sch)
                                <div class="px-6 py-4 flex items-center justify-between hover:bg-neutral-50 dark:hover:bg-neutral-800/40 transition-colors group">
                                    <div>
                                        <div class="text-sm font-bold text-neutral-900 dark:text-white flex items-center gap-2">
                                            {{ $sch->course ? $sch->course->name : 'Genel Ders' }}
                                            <span class="text-[11px] font-medium text-neutral-500 dark:text-neutral-400">({{ $sch->classroom ? $sch->classroom->name : 'Sınıfsız' }})</span>
                                        </div>
                                        <div class="text-[11px] font-medium text-neutral-500 dark:text-neutral-400 mt-1 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            Tarih: {{ $sch->date->format('d.m.Y') }} <span class="mx-1">•</span> Saat: {{ $sch->start_time }} - {{ $sch->end_time }}
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-green-50 dark:bg-green-500/10 text-green-700 dark:text-green-400 text-xs font-bold border border-green-200/50 dark:border-green-500/20">Çakışma Yok</span>
                                </div>
                            @empty
                                <div class="px-6 py-8">
                                    <x-admin.empty-state
                                        icon="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                        title="Takvim Kaydı Yok"
                                        description="Lütfen program listelemek için yukarıdan eğitmen seçin veya ders oluşturun."
                                    />
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </x-admin.crud.index-layout>
@endsection
