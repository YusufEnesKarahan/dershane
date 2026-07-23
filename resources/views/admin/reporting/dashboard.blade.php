@extends('layouts.admin')
@section('title', 'Yönetici Özet Paneli (Executive Dashboard)')
@section('content')
    <div class="space-y-6">
        
        <!-- Üst Karşılama ve Hızlı Aksiyon -->
        <div class="bg-gradient-to-r from-indigo-900 to-slate-900 p-8 rounded-3xl text-white shadow-premium flex flex-col md:flex-row md:items-center md:justify-between gap-6 border border-indigo-950">
            <div>
                <span class="px-2.5 py-1 text-[10px] uppercase font-bold tracking-widest bg-indigo-500/20 text-indigo-300 rounded-full border border-indigo-500/30">Merkezi Sistem Analitiği</span>
                <h1 class="text-2xl font-black mt-2">Executive BI & Yönetim Paneli</h1>
                <p class="text-xs text-slate-300 mt-1">Öğrenci, öğretmen, ders, yoklama, sınav başarı durumu, finansal tahsilat ve iletişim kanallarının canlı durumu.</p>
            </div>
            
            <div class="flex items-center gap-2">
                <form method="POST" action="{{ route('admin.reporting.snapshot') }}">
                    @csrf
                    <button type="submit" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-xs font-bold rounded-xl transition shadow-lg shadow-indigo-900/50 flex items-center gap-1.5 border border-indigo-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Anlık Durum Kaydet (Snapshot)
                    </button>
                </form>
                <a href="{{ route('admin.reporting.analytics') }}" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-xs font-bold rounded-xl transition border border-slate-700">
                    BI Analitiği Raporları
                </a>
            </div>
        </div>

        <!-- Ana Metrikler Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <!-- Öğrenci & Öğretmen KPI -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-neutral-400 uppercase">Akademik Kadro</span>
                    <div class="p-2 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                </div>
                <div>
                    <div class="text-3xl font-black text-neutral-900 dark:text-white">{{ $metrics['student_count'] }} / {{ $metrics['teacher_count'] }}</div>
                    <p class="text-[11px] text-neutral-500 mt-1">Toplam Öğrenci / Öğretmen Sayısı</p>
                </div>
                <div class="text-[10px] text-indigo-600 font-semibold bg-indigo-50 dark:bg-indigo-950/20 px-2 py-1 rounded w-fit">
                    Şube Sayısı: {{ $metrics['branch_count'] }}
                </div>
            </div>

            <!-- Günlük Ders & Yoklama KPI -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-neutral-400 uppercase">Bugünkü Ders Akışı</span>
                    <div class="p-2 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                </div>
                <div>
                    <div class="text-3xl font-black text-neutral-900 dark:text-white">{{ $metrics['today_lessons'] }} Ders</div>
                    <p class="text-[11px] text-neutral-500 mt-1">Bugün Tanımlanmış Aktif Ders Blokları</p>
                </div>
                <div class="text-[10px] text-emerald-600 font-semibold bg-emerald-50 dark:bg-emerald-950/20 px-2 py-1 rounded w-fit">
                    Yoklanan Oturum: {{ $metrics['today_attendance_sessions'] }}
                </div>
            </div>

            <!-- Devamsızlık & Akademik Başarı KPI -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-neutral-400 uppercase">Akademik Seviye</span>
                    <div class="p-2 bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                </div>
                <div>
                    <div class="text-2xl font-black text-neutral-900 dark:text-white">{{ $metrics['avg_tyt_net'] }} / {{ $metrics['avg_ayt_net'] }} Net</div>
                    <p class="text-[11px] text-neutral-500 mt-1">Ortalama TYT / AYT Başarı Netleri</p>
                </div>
                <div class="text-[10px] text-amber-600 font-semibold bg-amber-50 dark:bg-amber-950/20 px-2 py-1 rounded w-fit">
                    Devamsızlık Oranı: %{{ $metrics['absence_rate'] }}
                </div>
            </div>

            <!-- Finansal Tahsilat KPI -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-neutral-400 uppercase">Finansal Durum</span>
                    <div class="p-2 bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div>
                    <div class="text-2xl font-black text-neutral-900 dark:text-white">{{ number_format($metrics['total_collected'], 2) }} ₺</div>
                    <p class="text-[11px] text-neutral-500 mt-1">Toplam Alınan/Tahsil Edilen Tutar</p>
                </div>
                <div class="text-[10px] text-rose-600 font-semibold bg-rose-50 dark:bg-rose-950/20 px-2 py-1 rounded w-fit">
                    Bekleyen Borç: {{ number_format($metrics['pending_debt'], 2) }} ₺
                </div>
            </div>

        </div>

        <!-- Ek Veriler ve Grafik Görünümleri -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Ödev & İletişim Detayları -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Ek Göstergeler & İletişim</h3>
                
                <div class="space-y-4 pt-2">
                    <div class="flex items-center justify-between border-b border-neutral-100 dark:border-neutral-800 pb-2">
                        <span class="text-xs text-neutral-500">Teslim Edilen Ödev Sayısı</span>
                        <span class="text-xs font-bold text-neutral-900 dark:text-white font-mono">{{ $metrics['total_submissions'] }} Adet</span>
                    </div>

                    <div class="flex items-center justify-between border-b border-neutral-100 dark:border-neutral-800 pb-2">
                        <span class="text-xs text-neutral-500">Gönderilen Toplam Bildirim</span>
                        <span class="text-xs font-bold text-neutral-900 dark:text-white font-mono">{{ $metrics['total_notifications'] }} Adet</span>
                    </div>

                    <div class="flex items-center justify-between pb-2">
                        <span class="text-xs text-neutral-500">Metrik Güncellenme Zamanı</span>
                        <span class="text-[10px] text-indigo-500 font-bold font-mono">{{ $metrics['calculated_at'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Dashboard Snapshot Açıklaması -->
            <div class="lg:col-span-2 bg-gradient-to-br from-slate-900 to-indigo-950 p-6 rounded-2xl text-white shadow-premium space-y-4 flex flex-col justify-between">
                <div>
                    <h3 class="text-sm font-bold text-indigo-300">Yönetim Analizi ve Raporlama</h3>
                    <p class="text-xs text-slate-300 leading-relaxed mt-2">
                        Bu sistem, Dershane platformundaki tüm aktif eğitim modüllerini (Öğrenci, Yoklama, Sınav Sonuçları, Ödev Raporları, Mali İşler, Veli Bilgilendirmeleri) gerçek zamanlı olarak izler. Veriler önbelleğe alınarak sistem yükü azaltılır ve anlık dashboard snapshot kayıtlarıyla BI tarihsel raporları oluşturulabilir.
                    </p>
                </div>
                <div class="pt-4 border-t border-indigo-900/60 flex items-center justify-between">
                    <span class="text-[10px] text-slate-400">BI Analiz Versiyonu: v1.0.0</span>
                    <a href="{{ route('admin.reporting.reports') }}" class="text-xs font-bold text-indigo-400 hover:underline">Planlı Raporları Yönet &rarr;</a>
                </div>
            </div>

        </div>

    </div>
@endsection
