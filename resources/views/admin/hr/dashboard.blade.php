@extends('layouts.admin')
@section('title', 'İnsan Kaynakları Paneli')
@section('content')
    <div class="space-y-6">
        
        <!-- Üst Banner -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-2xl shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex flex-col">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] uppercase font-bold tracking-widest bg-blue-50 dark:bg-blue-950/30 text-blue-600 dark:text-blue-400 rounded-full border border-blue-200 dark:border-blue-800 mb-2 w-max">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                    HR Suite
                </span>
                <h1 class="text-xl font-semibold text-slate-900 dark:text-slate-100 flex items-center gap-2 tracking-tight">İnsan Kaynakları & Bordro Yönetimi</h1>
                <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400 font-medium max-w-xl">Kurum personel durumlarını, giriş-çıkış takibini, izin isteklerini, masrafları ve maaş bordrolarını yönetin.</p>
            </div>
            
            <div class="mt-4 sm:mt-0 flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.employees.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    Personel Listesi
                </a>
                <a href="{{ route('admin.hr.analytics') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-medium rounded-lg transition-colors border border-slate-200 dark:border-slate-700">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                    İK Analitiği
                </a>
                </a>
            </div>
        </div>

        <!-- KPI Kartları -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm relative overflow-hidden group hover:border-blue-500/30 transition-colors">
                <div class="absolute top-0 right-0 w-24 h-24 bg-blue-500/5 dark:bg-blue-500/10 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                <div class="relative z-10 flex items-start justify-between">
                    <div>
                        <h4 class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                            Toplam Personel
                        </h4>
                        <div class="text-3xl font-black text-slate-900 dark:text-white flex items-baseline gap-1 font-mono">
                            {{ $analytics['total_employees'] }} <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">/ {{ $analytics['active_employees'] }}</span>
                        </div>
                    </div>
                    <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl text-slate-400 dark:text-slate-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm relative overflow-hidden group hover:border-emerald-500/30 transition-colors">
                <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-500/5 dark:bg-emerald-500/10 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                <div class="relative z-10 flex items-start justify-between">
                    <div>
                        <h4 class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Bugün Gelen / İzinli
                        </h4>
                        <div class="text-3xl font-black text-slate-900 dark:text-white flex items-baseline gap-1 font-mono">
                            {{ $analytics['checked_in_today'] }} <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">/ {{ $analytics['on_leave_today'] }}</span>
                        </div>
                    </div>
                    <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl text-slate-400 dark:text-slate-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm relative overflow-hidden group hover:border-blue-500/30 transition-colors">
                <div class="absolute top-0 right-0 w-24 h-24 bg-blue-500/5 dark:bg-blue-500/10 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                <div class="relative z-10 flex items-start justify-between">
                    <div>
                        <h4 class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                            Aylık Maaş Gideri
                        </h4>
                        <div class="text-2xl font-black text-slate-900 dark:text-white flex items-baseline gap-1 font-mono">
                            ₺{{ number_format($analytics['monthly_salary_sum'], 0) }}
                        </div>
                    </div>
                    <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl text-slate-400 dark:text-slate-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm relative overflow-hidden group hover:border-amber-500/30 transition-colors">
                <div class="absolute top-0 right-0 w-24 h-24 bg-amber-500/5 dark:bg-amber-500/10 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                <div class="relative z-10 flex items-start justify-between">
                    <div>
                        <h4 class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            Genel Performans
                        </h4>
                        <div class="text-3xl font-black text-slate-900 dark:text-white flex items-baseline gap-1 font-mono">
                            {{ $analytics['performance_avg'] }} <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">/ 100</span>
                        </div>
                    </div>
                    <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl text-slate-400 dark:text-slate-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.9 1.397-.9 1.697 0l1.518 4.674a1 1 0 00.95.69h4.907c.961 0 1.367 1.223.593 1.832l-3.978 2.893a1 1 0 00-.363 1.118l1.518 4.674c.3.9-.755 1.688-1.538 1.118l-3.978-2.893a1 1 0 00-1.17 0l-3.978 2.893c-.783.57-1.838-.218-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.978-2.893c-.773-.609-.368-1.832.593-1.832h4.907a1 1 0 00.95-.69l1.518-4.674z"></path></svg>
                    </div>
                </div>
            </div>

        </div>

        <!-- Onay Bekleyenler & Grafik Dağılımları -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Panel: Bekleyen Talepler -->
            <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Bekleyen İK Süreçleri</h3>
                
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
            <div class="lg:col-span-2 bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Departman Bazlı Dağılım</h3>
                
                <div class="space-y-4">
                    @forelse($analytics['department_distribution'] as $dept)
                        <div class="space-y-1">
                            <div class="flex justify-between items-center text-xs font-bold">
                                <span class="text-slate-700 dark:text-slate-300">{{ $dept['name'] }}</span>
                                <span class="text-slate-500 font-mono">{{ $dept['count'] }} Kişi</span>
                            </div>
                            <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                                <div class="bg-blue-600 h-full rounded-full" style="width: {{ $analytics['total_employees'] > 0 ? ($dept['count'] / $analytics['total_employees']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-xs text-slate-400 py-6">Departman verisi bulunmamaktadır.</div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>
@endsection
