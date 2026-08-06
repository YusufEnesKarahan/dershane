@extends('layouts.admin')

@section('title', 'Veli Profili: ' . ($guardian->guardian_name ?? 'Veli Detayı'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-black text-2xl shadow-sm border border-emerald-200 dark:border-emerald-800">
                <i class="fas fa-user-shield"></i>
            </div>
            <div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white">{{ $guardian->guardian_name ?: 'Veli Profili' }}</h1>
                <div class="flex items-center gap-3 mt-1 text-sm text-slate-500 dark:text-slate-400">
                    <span>{{ $guardian->relation ?: 'Veli' }}</span>
                    <span>•</span>
                    <span class="font-mono">{{ $guardian->phone ?: '-' }}</span>
                </div>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.students.index') }}" class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-semibold hover:bg-slate-50 transition-colors shadow-sm">
                Öğrenci Listesine Dön
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Veli Bilgileri Kartı -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                <i class="fas fa-id-card text-emerald-500"></i> Veli İletişim & Hesap Bilgisi
            </h3>
            <div>
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 block">Ad Soyad</span>
                <span class="text-sm font-bold text-slate-900 dark:text-white">{{ $guardian->guardian_name }}</span>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 block">Yakınlık Derecesi</span>
                <span class="text-sm text-slate-900 dark:text-white">{{ $guardian->relation ?: 'Veli' }}</span>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 block">Telefon</span>
                <span class="text-sm font-mono text-slate-900 dark:text-white">{{ $guardian->phone ?: '-' }}</span>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 block">E-posta</span>
                <span class="text-sm text-slate-900 dark:text-white">{{ $guardian->email ?: '-' }}</span>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 block">Veli Portalı Hesabı</span>
                @if($guardian->user)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300 mt-1">
                        <i class="fas fa-check-circle text-xs"></i> Giriş Hesabı Aktif ({{ $guardian->user->email }})
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 mt-1">
                        Giriş Hesabı Yok
                    </span>
                @endif
            </div>
        </div>

        <!-- Bağlı Öğrenciler Listesi -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3 mb-4">
                <span class="flex items-center gap-2">
                    <i class="fas fa-user-graduate text-blue-500"></i> Bağlı Öğrenciler
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                    {{ $linkedStudents->count() }} Öğrenci
                </span>
            </h3>

            @if($linkedStudents->count() > 0)
                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($linkedStudents as $student)
                        <div class="py-3 flex items-center justify-between hover:bg-slate-50 dark:hover:bg-slate-800/40 px-2 rounded-xl transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold text-sm">
                                    {{ mb_substr($student->first_name, 0, 1) }}{{ mb_substr($student->last_name, 0, 1) }}
                                </div>
                                <div>
                                    <a href="{{ route('admin.students.show', $student->id) }}" class="text-sm font-bold text-slate-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                        {{ $student->full_name }}
                                    </a>
                                    <div class="text-xs text-slate-500 dark:text-slate-400 font-mono mt-0.5">
                                        No: {{ $student->student_number }} • Sınıf: {{ $student->classroom ? $student->classroom->name : 'Atanmadı' }}
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase
                                    {{ $student->status === 'Active' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-slate-100 text-slate-700' }}
                                ">
                                    {{ $student->status === 'Active' ? 'Aktif' : 'Pasif' }}
                                </span>
                                <a href="{{ route('admin.students.show', $student->id) }}" class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-500/10 rounded-lg transition-colors text-xs font-semibold">
                                    Öğrenci Detayı <i class="fas fa-chevron-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-6 text-center text-sm text-slate-500 italic">
                    Bu veliye bağlı öğrenci kaydı bulunamadı.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
