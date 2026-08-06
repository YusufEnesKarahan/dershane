@extends('layouts.admin')
@section('title', 'Yönetim Paneli')
@section('content')
    <div class="space-y-6">
        <!-- Welcome Hero Card -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 sm:p-8 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <span class="inline-block px-3 py-1 bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-800 rounded-full text-xs font-bold uppercase tracking-wider mb-2">
                    Dershane SaaS Platformu
                </span>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-slate-100 tracking-tight">
                    Hoş Geldiniz, {{ auth()->user()->name ?? 'Yönetici' }} 👋
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Aktif Şube: <span class="font-bold text-slate-800 dark:text-slate-200">{{ session('active_branch_name', auth()->user()->branch?->name ?? 'Merkez Şube') }}</span> — Bugünün kurum özetini inceleyin.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.students.create') }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition shadow-sm flex items-center gap-2">
                    <span>+</span> Yeni Öğrenci Kaydı
                </a>
                <a href="{{ route('admin.invoices.create') }}" class="px-5 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-sm rounded-xl transition">
                    + Fatura Oluştur
                </a>
            </div>
        </div>

        <!-- 4 Key Stat Cards (Öğrenci, Öğretmen, Sınıf, Tahsilat) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Öğrenci Stat -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block mb-1">Toplam Öğrenci</span>
                    <span class="text-2xl font-black text-slate-900 dark:text-slate-100">1,240</span>
                    <span class="inline-flex items-center text-xs font-bold text-emerald-600 dark:text-emerald-400 mt-1">
                        ↑ %12 geçen aya göre
                    </span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl font-bold border border-blue-100 dark:border-blue-900">
                    🎓
                </div>
            </div>

            <!-- Öğretmen Stat -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block mb-1">Kadro Öğretmen</span>
                    <span class="text-2xl font-black text-slate-900 dark:text-slate-100">42</span>
                    <span class="inline-flex items-center text-xs font-bold text-emerald-600 dark:text-emerald-400 mt-1">
                        ↑ 3 yeni katılım
                    </span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl font-bold border border-emerald-100 dark:border-emerald-900">
                    👨‍🏫
                </div>
            </div>

            <!-- Sınıf Stat -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block mb-1">Aktif Sınıf / Şube</span>
                    <span class="text-2xl font-black text-slate-900 dark:text-slate-100">28</span>
                    <span class="inline-flex items-center text-xs font-bold text-blue-600 dark:text-blue-400 mt-1">
                        Tam Kapasite %85
                    </span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl font-bold border border-amber-100 dark:border-amber-900">
                    🏫
                </div>
            </div>

            <!-- Tahsilat Stat -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block mb-1">Aylık Tahsilat</span>
                    <span class="text-2xl font-black text-slate-900 dark:text-slate-100">₺450,000</span>
                    <span class="inline-flex items-center text-xs font-bold text-emerald-600 dark:text-emerald-400 mt-1">
                        ↑ %18 artış
                    </span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl font-bold border border-emerald-100 dark:border-emerald-900">
                    💳
                </div>
            </div>
        </div>

        <!-- Lower Section: Son Kayıtlar & Aktiviteler -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Son Kayıtlar (2 Cols) -->
            <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                        <span>📋</span> Son Kaydolan Öğrenciler
                    </h3>
                    <a href="{{ route('admin.students.index') }}" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">
                        Tümünü Gör →
                    </a>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-800">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 text-xs font-bold uppercase text-slate-500 dark:text-slate-400">
                                <th class="px-4 py-3">Öğrenci</th>
                                <th class="px-4 py-3">Program / Sınıf</th>
                                <th class="px-4 py-3">Durum</th>
                                <th class="px-4 py-3 text-right">Kayıt Tarihi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                                <td class="px-4 py-3 font-bold text-slate-900 dark:text-slate-100">Ahmet Yılmaz</td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-400">YKS Sayısal 12-A</td>
                                <td class="px-4 py-3">
                                    <span class="px-2.5 py-1 text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 rounded-full">Aktif</span>
                                </td>
                                <td class="px-4 py-3 text-right text-xs text-slate-500 dark:text-slate-400">Bugün</td>
                            </tr>
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                                <td class="px-4 py-3 font-bold text-slate-900 dark:text-slate-100">Ayşe Demir</td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-400">LGS Hazırlık 8-B</td>
                                <td class="px-4 py-3">
                                    <span class="px-2.5 py-1 text-xs font-semibold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800 rounded-full">Bekliyor</span>
                                </td>
                                <td class="px-4 py-3 text-right text-xs text-slate-500 dark:text-slate-400">Dün</td>
                            </tr>
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                                <td class="px-4 py-3 font-bold text-slate-900 dark:text-slate-100">Caner Öztürk</td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-400">YKS Eşit Ağırlık 12-B</td>
                                <td class="px-4 py-3">
                                    <span class="px-2.5 py-1 text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 rounded-full">Aktif</span>
                                </td>
                                <td class="px-4 py-3 text-right text-xs text-slate-500 dark:text-slate-400">3 Gün Önce</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Son Aktiviteler (1 Col) -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 space-y-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                    <span>⚡</span> Son Sistem Aktiviteleri
                </h3>

                <div class="space-y-3 text-xs">
                    <div class="p-3 bg-slate-50 dark:bg-slate-800/40 rounded-xl border border-slate-200/80 dark:border-slate-800 flex items-start gap-3">
                        <span class="p-1.5 bg-blue-100 dark:bg-blue-950 text-blue-600 dark:text-blue-300 rounded-lg font-bold">💳</span>
                        <div>
                            <p class="font-bold text-slate-800 dark:text-slate-200">Tahsilat Alındı</p>
                            <p class="text-slate-500 dark:text-slate-400 mt-0.5">Ahmet Yılmaz velisinden ₺4,500 taksit tahsil edildi.</p>
                            <span class="text-[10px] text-slate-400 mt-1 block">10 dakika önce</span>
                        </div>
                    </div>

                    <div class="p-3 bg-slate-50 dark:bg-slate-800/40 rounded-xl border border-slate-200/80 dark:border-slate-800 flex items-start gap-3">
                        <span class="p-1.5 bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-300 rounded-lg font-bold">📢</span>
                        <div>
                            <p class="font-bold text-slate-800 dark:text-slate-200">Duyuru Yayınlandı</p>
                            <p class="text-slate-500 dark:text-slate-400 mt-0.5">"YKS Deneme Sınavı Takvimi" portal üzerinde duyuruldu.</p>
                            <span class="text-[10px] text-slate-400 mt-1 block">1 saat önce</span>
                        </div>
                    </div>

                    <div class="p-3 bg-slate-50 dark:bg-slate-800/40 rounded-xl border border-slate-200/80 dark:border-slate-800 flex items-start gap-3">
                        <span class="p-1.5 bg-amber-100 dark:bg-amber-950 text-amber-600 dark:text-amber-300 rounded-lg font-bold">📝</span>
                        <div>
                            <p class="font-bold text-slate-800 dark:text-slate-200">Yeni Ön Kayıt</p>
                            <p class="text-slate-500 dark:text-slate-400 mt-0.5">Mehmet Demir için ön kayıt formu dolduruldu.</p>
                            <span class="text-[10px] text-slate-400 mt-1 block">3 saat önce</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection