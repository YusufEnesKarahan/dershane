@extends('layouts.admin')
@section('title', 'Haftalık Ders Programı')
@section('content')
    <x-admin.crud.index-layout title="Haftalık Ders Programı & Çakışma Yönetimi" description="Dersliklerin haftalık saat bloklarını görün, eğitmen ve sınıf çakışmalarını engelleyin.">
        <x-slot name="actions">
            <x-admin.button href="{{ route('admin.classrooms.index') }}" variant="secondary" icon="M10 19l-7-7m0 0l7-7m-7 7h18">
                Dersliklere Geri Dön
            </x-admin.button>
        </x-slot>
        
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            
            <!-- Sol Form: Ders Ekleme -->
            <div class="space-y-6">
                <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm overflow-hidden flex flex-col h-full">
                    <div class="p-6 border-b border-neutral-100 dark:border-neutral-800 bg-neutral-50/50 dark:bg-neutral-800/30">
                        <h3 class="text-sm font-bold text-neutral-900 dark:text-white flex items-center gap-2">
                            <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                            Programa Ders Ekle
                        </h3>
                    </div>
                    
                    <div class="p-6 flex-1">
                        <x-admin.form.layout :action="route('admin.classrooms.schedules.store')" method="POST">
                            
                            <div class="space-y-5">
                                <x-admin.form.field-group label="Derslik" id="classroom_id" required>
                                    <select name="classroom_id" id="classroom_id" required class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-neutral-900 dark:text-white transition-colors">
                                        @foreach($classrooms as $c)
                                            <option value="{{ $c->id }}" {{ $selectedClassroomId == $c->id ? 'selected' : '' }}>{{ $c->code }} - {{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                </x-admin.form.field-group>

                                <x-admin.form.field-group label="Eğitmen" id="teacher_id" required>
                                    <select name="teacher_id" id="teacher_id" required class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-neutral-900 dark:text-white transition-colors">
                                        @foreach($teachers as $t)
                                            <option value="{{ $t->id }}">{{ $t->user->name }}</option>
                                        @endforeach
                                    </select>
                                </x-admin.form.field-group>

                                <x-admin.form.field-group label="Kurs / Ders" id="course_id" required>
                                    <select name="course_id" id="course_id" required class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-neutral-900 dark:text-white transition-colors">
                                        @foreach($courses as $co)
                                            <option value="{{ $co->id }}">{{ $co->name }}</option>
                                        @endforeach
                                    </select>
                                </x-admin.form.field-group>

                                <x-admin.form.field-group label="Gün" id="day_of_week" required>
                                    <select name="day_of_week" id="day_of_week" required class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-neutral-900 dark:text-white transition-colors">
                                        <option value="1">Pazartesi</option>
                                        <option value="2">Salı</option>
                                        <option value="3">Çarşamba</option>
                                        <option value="4">Perşembe</option>
                                        <option value="5">Cuma</option>
                                        <option value="6">Cumartesi</option>
                                        <option value="7">Pazar</option>
                                    </select>
                                </x-admin.form.field-group>

                                <div class="grid grid-cols-2 gap-4">
                                    <x-admin.form.field-group label="Başlangıç" id="start_time" required>
                                        <input type="time" name="start_time" id="start_time" required value="09:00" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-neutral-900 dark:text-white transition-colors">
                                    </x-admin.form.field-group>
                                    <x-admin.form.field-group label="Bitiş" id="end_time" required>
                                        <input type="time" name="end_time" id="end_time" required value="10:30" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-neutral-900 dark:text-white transition-colors">
                                    </x-admin.form.field-group>
                                </div>
                            </div>

                            <div class="pt-6 mt-6 border-t border-neutral-100 dark:border-neutral-800">
                                <x-admin.button type="submit" variant="primary" icon="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" class="w-full justify-center">
                                    Ekle & Kontrol Et
                                </x-admin.button>
                            </div>

                        </x-admin.form.layout>
                    </div>
                </div>
            </div>

            <!-- Sağ Panel: Derslik Program Listesi / Gridi -->
            <div class="lg:col-span-3">
                <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm overflow-hidden flex flex-col h-full">
                    <div class="p-6 border-b border-neutral-100 dark:border-neutral-800 bg-neutral-50/50 dark:bg-neutral-800/30">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <h3 class="text-sm font-bold text-neutral-900 dark:text-white flex items-center gap-2">
                                <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                Derslik Haftalık Görünümü
                            </h3>
                            <form method="GET" class="flex items-center gap-2">
                                <select name="classroom_id" onchange="this.form.submit()" class="w-full sm:w-auto bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-neutral-900 dark:text-white transition-colors font-medium">
                                    @foreach($classrooms as $c)
                                        <option value="{{ $c->id }}" {{ $selectedClassroomId == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->code }})</option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    </div>

                    <div class="p-6 flex-1 overflow-x-auto">
                        <div class="grid grid-cols-7 gap-3 min-w-[700px]">
                            @php $days = [1 => 'Pzt', 2 => 'Sal', 3 => 'Çar', 4 => 'Per', 5 => 'Cum', 6 => 'Cmt', 7 => 'Paz']; @endphp
                            @foreach($days as $num => $dayName)
                                <div class="text-center font-bold text-[10px] text-neutral-500 dark:text-neutral-400 uppercase tracking-widest py-2 border-b border-neutral-100 dark:border-neutral-800 mb-2">{{ $dayName }}</div>
                            @endforeach

                            @foreach($days as $num => $dayName)
                                <div class="min-h-[300px] bg-neutral-50/50 dark:bg-neutral-800/20 p-2 rounded-xl border border-dashed border-neutral-200 dark:border-neutral-700 space-y-3 flex flex-col">
                                    @php $daySchedules = $schedules->where('day_of_week', $num)->sortBy('start_time'); @endphp
                                    @forelse($daySchedules as $sch)
                                        <div class="p-3 rounded-xl text-white shadow-sm font-medium space-y-2 transition-transform hover:scale-[1.02] border border-white/10 relative group" style="background-color: {{ $sch->color_code }}">
                                            <!-- Aksiyonlar overlay (opsiyonel ileride eklenebilir) -->
                                            <div class="absolute inset-0 bg-black/40 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2 backdrop-blur-sm">
                                                <button type="button" class="p-1.5 bg-white/20 hover:bg-white/40 rounded-lg backdrop-blur text-white transition-colors">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                                </button>
                                                <form action="{{ route('admin.classrooms.schedules.destroy', $sch->id) }}" method="POST" onsubmit="return confirm('Bu dersi programdan silmek istediğinize emin misiniz?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="p-1.5 bg-red-500/80 hover:bg-red-600 rounded-lg backdrop-blur text-white transition-colors">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                    </button>
                                                </form>
                                            </div>

                                            <div class="font-bold text-xs leading-tight line-clamp-2 drop-shadow-sm" title="{{ $sch->course->name }}">{{ $sch->course->name }}</div>
                                            <div class="text-[10px] opacity-90 truncate drop-shadow-sm flex items-center gap-1" title="{{ $sch->teacher->user->name }}">
                                                <div class="w-1.5 h-1.5 rounded-full bg-white/60"></div>
                                                {{ $sch->teacher->user->name }}
                                            </div>
                                            <div class="text-[10px] bg-black/20 rounded-lg px-2 py-1 inline-flex items-center gap-1 font-mono mt-1 backdrop-blur-sm border border-white/10">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                {{ substr($sch->start_time, 0, 5) }} - {{ substr($sch->end_time, 0, 5) }}
                                            </div>
                                        </div>
                                    @empty
                                        <div class="flex-1 flex flex-col items-center justify-center text-center opacity-50 text-neutral-400">
                                            <svg class="w-5 h-5 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            <div class="text-[9px] font-bold uppercase tracking-widest">Boş Slot</div>
                                        </div>
                                    @endforelse
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </x-admin.crud.index-layout>
@endsection
