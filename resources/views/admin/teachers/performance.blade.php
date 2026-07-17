@extends('layouts.admin')
@section('title', 'Performans Ölçümleri')
@section('content')
    <x-admin.crud.index-layout title="Eğitmen Performans İzleme" description="KPI kriterlerine, veli geri bildirimlerine ve ders tamamlama oranlarına göre eğitmenlerinizin performans grafiklerini izleyin.">
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Panel: Yeni Performans KPI Ekle -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white mb-4">Yeni KPI Değerlendirmesi Gir</h3>
                <x-admin.form.layout :action="route('admin.teachers.performance.store')" method="POST">
                    <x-admin.form.field-group label="Eğitmen" id="teacher_id">
                        <select name="teacher_id" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2">
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}">{{ $t->user->name }}</option>
                            @endforeach
                        </select>
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Derse Katılım Oranı (%)" id="attendance_rate">
                        <input type="number" step="0.01" name="attendance_rate" required value="100.00" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2">
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Öğrenci Memnuniyet Skoru (1.0 - 5.0)" id="student_satisfaction">
                        <input type="number" step="0.1" name="student_satisfaction" required value="5.0" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2">
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Toplam İşlenen Ders Sayısı" id="lesson_count">
                        <input type="number" name="lesson_count" value="40" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2">
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Değerlendirme Dönemi (Örn: 2026-07)" id="kpi_month">
                        <input type="text" name="kpi_month" value="{{ date('Y-m') }}" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2">
                    </x-admin.form.field-group>

                    <div class="pt-4">
                        <button type="submit" class="w-full px-4 py-2 bg-primary text-white text-xs font-semibold rounded-lg hover:bg-primary-dark transition shadow-sm">
                            Skorları Logla
                        </button>
                    </div>
                </x-admin.form.layout>
            </div>

            <!-- Sağ Panel: Performans Listesi -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                    <div class="flex items-center justify-between border-b pb-4">
                        <h3 class="text-sm font-bold text-neutral-900 dark:text-white">KPI Değerlendirme Geçmişi</h3>
                        <form action="{{ route('admin.teachers.performance.index') }}" method="GET" class="flex gap-2">
                            <select name="teacher_id" onchange="this.form.submit()" class="text-xs bg-neutral-50 border rounded-lg p-1.5">
                                <option value="">Eğitmen Seçiniz</option>
                                @foreach($teachers as $t)
                                    <option value="{{ $t->id }}" {{ (isset($teacher) && $teacher->id === $t->id) ? 'selected' : '' }}>{{ $t->user->name }}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>

                    <div class="divide-y divide-neutral-100">
                        @forelse($performances as $perf)
                            <div class="py-3 flex items-center justify-between text-xs">
                                <div>
                                    <span class="font-semibold text-neutral-800">Dönem: {{ $perf->kpi_month }}</span>
                                    <div class="text-[10px] text-neutral-400 mt-0.5">Katılım Oranı: %{{ $perf->attendance_rate }} | Ders Sayısı: {{ $perf->lesson_count }}</div>
                                </div>
                                <span class="px-2.5 py-1 text-[10px] font-bold bg-primary-light text-primary rounded-full">
                                    ★ {{ $perf->student_satisfaction }} / 5.0
                                </span>
                            </div>
                        @empty
                            <div class="text-center text-neutral-400 text-xs py-8">Lütfen KPI detayları listelemek için yukarıdan eğitmen seçin.</div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </x-admin.crud.index-layout>
@endsection
