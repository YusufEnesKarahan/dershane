@extends('layouts.admin')
@section('title', 'Öğrenci Analizleri')
@section('content')
    <x-admin.crud.index-layout title="Öğrenci İstatistik & Dağılım Raporları" description="Aktif öğrenci hacmini, mezuniyet oranlarını ve kurs kaydı yoğunluklarını takip edin.">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Toplam Kayıtlı Öğrenci</h4>
                <div class="text-2xl font-bold">{{ $summary['total_students'] }} Öğrenci</div>
            </div>
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Aktif Devam Eden</h4>
                <div class="text-2xl font-bold text-green-600">{{ $summary['active_students'] }} Öğrenci</div>
            </div>
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Mezun Başarısı</h4>
                <div class="text-2xl font-bold text-blue-600">{{ $summary['graduated_students'] }} Öğrenci</div>
            </div>
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Toplam Kurs Kaydı</h4>
                <div class="text-2xl font-bold">{{ $summary['total_enrollments'] }} Kayıt</div>
            </div>
        </div>
    </x-admin.crud.index-layout>
@endsection
