@extends('layouts.admin')

@section('title', 'Akademik Gelişim Analizi: ' . $student->full_name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-black text-xl shadow-sm">
                {{ mb_substr($student->first_name, 0, 1) }}{{ mb_substr($student->last_name, 0, 1) }}
            </div>
            <div>
                <h1 class="text-2xl font-black text-neutral-900 dark:text-white">{{ $student->full_name }} — Akademik Gelişim Paneli</h1>
                <div class="flex items-center gap-3 text-xs text-neutral-500 font-mono mt-1">
                    <span>No: {{ $student->student_number }}</span>
                    <span>•</span>
                    <span>Sınıf: {{ $student->classroom?->name ?? 'Atanmadı' }}</span>
                    <span>•</span>
                    <span>Şube: {{ $student->branch?->name ?? 'Kadıköy' }}</span>
                </div>
            </div>
        </div>
        <div class="flex gap-2">
            <button type="button" onclick="window.print()" class="px-4 py-2 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 text-neutral-700 dark:text-neutral-300 rounded-xl text-xs font-bold shadow-sm hover:bg-neutral-50">
                <i class="fas fa-file-pdf text-rose-500 mr-1.5"></i> PDF Rapor Al
            </button>
            <a href="{{ route('admin.students.show', $student->id) }}" class="px-4 py-2 bg-neutral-900 text-white rounded-xl text-xs font-bold shadow-sm hover:bg-neutral-800">
                Öğrenci Profiline Dön
            </a>
        </div>
    </div>

    <!-- Karşılaştırma Kartları -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-neutral-900 p-5 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm">
            <div class="text-xs font-bold text-neutral-400 uppercase tracking-wider">Öğrenci Net Ortalaması</div>
            <div class="text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-2">{{ number_format($comparisons['student_avg'], 2) }} Net</div>
            <div class="text-[11px] text-neutral-500 mt-1">Son girdiği sınavlar baz alındı</div>
        </div>

        <div class="bg-white dark:bg-neutral-900 p-5 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm">
            <div class="text-xs font-bold text-neutral-400 uppercase tracking-wider">Sınıf Ortalaması</div>
            <div class="text-2xl font-black text-neutral-800 dark:text-neutral-200 mt-2">{{ number_format($comparisons['class_avg'], 2) }} Net</div>
            <div class="text-[11px] text-neutral-500 mt-1">{{ $student->classroom?->name ?? 'Sınıf' }} geneli</div>
        </div>

        <div class="bg-white dark:bg-neutral-900 p-5 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm">
            <div class="text-xs font-bold text-neutral-400 uppercase tracking-wider">Şube Ortalaması</div>
            <div class="text-2xl font-black text-neutral-800 dark:text-neutral-200 mt-2">{{ number_format($comparisons['branch_avg'], 2) }} Net</div>
            <div class="text-[11px] text-neutral-500 mt-1">{{ $student->branch?->name ?? 'Şube' }} geneli</div>
        </div>

        <div class="bg-white dark:bg-neutral-900 p-5 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm">
            <div class="text-xs font-bold text-neutral-400 uppercase tracking-wider">Kurum Genel Ortalaması</div>
            <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-2">{{ number_format($comparisons['institution_avg'], 2) }} Net</div>
            <div class="text-[11px] text-neutral-500 mt-1">Tüm şubeler toplamı</div>
        </div>
    </div>

    <!-- Gelişim Analizi & Çalışma Programı -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sınav Net Gelişim Trendi -->
        <div class="lg:col-span-2 bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-neutral-900 dark:text-white flex items-center justify-between border-b border-neutral-100 dark:border-neutral-800 pb-3">
                <span class="flex items-center gap-2">
                    <i class="fas fa-chart-line text-indigo-500"></i> Deneme Sınavları Net Gelişim Grafiği
                </span>
                <span class="text-xs font-mono text-neutral-500">Toplam {{ $netGrowth['total_exams'] }} Deneme</span>
            </h3>

            @if(!empty($netGrowth['labels']))
                <div class="space-y-4">
                    <div class="p-4 bg-indigo-50/50 dark:bg-indigo-900/20 rounded-xl border border-indigo-100 dark:border-indigo-800/40">
                        <div class="flex justify-between text-xs font-bold text-indigo-900 dark:text-indigo-300 mb-2">
                            <span>Net Trendi (Girdi Sınavlar)</span>
                            <span>Son Net: {{ number_format($netGrowth['latest_net'], 2) }}</span>
                        </div>
                        <div class="flex items-end gap-3 h-40 pt-4 border-b border-indigo-200 dark:border-indigo-800">
                            @foreach($netGrowth['net_series'] as $idx => $net)
                                @php
                                    $maxNet = max(max($netGrowth['net_series']), 1);
                                    $heightPct = min(round(($net / $maxNet) * 100), 100);
                                @endphp
                                <div class="flex-1 flex flex-col items-center gap-1 group relative">
                                    <div class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 group-hover:scale-110 transition-transform">{{ $net }}</div>
                                    <div class="w-full bg-indigo-500 rounded-t-md transition-all duration-500" style="height: {{ max($heightPct, 8) }}%"></div>
                                    <span class="text-[9px] text-neutral-500 truncate w-full text-center mt-1">{{ Str::limit($netGrowth['labels'][$idx], 8) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <div class="p-8 text-center text-sm text-neutral-500 italic">
                    Öğrenciye ait henüz girilmiş bir deneme sınavı sonucu bulunamadı.
                </div>
            @endif
        </div>

        <!-- Haftalık Çalışma Programı & Görev İlerlemesi -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-neutral-900 dark:text-white flex items-center justify-between border-b border-neutral-100 dark:border-neutral-800 pb-3">
                <span class="flex items-center gap-2">
                    <i class="fas fa-tasks text-emerald-500"></i> Haftalık Çalışma Programı
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                    %{{ $studyProgramSummary['progress_percentage'] }} Tamamlandı
                </span>
            </h3>

            <div class="space-y-2">
                <div class="w-full bg-neutral-200 dark:bg-neutral-700 rounded-full h-3 overflow-hidden">
                    <div class="bg-emerald-500 h-3 rounded-full transition-all duration-500" style="width: {{ $studyProgramSummary['progress_percentage'] }}%"></div>
                </div>
                <div class="flex justify-between text-xs text-neutral-500 pt-1">
                    <span>Toplam {{ $studyProgramSummary['total_tasks'] }} Görev</span>
                    <span>{{ $studyProgramSummary['completed_tasks'] }} Bitti • {{ $studyProgramSummary['in_progress_tasks'] }} Sürüyor</span>
                </div>
            </div>

            <div class="divide-y divide-neutral-100 dark:divide-neutral-800 pt-2">
                @forelse($studyProgramSummary['schedules'] as $sch)
                    @php
                        $sub = $sch->submissions->first();
                        $status = $sub?->task_status ?? 'Not Started';
                    @endphp
                    <div class="py-3 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-neutral-900 dark:text-white">{{ $sch->title }}</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold 
                                {{ $status === 'Completed' ? 'bg-emerald-100 text-emerald-700' : ($status === 'In Progress' ? 'bg-amber-100 text-amber-700' : 'bg-neutral-100 text-neutral-600') }}
                            ">
                                {{ $status === 'Completed' ? 'Tamamlandı' : ($status === 'In Progress' ? 'Devam Ediyor' : 'Başlanmadı') }}
                            </span>
                        </div>
                        <div class="text-[11px] text-neutral-500 flex items-center gap-2">
                            <span><i class="fas fa-book mr-1"></i> {{ $sch->source_book ?: 'Ders Kitabı' }}</span>
                            @if($sch->page_range)
                                <span>(S. {{ $sch->page_range }})</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-xs text-neutral-400 italic">
                        Aktif haftalık çalışma programı atanmadı.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
