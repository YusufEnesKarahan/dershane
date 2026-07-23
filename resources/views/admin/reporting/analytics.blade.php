@extends('layouts.admin')
@section('title', 'Kurumsal BI & Analitik Raporları')
@section('content')
    <div class="space-y-6">
        
        <!-- Başlık Bölümü -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <h1 class="text-lg font-bold text-neutral-900 dark:text-white">Dershane BI & Detaylı Analiz Paneli</h1>
            <p class="text-xs text-neutral-500 mt-1">Öğrenci başarısı, ders doluluk oranları ve şubeler arası başarı dağılımlarının derinlemesine analizi.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Panel: Yoklama ve Katılım Analizi -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Yoklama & Katılım Oranları</h3>
                
                <div class="space-y-4">
                    <div>
                        <div class="flex items-center justify-between text-xs font-bold text-neutral-600 mb-1">
                            <span>Genel Katılım Oranı</span>
                            <span class="text-emerald-600">%{{ 100 - $metrics['absence_rate'] }}</span>
                        </div>
                        <div class="w-full bg-neutral-100 dark:bg-neutral-800 h-2 rounded-full overflow-hidden">
                            <div class="bg-emerald-500 h-full" style="width: {{ 100 - $metrics['absence_rate'] }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between text-xs font-bold text-neutral-600 mb-1">
                            <span>Devamsızlık Oranı</span>
                            <span class="text-rose-600">%{{ $metrics['absence_rate'] }}</span>
                        </div>
                        <div class="w-full bg-neutral-100 dark:bg-neutral-800 h-2 rounded-full overflow-hidden">
                            <div class="bg-rose-500 h-full" style="width: {{ $metrics['absence_rate'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Orta Panel: Akademik Başarı Netleri (TYT / AYT) -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Sınav Başarı Net Grafiği</h3>
                
                <div class="space-y-4">
                    <div>
                        <div class="flex items-center justify-between text-xs font-bold text-neutral-600 mb-1">
                            <span>Ortalama TYT Neti (120 Soru)</span>
                            <span class="text-indigo-600 font-mono">{{ $metrics['avg_tyt_net'] }} Net</span>
                        </div>
                        <div class="w-full bg-neutral-100 dark:bg-neutral-800 h-2 rounded-full overflow-hidden">
                            @php
                                $tytPercent = $metrics['avg_tyt_net'] > 0 ? ($metrics['avg_tyt_net'] / 120) * 100 : 0;
                            @endphp
                            <div class="bg-indigo-500 h-full" style="width: {{ $tytPercent }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between text-xs font-bold text-neutral-600 mb-1">
                            <span>Ortalama AYT Neti (80 Soru)</span>
                            <span class="text-amber-600 font-mono">{{ $metrics['avg_ayt_net'] }} Net</span>
                        </div>
                        <div class="w-full bg-neutral-100 dark:bg-neutral-800 h-2 rounded-full overflow-hidden">
                            @php
                                $aytPercent = $metrics['avg_ayt_net'] > 0 ? ($metrics['avg_ayt_net'] / 80) * 100 : 0;
                            @endphp
                            <div class="bg-amber-500 h-full" style="width: {{ $aytPercent }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sağ Panel: Finansal Tahsilat & Borç Analizi -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Tahsilat / Alacak Analizi</h3>
                
                <div class="space-y-4">
                    <div>
                        <div class="flex items-center justify-between text-xs font-bold text-neutral-600 mb-1">
                            <span>Tahsilat Oranı</span>
                            @php
                                $totalFinance = $metrics['total_collected'] + $metrics['pending_debt'];
                                $financePercent = $totalFinance > 0 ? round(($metrics['total_collected'] / $totalFinance) * 100, 1) : 0.0;
                            @endphp
                            <span class="text-emerald-600">%{{ $financePercent }}</span>
                        </div>
                        <div class="w-full bg-neutral-100 dark:bg-neutral-800 h-2 rounded-full overflow-hidden">
                            <div class="bg-emerald-500 h-full" style="width: {{ $financePercent }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between text-xs font-bold text-neutral-600 mb-1">
                            <span>Bekleyen Alacak Oranı</span>
                            <span class="text-rose-600">%{{ 100 - $financePercent }}</span>
                        </div>
                        <div class="w-full bg-neutral-100 dark:bg-neutral-800 h-2 rounded-full overflow-hidden">
                            <div class="bg-rose-500 h-full" style="width: {{ 100 - $financePercent }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection
