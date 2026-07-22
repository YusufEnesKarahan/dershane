@extends('layouts.admin')
@section('title', 'Öğretmen Bilgi Sistemi')
@section('content')
    <div class="space-y-6">
        
        <!-- Üst Karşılama Kartı -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-neutral-900 dark:text-white">Hoş Geldiniz, {{ $teacher->user->name }}</h1>
                <p class="text-xs text-neutral-500 mt-1">Portalınız üzerinden ders atamalarını, haftalık programınızı, öğrenci yoklamalarını ve ödev süreçlerini yönetebilirsiniz.</p>
            </div>
            
            <div class="flex items-center gap-2">
                <a href="{{ route('teacher.classes') }}" class="px-3 py-2 bg-neutral-100 dark:bg-neutral-800 text-xs font-semibold rounded-xl text-neutral-800 dark:text-neutral-200 hover:bg-neutral-200 transition">
                    Sınıflarım & Derslerim
                </a>
                <a href="{{ route('teacher.attendance') }}" class="px-3 py-2 bg-neutral-100 dark:bg-neutral-800 text-xs font-semibold rounded-xl text-neutral-800 dark:text-neutral-200 hover:bg-neutral-200 transition">
                    Yoklama Girişi
                </a>
                <a href="{{ route('teacher.homework') }}" class="px-3 py-2 bg-neutral-100 dark:bg-neutral-800 text-xs font-semibold rounded-xl text-neutral-800 dark:text-neutral-200 hover:bg-neutral-200 transition">
                    Ödev Yönetimi
                </a>
                <a href="{{ route('teacher.analytics') }}" class="px-3 py-2 bg-neutral-100 dark:bg-neutral-800 text-xs font-semibold rounded-xl text-neutral-800 dark:text-neutral-200 hover:bg-neutral-200 transition">
                    Performans & Başarı Analizi
                </a>
            </div>
        </div>

        <!-- İstatistik Özetleri -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Aktif Sınıflar</h4>
                <div class="text-2xl font-bold text-primary">{{ $assignedClasses->count() }} Sınıf</div>
            </div>
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Haftalık Ders Saati</h4>
                <div class="text-2xl font-bold text-green-600">{{ $schedules->count() }} Ders</div>
            </div>
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Ortalama Performans Skoru</h4>
                <div class="text-2xl font-bold text-amber-600">%{{ $analytics['average_performance_score'] }}</div>
            </div>
        </div>

        <!-- Ders Programı & Takvim -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm space-y-4">
            <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Ders Programım & Günlük Takvim</h3>
            
            <x-admin.table.layout>
                <x-slot name="head">
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Tarih</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Saat Aralığı</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Sınıf</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Ders</th>
                </x-slot>
                <x-slot name="body">
                    @forelse($schedules as $sch)
                        <tr>
                            <td class="px-4 py-3 text-xs font-bold text-neutral-900 font-mono">{{ $sch->date }}</td>
                            <td class="px-4 py-3 text-xs text-neutral-600 font-mono">{{ $sch->start_time }} - {{ $sch->end_time }}</td>
                            <td class="px-4 py-3 text-xs font-semibold text-neutral-800">{{ $sch->classroom->name ?? 'Derslik Yok' }}</td>
                            <td class="px-4 py-3 text-xs font-semibold text-primary">{{ $sch->course->name ?? 'Genel Ders' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-xs text-neutral-400">Yakın zamanda planlanmış dersiniz bulunmamaktadır.</td>
                        </tr>
                    @endforelse
                </x-slot>
            </x-admin.table.layout>
        </div>

    </div>
@endsection
