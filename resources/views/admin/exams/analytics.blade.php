@extends('layouts.admin')
@section('title', 'Sınav Başarı Analitiği')
@section('content')
    <x-admin.crud.index-layout title="Sınav & Ölçme Değerlendirme Analitiği" description="Kurum geneli net ortalamalarını, zirvedeki öğrencileri ve şube başarı sıralamalarını inceleyin.">
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Toplam Sınav</h4>
                <div class="text-2xl font-bold text-slate-900 dark:text-white">{{ $summary['total_exams'] }} Sınav</div>
            </div>
            <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Değerlendirilen Sonuç</h4>
                <div class="text-2xl font-bold text-primary">{{ $summary['total_results'] }} Kağıt</div>
            </div>
            <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Kurum Net Ortalaması</h4>
                <div class="text-2xl font-bold text-emerald-600">{{ $summary['avg_net'] }} Net</div>
            </div>
            <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">En Yüksek Puan</h4>
                <div class="text-2xl font-bold text-amber-600 dark:text-amber-500">{{ $summary['top_score'] }} Puan</div>
            </div>
        </div>

    </x-admin.crud.index-layout>
@endsection
