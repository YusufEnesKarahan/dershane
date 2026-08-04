@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-onboarding.stepper :currentStep="2" :progress="$progress" />

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h3 class="text-lg font-bold text-slate-900 mb-1">Adım 2: Akademik Yıl & Çalışma Dönemi</h3>
        <p class="text-xs text-slate-500 mb-6">Kurumunuzun aktif eğitim-öğretim yılını ve tarih aralıklarını belirleyin.</p>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.onboarding.saveAcademicYear') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Dönem Adı</label>
                    <input type="text" name="name" required value="{{ old('name', $term ? $term->name : '2026-2027 Eğitim Öğretim Yılı') }}" placeholder="Örn: 2026-2027 Dönemi" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Dönem Başlangıç Tarihi</label>
                    <input type="date" name="start_date" required value="{{ old('start_date', $term && $term->start_date ? $term->start_date->format('Y-m-d') : date('Y-09-01')) }}" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Dönem Bitiş Tarihi</label>
                    <input type="date" name="end_date" required value="{{ old('end_date', $term && $term->end_date ? $term->end_date->format('Y-m-d') : date('Y-06-30', strtotime('+1 year'))) }}" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <a href="{{ route('admin.onboarding.profile') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-sm rounded-xl">
                    ← Geri
                </a>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl transition-all shadow-sm">
                    Kaydet ve Devam Et →
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
