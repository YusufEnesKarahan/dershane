@extends('layouts.admin')
@section('title', 'İnsan Kaynakları Paneli')
@section('content')
    <div class="space-y-6">
        
        <!-- Üst Banner -->
        <div class="bg-gradient-to-r from-violet-900 to-slate-900 p-8 rounded-3xl text-white shadow-premium flex flex-col md:flex-row md:items-center md:justify-between gap-6 border border-violet-950">
            <div>
                <span class="px-2.5 py-1 text-[10px] uppercase font-bold tracking-widest bg-violet-500/20 text-violet-300 rounded-full border border-violet-500/30 font-mono">HR Suite</span>
                <h1 class="text-2xl font-black mt-2">İnsan Kaynakları & Bordro Yönetimi</h1>
                <p class="text-xs text-violet-100 mt-1">Kurum personel durumlarını, giriş-çıkış takibini, izin isteklerini, masrafları ve maaş bordrolarını yönetin.</p>
            </div>
            
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.employees.index') }}" class="px-4 py-2.5 bg-violet-600 hover:bg-violet-700 text-xs font-bold rounded-xl transition shadow-lg shadow-violet-950 flex items-center gap-1.5 border border-violet-500">
                    Personel Listesi
                </a>
                <a href="{{ route('admin.hr.analytics') }}" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-xs font-bold rounded-xl transition border border-slate-700">
                    İK Analitiği
                </a>
            </div>
        </div>

        <!-- KPI Kartları -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-neutral-400 uppercase">Toplam Personel</span>
                    <div class="p-2 bg-indigo-50 dark:bg-indigo-950 text-indigo-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                </div>
                <div>
                    <div class="text-3xl font-black text-neutral-900 dark:text-white font-mono">{{ $analytics['total_employees'] }} / {{ $analytics['active_employees'] }}</div>
                    <p class="text-[11px] text-neutral-500 mt-1">Toplam / Aktif Personel Sayısı</p>
                </div>
            </div>

            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-neutral-400 uppercase">Bugün Gelen / İzindeki</span>
                    <div class="p-2 bg-green-50 dark:bg-green-950 text-green-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div>
                    <div class="text-3xl font-black text-neutral-900 dark:text-white font-mono">{{ $analytics['checked_in_today'] }} / {{ $analytics['on_leave_today'] }}</div>
                    <p class="text-[11px] text-neutral-500 mt-1">Mevcut Çalışan / İzinli Personel</p>
                </div>
            </div>

            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-neutral-400 uppercase">Aylık Maaş Toplamı</span>
                    <div class="p-2 bg-emerald-50 dark:bg-emerald-950 text-emerald-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div>
                    <div class="text-3xl font-black text-neutral-900 dark:text-white font-mono">₺{{ number_format($analytics['monthly_salary_sum'], 2) }}</div>
                    <p class="text-[11px] text-neutral-500 mt-1">Aktif çalışanların aylık maaş gideri</p>
                </div>
            </div>

            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-neutral-400 uppercase">Değerlendirme Puanı</span>
                    <div class="p-2 bg-yellow-50 dark:bg-yellow-950 text-yellow-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.9 1.397-.9 1.697 0l1.518 4.674a1 1 0 00.95.69h4.907c.961 0 1.367 1.223.593 1.832l-3.978 2.893a1 1 0 00-.363 1.118l1.518 4.674c.3.9-.755 1.688-1.538 1.118l-3.978-2.893a1 1 0 00-1.17 0l-3.978 2.893c-.783.57-1.838-.218-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.978-2.893c-.773-.609-.368-1.832.593-1.832h4.907a1 1 0 00.95-.69l1.518-4.674z"></path></svg>
                    </div>
                </div>
                <div>
                    <div class="text-3xl font-black text-neutral-900 dark:text-white font-mono">{{ $analytics['performance_avg'] }} / 100</div>
                    <p class="text-[11px] text-neutral-500 mt-1">Personel genel performans ortalaması</p>
                </div>
            </div>

        </div>

        <!-- Onay Bekleyenler & Grafik Dağılımları -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Panel: Bekleyen Talepler -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Bekleyen İK Süreçleri</h3>
                
                <div class="space-y-3">
                    <div class="p-4 bg-amber-50 dark:bg-amber-950/20 border border-amber-100 dark:border-amber-900/50 rounded-xl flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-amber-800 dark:text-amber-300">Bekleyen İzinler</span>
                            <p class="text-[10px] text-amber-600 dark:text-amber-400 mt-0.5">Onay bekleyen yıllık/mazeret izinleri</p>
                        </div>
                        <span class="text-xl font-bold font-mono text-amber-800 dark:text-amber-300">{{ $analytics['pending_leaves'] }}</span>
                    </div>

                    <div class="p-4 bg-blue-50 dark:bg-blue-950/20 border border-blue-100 dark:border-blue-900/50 rounded-xl flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-blue-800 dark:text-blue-300">Bekleyen Masraf Talepleri</span>
                            <p class="text-[10px] text-blue-600 dark:text-blue-400 mt-0.5">Onay sırasındaki masraf fişleri</p>
                        </div>
                        <span class="text-xl font-bold font-mono text-blue-800 dark:text-blue-300">{{ $analytics['pending_expenses'] }}</span>
                    </div>

                    <div class="p-4 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/50 rounded-xl flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-emerald-800 dark:text-emerald-300">Bekleyen Avans İstekleri</span>
                            <p class="text-[10px] text-emerald-600 dark:text-emerald-400 mt-0.5">Avans talepleri onay kuyruğu</p>
                        </div>
                        <span class="text-xl font-bold font-mono text-emerald-800 dark:text-emerald-300">{{ $analytics['pending_advances'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Sağ Panel: Departman Dağılımları -->
            <div class="lg:col-span-2 bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Departman Bazlı Dağılım</h3>
                
                <div class="space-y-4">
                    @forelse($analytics['department_distribution'] as $dept)
                        <div class="space-y-1">
                            <div class="flex justify-between items-center text-xs font-bold">
                                <span class="text-neutral-700 dark:text-neutral-300">{{ $dept['name'] }}</span>
                                <span class="text-neutral-500 font-mono">{{ $dept['count'] }} Kişi</span>
                            </div>
                            <div class="w-full bg-neutral-100 dark:bg-neutral-800 h-2 rounded-full overflow-hidden">
                                <div class="bg-violet-600 h-full rounded-full" style="width: {{ $analytics['total_employees'] > 0 ? ($dept['count'] / $analytics['total_employees']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-xs text-neutral-400 py-6">Departman verisi bulunmamaktadır.</div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>
@endsection
