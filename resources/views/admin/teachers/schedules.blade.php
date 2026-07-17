@extends('layouts.admin')
@section('title', 'Ders Programları')
@section('content')
    <x-admin.crud.index-layout title="Ders Programı & Çakışma Yönetimi" description="Eğitmenlerinizin haftalık/günlük ders planlarını tanımlayın. Çakışma tespiti otomatik olarak yapılır.">
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Panel: Program Atama Formu -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white mb-4">Yeni Ders Programı Ekle</h3>
                
                @if($errors->has('start_time'))
                    <div class="p-4 mb-4 text-xs text-red-700 bg-red-100 rounded-xl border border-red-200">
                        ⚠️ {{ $errors->first('start_time') }}
                    </div>
                @endif

                <x-admin.form.layout :action="route('admin.teachers.schedules.store')" method="POST">
                    <x-admin.form.field-group label="Eğitmen" id="teacher_id">
                        <select name="teacher_id" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2">
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}" {{ (isset($teacher) && $teacher->id === $t->id) ? 'selected' : '' }}>{{ $t->user->name }}</option>
                            @endforeach
                        </select>
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Sınıf" id="classroom_id">
                        <select name="classroom_id" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2">
                            @foreach($classrooms as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Ders / Kurs" id="course_id">
                        <select name="course_id" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2">
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->name }}</option>
                            @endforeach
                        </select>
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Tarih" id="date">
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2">
                    </x-admin.form.field-group>

                    <div class="grid grid-cols-2 gap-4">
                        <x-admin.form.field-group label="Başlangıç Saati" id="start_time">
                            <input type="time" name="start_time" required value="09:00" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2">
                        </x-admin.form.field-group>

                        <x-admin.form.field-group label="Bitiş Saati" id="end_time">
                            <input type="time" name="end_time" required value="10:00" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2">
                        </x-admin.form.field-group>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full px-4 py-2 bg-primary text-white text-xs font-semibold rounded-lg hover:bg-primary-dark transition shadow-sm">
                            Programı Kaydet & Doğrula
                        </button>
                    </div>
                </x-admin.form.layout>
            </div>

            <!-- Sağ Panel: Seçilen Eğitmen Programı -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                    <div class="flex items-center justify-between border-b pb-4">
                        <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Eğitmen Takvim Kayıtları</h3>
                        <form action="{{ route('admin.teachers.schedules.index') }}" method="GET" class="flex gap-2">
                            <select name="teacher_id" onchange="this.form.submit()" class="text-xs bg-neutral-50 border rounded-lg p-1.5">
                                <option value="">Eğitmen Seçiniz</option>
                                @foreach($teachers as $t)
                                    <option value="{{ $t->id }}" {{ (isset($teacher) && $teacher->id === $t->id) ? 'selected' : '' }}>{{ $t->user->name }}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>

                    <div class="divide-y divide-neutral-100">
                        @forelse($schedules as $sch)
                            <div class="py-3 flex items-center justify-between text-xs">
                                <div>
                                    <span class="font-semibold text-neutral-800">{{ $sch->course ? $sch->course->name : 'Genel Ders' }}</span>
                                    <span class="text-neutral-400 ml-2">({{ $sch->classroom ? $sch->classroom->name : 'Sınıfsız' }})</span>
                                    <div class="text-[10px] text-neutral-400 mt-0.5">Tarih: {{ $sch->date->format('d.m.Y') }} | Saat: {{ $sch->start_time }} - {{ $sch->end_time }}</div>
                                </div>
                                <span class="px-2 py-0.5 text-[9px] bg-green-100 text-green-800 font-bold rounded">Çakışma Yok</span>
                            </div>
                        @empty
                            <div class="text-center text-neutral-400 text-xs py-8">Lütfen program listelemek için yukarıdan eğitmen seçin veya ders oluşturun.</div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </x-admin.crud.index-layout>
@endsection
