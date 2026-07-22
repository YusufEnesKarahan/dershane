@extends('layouts.admin')
@section('title', 'Başarı & Performans Analizi')
@section('content')
    <div class="space-y-6">
        
        <!-- Üst Başlık -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm">
            <h1 class="text-lg font-bold text-neutral-900 dark:text-white">Başarı & Performans Raporum</h1>
            <p class="text-xs text-neutral-500 mt-1">Öğrencilerinizin deneme sınavı ortalamaları ve yönetim tarafından girilen performans değerlendirmeleriniz.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Sol Panel: Öğretmen Performans Değerlendirmeleri -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Performans Kayıtlarım</h3>
                
                <x-admin.table.layout>
                    <x-slot name="head">
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Kriter</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Skor</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Yorum</th>
                    </x-slot>
                    <x-slot name="body">
                        @forelse($performanceLogs as $log)
                            <tr>
                                <td class="px-4 py-3 text-xs font-bold text-neutral-900">{{ $log->metric_type }}</td>
                                <td class="px-4 py-3 text-xs font-bold text-primary font-mono">{{ $log->score }} / 100</td>
                                <td class="px-4 py-3 text-xs text-neutral-600">{{ $log->comments ?? 'Geri bildirim yok' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-6 text-center text-xs text-neutral-400">Henüz performans değerlendirmesi bulunmamaktadır.</td>
                            </tr>
                        @endforelse
                    </x-slot>
                </x-admin.table.layout>
            </div>

            <!-- Sağ Panel: Ders/Kurs Başarı Ortalamaları -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Sınıf/Kurs Başarı Dağılımı</h3>
                
                <div class="p-6 bg-neutral-50 rounded-2xl space-y-4">
                    <div>
                        <div class="flex items-center justify-between text-xs font-bold text-neutral-600 mb-1">
                            <span>TYT Matematik Kursu Ortalaması</span>
                            <span class="text-primary">%88.5 Başarı</span>
                        </div>
                        <div class="w-full bg-neutral-200 h-2 rounded-full overflow-hidden">
                            <div class="bg-primary h-full" style="width: 88.5%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between text-xs font-bold text-neutral-600 mb-1">
                            <span>AYT Analitik Geometri Ortalaması</span>
                            <span class="text-green-600">%74.2 Başarı</span>
                        </div>
                        <div class="w-full bg-neutral-200 h-2 rounded-full overflow-hidden">
                            <div class="bg-green-600 h-full" style="width: 74.2%"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection
