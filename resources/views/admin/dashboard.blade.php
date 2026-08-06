@extends('layouts.admin')
@section('title', 'Yönetim Paneli')
@section('content')
    <div class="space-y-6">
        {{-- ─── Welcome Header ─── --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900 dark:text-slate-100 tracking-tight">
                    Hoş Geldiniz, {{ auth()->user()->name ?? 'Yönetici' }}
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    {{ session('active_branch_name', auth()->user()->branch?->name ?? 'Merkez Şube') }} — {{ now()->translatedFormat('d F Y, l') }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.students.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Yeni Öğrenci
                </a>
                <a href="{{ route('admin.invoices.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-sm font-medium rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Fatura Oluştur
                </a>
            </div>
        </div>

        {{-- ─── Stat Cards ─── --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Students --}}
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium text-slate-500 dark:text-slate-400">Toplam Öğrenci</span>
                    <div class="p-2 rounded-lg bg-blue-50 dark:bg-blue-950/40">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 7a4 4 0 100-8 4 4 0 000 8z"/><path stroke-linecap="round" stroke-linejoin="round" d="M23 21v-2a4 4 0 00-3-3.87"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 3.13a4 4 0 010 7.75"/></svg>
                    </div>
                </div>
                <div class="text-2xl font-semibold text-slate-900 dark:text-slate-100">{{ number_format($totalStudents) }}</div>
            </div>

            {{-- Teachers --}}
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium text-slate-500 dark:text-slate-400">Kadro Öğretmen</span>
                    <div class="p-2 rounded-lg bg-emerald-50 dark:bg-emerald-950/40">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M22 10v6M2 10l10-5 10 5-10 5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 12v5c3 3 7 3 12 0v-5"/></svg>
                    </div>
                </div>
                <div class="text-2xl font-semibold text-slate-900 dark:text-slate-100">{{ number_format($totalTeachers) }}</div>
            </div>

            {{-- Classrooms --}}
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium text-slate-500 dark:text-slate-400">Aktif Sınıf</span>
                    <div class="p-2 rounded-lg bg-amber-50 dark:bg-amber-950/40">
                        <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                </div>
                <div class="text-2xl font-semibold text-slate-900 dark:text-slate-100">{{ number_format($totalClassrooms) }}</div>
            </div>

            {{-- Revenue --}}
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium text-slate-500 dark:text-slate-400">Aylık Tahsilat</span>
                    <div class="p-2 rounded-lg bg-emerald-50 dark:bg-emerald-950/40">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="text-2xl font-semibold text-slate-900 dark:text-slate-100">₺{{ number_format($monthlyRevenue, 0, ',', '.') }}</div>
            </div>
        </div>

        {{-- ─── Main Content Area ─── --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Recent Students (2/3 width) --}}
            <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Son Kaydolan Öğrenciler</h3>
                    <a href="{{ route('admin.students.index') }}" class="text-xs font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                        Tümünü Gör →
                    </a>
                </div>

                @if($recentStudents->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-800/50 text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    <th class="px-5 py-2.5 text-left">Öğrenci</th>
                                    <th class="px-5 py-2.5 text-left">Sınıf</th>
                                    <th class="px-5 py-2.5 text-left">Durum</th>
                                    <th class="px-5 py-2.5 text-right">Kayıt Tarihi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @foreach($recentStudents as $student)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                                        <td class="px-5 py-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-sm font-medium text-slate-600 dark:text-slate-300 shrink-0">
                                                    {{ substr($student->name ?? '?', 0, 1) }}
                                                </div>
                                                <span class="font-medium text-slate-900 dark:text-slate-100">{{ $student->name ?? '-' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-5 py-3 text-slate-500 dark:text-slate-400">{{ $student->classroom?->name ?? '-' }}</td>
                                        <td class="px-5 py-3">
                                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full
                                                {{ ($student->status ?? 'active') === 'active'
                                                    ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300'
                                                    : 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300' }}">
                                                {{ ($student->status ?? 'active') === 'active' ? 'Aktif' : 'Bekliyor' }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3 text-right text-xs text-slate-500 dark:text-slate-400">
                                            {{ $student->created_at?->diffForHumans() ?? '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-12 px-6 text-center">
                        <div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-3">
                            <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 7a4 4 0 100-8 4 4 0 000 8z"/></svg>
                        </div>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Henüz öğrenci kaydı bulunmuyor.</p>
                        <a href="{{ route('admin.students.create') }}" class="mt-3 text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700">
                            İlk öğrenciyi kaydet →
                        </a>
                    </div>
                @endif
            </div>

            {{-- Activity Feed (1/3 width) --}}
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Son Aktiviteler</h3>
                </div>

                @if($recentActivities->count() > 0)
                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($recentActivities as $activity)
                            <div class="px-5 py-3.5 flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm text-slate-700 dark:text-slate-300 line-clamp-2">{{ $activity->description ?? 'Sistem aktivitesi' }}</p>
                                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">{{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-12 px-6 text-center">
                        <div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-3">
                            <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Henüz aktivite bulunmuyor.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- ─── Quick Actions Grid ─── --}}
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100 mb-4">Hızlı İşlemler</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                @php
                    $quickActions = [
                        ['label' => 'Öğrenci Ekle', 'route' => 'admin.students.create', 'icon' => 'M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2||M8.5 7a4 4 0 100-8 4 4 0 000 8z||M20 8v6||M23 11h-6'],
                        ['label' => 'Fatura Oluştur', 'route' => 'admin.invoices.create', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                        ['label' => 'Duyuru Yap', 'route' => 'admin.announcements.create', 'icon' => 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z'],
                        ['label' => 'Devamsızlık', 'route' => 'admin.attendances.sessions.index', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                        ['label' => 'Sınav Oluştur', 'route' => 'admin.exams.create', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                        ['label' => 'Raporlar', 'route' => 'admin.reporting.dashboard', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                    ];
                @endphp
                @foreach($quickActions as $action)
                    <a href="{{ \Illuminate\Support\Facades\Route::has($action['route']) ? route($action['route']) : '#' }}"
                       class="flex flex-col items-center gap-2 p-4 rounded-lg border border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:border-slate-200 dark:hover:border-slate-700 transition-all text-center group">
                        <div class="w-9 h-9 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center group-hover:bg-blue-50 dark:group-hover:bg-blue-950/30 transition-colors">
                            <svg class="w-4.5 h-4.5 text-slate-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                @foreach(explode('||', $action['icon']) as $path)
                                    <path d="{{ $path }}"></path>
                                @endforeach
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-slate-600 dark:text-slate-400 group-hover:text-slate-900 dark:group-hover:text-slate-200 transition-colors">{{ $action['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@endsection