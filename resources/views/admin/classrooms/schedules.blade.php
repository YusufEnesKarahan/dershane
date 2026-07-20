@extends('layouts.admin')
@section('title', 'Haftalık Ders Programı')
@section('content')
    <x-admin.crud.index-layout title="Haftalık Ders Programı & Çakışma Yönetimi" description="Dersliklerin haftalık saat bloklarını görün, eğitmen ve sınıf çakışmalarını engelleyin.">
        
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            
            <!-- Sol Form: Ders Ekleme -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Programa Ders Ekle</h3>
                
                <x-admin.form.layout :action="route('admin.classrooms.schedules.store')" method="POST">
                    
                    <x-admin.form.field-group label="Derslik" id="classroom_id">
                        <select name="classroom_id" required class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                            @foreach($classrooms as $c)
                                <option value="{{ $c->id }}" {{ $selectedClassroomId == $c->id ? 'selected' : '' }}>{{ $c->code }} - {{ $c->name }}</option>
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

                    <x-admin.form.field-group label="Kurs / Ders" id="course_id">
                        <select name="course_id" required class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                            @foreach($courses as $co)
                                <option value="{{ $co->id }}">{{ $co->name }}</option>
                            @endforeach
                        </select>
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Gün" id="day_of_week">
                        <select name="day_of_week" required class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                            <option value="1">Pazartesi</option>
                            <option value="2">Salı</option>
                            <option value="3">Çarşamba</option>
                            <option value="4">Perşembe</option>
                            <option value="5">Cuma</option>
                            <option value="6">Cumartesi</option>
                            <option value="7">Pazar</option>
                        </select>
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
                            Dersi Programa Ekle & Kontrol Et
                        </button>
                    </div>

                </x-admin.form.layout>
            </div>

            <!-- Sağ Panel: Derslik Program Listesi / Gridi -->
            <div class="lg:col-span-3 bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm space-y-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Derslik Haftalık Görünümü</h3>
                    <form method="GET" class="flex items-center gap-2">
                        <select name="classroom_id" onchange="this.form.submit()" class="text-xs bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-1.5 font-bold">
                            @foreach($classrooms as $c)
                                <option value="{{ $c->id }}" {{ $selectedClassroomId == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->code }})</option>
                            @endforeach
                        </select>
                    </form>
                </div>

                <div class="grid grid-cols-7 gap-2 border-t pt-4">
                    @php $days = [1 => 'Pzt', 2 => 'Sal', 3 => 'Çar', 4 => 'Per', 5 => 'Cum', 6 => 'Cmt', 7 => 'Paz']; @endphp
                    @foreach($days as $num => $dayName)
                        <div class="text-center font-bold text-xs text-neutral-400 py-2 border-b">{{ $dayName }}</div>
                    @endforeach

                    @foreach($days as $num => $dayName)
                        <div class="min-h-[220px] bg-neutral-50/50 p-1.5 rounded-lg border border-dashed border-neutral-200 space-y-2">
                            @php $daySchedules = $schedules->where('day_of_week', $num); @endphp
                            @forelse($daySchedules as $sch)
                                <div class="p-2 rounded-lg text-[11px] text-white shadow-sm font-medium space-y-1" style="background-color: {{ $sch->color_code }}">
                                    <div class="font-bold">{{ $sch->course->name }}</div>
                                    <div class="opacity-90">{{ $sch->teacher->user->name }}</div>
                                    <div class="text-[9px] opacity-75 font-mono">{{ substr($sch->start_time, 0, 5) }} - {{ substr($sch->end_time, 0, 5) }}</div>
                                </div>
                            @empty
                                <div class="text-[10px] text-neutral-300 text-center py-4">Boş Slot</div>
                            @endforelse
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

    </x-admin.crud.index-layout>
@endsection
