@extends('layouts.admin')
@section('title', 'Öğrenci Profili: ' . $student->full_name)
@section('content')
<div class="space-y-6">
    <!-- Header / Quick Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-black text-2xl shadow-sm border border-indigo-200 dark:border-indigo-800">
                {{ mb_substr($student->first_name, 0, 1) }}{{ mb_substr($student->last_name, 0, 1) }}
            </div>
            <div>
                <h1 class="text-2xl font-black text-neutral-900 dark:text-white">{{ $student->full_name }}</h1>
                <div class="flex items-center gap-3 mt-1">
                    <span class="text-sm font-mono text-neutral-500 dark:text-neutral-400">No: {{ $student->student_number }}</span>
                    <span class="px-2.5 py-0.5 rounded text-[11px] font-bold uppercase tracking-wider
                        {{ $student->status === 'Active' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400' : '' }}
                        {{ $student->status === 'Inactive' ? 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400' : '' }}
                        {{ $student->status === 'Graduated' ? 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400' : '' }}
                        {{ $student->status === 'Suspended' ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400' : '' }}
                    ">
                        {{ $student->status === 'Active' ? 'Aktif' : ($student->status === 'Graduated' ? 'Mezun' : ($student->status === 'Suspended' ? 'Ayrıldı' : 'Pasif')) }}
                    </span>
                </div>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.students.index') }}" class="px-4 py-2 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 text-neutral-700 dark:text-neutral-300 rounded-xl text-sm font-semibold hover:bg-neutral-50 dark:hover:bg-neutral-700 transition-colors shadow-sm">
                Listeye Dön
            </a>
            @can('update', $student)
            <a href="{{ route('admin.students.edit', $student->id) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold transition-colors shadow-sm flex items-center gap-2">
                <i class="fas fa-edit"></i> Profili Düzenle
            </a>
            @endcan
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl shadow-sm flex items-center gap-2 font-bold">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Column: Details -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Personal Info Card -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white flex items-center gap-2 mb-4">
                    <i class="fas fa-address-card text-indigo-500"></i> Kişisel & İletişim Bilgileri
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-6">
                    <div>
                        <div class="text-xs font-semibold text-neutral-500 dark:text-neutral-400 mb-1">TC Kimlik No</div>
                        <div class="text-sm text-neutral-900 dark:text-white font-mono">{{ $student->identity_number ?: '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-neutral-500 dark:text-neutral-400 mb-1">Cinsiyet</div>
                        <div class="text-sm text-neutral-900 dark:text-white font-bold">{{ $student->gender ?: '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-neutral-500 dark:text-neutral-400 mb-1">Doğum Tarihi</div>
                        <div class="text-sm text-neutral-900 dark:text-white">{{ $student->birth_date ? \Carbon\Carbon::parse($student->birth_date)->format('d.m.Y') : '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-neutral-500 dark:text-neutral-400 mb-1">Öğrenci Telefon</div>
                        <div class="text-sm text-neutral-900 dark:text-white font-mono">{{ $student->contact?->phone ?: '-' }}</div>
                    </div>
                    <div class="sm:col-span-2">
                        <div class="text-xs font-semibold text-neutral-500 dark:text-neutral-400 mb-1">Adres Bilgisi</div>
                        <div class="text-sm text-neutral-900 dark:text-white">{{ $student->address?->address_text ?: 'Adres bilgisi girilmemiş.' }}</div>
                    </div>
                </div>
            </div>

            <!-- Veli Kartı (Guardian Info Card) -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-neutral-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-user-shield text-emerald-500"></i> Veli Kartı
                    </h3>
                    @if($guardian_user_account)
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                            Veli Portalı Hesabı Var
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-neutral-100 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-400">
                            Veli Hesabı Yok
                        </span>
                    @endif
                </div>
                @if($primary_guardian)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-6">
                        <div>
                            <div class="text-xs font-semibold text-neutral-500 dark:text-neutral-400 mb-1">Veli Ad Soyad</div>
                            <div class="text-sm text-neutral-900 dark:text-white font-bold">{{ $primary_guardian->guardian_name }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-neutral-500 dark:text-neutral-400 mb-1">Yakınlık Derecesi</div>
                            <div class="text-sm text-neutral-900 dark:text-white">{{ $primary_guardian->relation ?: 'Veli' }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-neutral-500 dark:text-neutral-400 mb-1">Telefon</div>
                            <div class="text-sm text-neutral-900 dark:text-white font-mono">{{ $primary_guardian->phone ?: '-' }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-neutral-500 dark:text-neutral-400 mb-1">E-posta</div>
                            <div class="text-sm text-neutral-900 dark:text-white">{{ $primary_guardian->email ?: '-' }}</div>
                        </div>
                    </div>
                @else
                    <div class="text-sm text-neutral-500 italic p-4 bg-neutral-50 dark:bg-neutral-800/50 rounded-xl">Veli bilgisi girilmemiş.</div>
                @endif
            </div>

            <!-- Academic Info Card -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white flex items-center gap-2 mb-4">
                    <i class="fas fa-graduation-cap text-indigo-500"></i> Akademik & Sınıf Bilgisi
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-6">
                    <div>
                        <div class="text-xs font-semibold text-neutral-500 dark:text-neutral-400 mb-1">Kayıtlı Sınıf</div>
                        <div class="text-sm text-neutral-900 dark:text-white font-bold">{{ $student->classroom ? $student->classroom->name . ' (' . $student->classroom->code . ')' : 'Sınıf Atanmadı' }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-neutral-500 dark:text-neutral-400 mb-1">Sisteme Kayıt Tarihi</div>
                        <div class="text-sm text-neutral-900 dark:text-white">{{ $student->created_at->format('d.m.Y H:i') }}</div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column: Account Status & Activities -->
        <div class="space-y-6">
            
            <!-- Kullanıcı Hesabı Kartı -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white flex items-center gap-2 mb-4">
                    <i class="fas fa-user-lock text-indigo-500"></i> Kullanıcı Hesabı Kartı
                </h3>
                @if($user_account)
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-emerald-50 dark:bg-emerald-950/30 rounded-xl border border-emerald-100 dark:border-emerald-900/50">
                            <span class="text-xs font-bold text-emerald-800 dark:text-emerald-300">Giriş Hesabı Aktif</span>
                            <i class="fas fa-check-circle text-emerald-600"></i>
                        </div>
                        <div>
                            <span class="text-xs text-neutral-500 dark:text-neutral-400 block">Kullanıcı Adı / Ad</span>
                            <span class="text-sm font-semibold text-neutral-900 dark:text-white">{{ $user_account->name }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-neutral-500 dark:text-neutral-400 block">Giriş E-Posta</span>
                            <span class="text-sm font-semibold text-neutral-900 dark:text-white">{{ $user_account->email }}</span>
                        </div>
                    </div>
                @else
                    <div class="p-4 bg-amber-50 dark:bg-amber-950/30 rounded-xl border border-amber-200 dark:border-amber-900/50 text-amber-800 dark:text-amber-300 text-xs leading-relaxed">
                        <i class="fas fa-info-circle mr-1"></i> Bu öğrenci için sisteme giriş hesabı (User) tanımlanmamış. Öğrenci sadece idari kayıtlarda yer alır.
                    </div>
                @endif
            </div>

            <!-- Giriş Durumu Kartı -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white flex items-center gap-2 mb-4">
                    <i class="fas fa-shield-alt text-indigo-500"></i> Giriş Durumu Kartı
                </h3>
                <div class="space-y-3 text-xs">
                    <div class="flex items-center justify-between pb-2 border-b border-neutral-100 dark:border-neutral-800">
                        <span class="text-neutral-600 dark:text-neutral-400">Öğrenci Portalı Hesabı:</span>
                        <span class="font-bold {{ $login_status['has_account'] ? 'text-emerald-600' : 'text-neutral-400' }}">
                            {{ $login_status['has_account'] ? 'TANIMLI' : 'TANIMSIZ' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between pb-2 border-b border-neutral-100 dark:border-neutral-800">
                        <span class="text-neutral-600 dark:text-neutral-400">Veli Portalı Hesabı:</span>
                        <span class="font-bold {{ $parent_login_status['has_account'] ? 'text-emerald-600' : 'text-neutral-400' }}">
                            {{ $parent_login_status['has_account'] ? 'TANIMLI' : 'TANIMSIZ' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-neutral-600 dark:text-neutral-400">Hesap Durumu:</span>
                        <span class="font-bold text-neutral-900 dark:text-white">
                            {{ $student->status === 'Active' ? 'Aktif Öğrenci' : 'Pasif Öğrenci' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Attendance Summary -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white flex items-center gap-2 mb-4">
                    <i class="fas fa-calendar-check text-indigo-500"></i> Yoklama Özeti
                </h3>
                <div class="flex items-center justify-between p-4 bg-neutral-50 dark:bg-neutral-800/50 rounded-xl">
                    <div class="text-center">
                        <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ $attendance_summary['present'] }}</div>
                        <div class="text-[10px] font-bold text-neutral-500 uppercase mt-1">Katılım</div>
                    </div>
                    <div class="w-px h-10 bg-neutral-200 dark:bg-neutral-700"></div>
                    <div class="text-center">
                        <div class="text-2xl font-black text-rose-500 dark:text-rose-400">{{ $attendance_summary['absent'] }}</div>
                        <div class="text-[10px] font-bold text-neutral-500 uppercase mt-1">Devamsız</div>
                    </div>
                    <div class="w-px h-10 bg-neutral-200 dark:bg-neutral-700"></div>
                    <div class="text-center">
                        <div class="text-2xl font-black text-indigo-600 dark:text-indigo-400">{{ $attendance_summary['total'] }}</div>
                        <div class="text-[10px] font-bold text-neutral-500 uppercase mt-1">Toplam</div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white flex items-center gap-2 mb-4">
                    <i class="fas fa-history text-indigo-500"></i> Son Aktiviteler
                </h3>
                @if($recent_activities->count() > 0)
                    <div class="space-y-4">
                        @foreach($recent_activities as $activity)
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center shrink-0">
                                    <i class="fas fa-info-circle text-indigo-500 text-xs"></i>
                                </div>
                                <div>
                                    <div class="text-sm text-neutral-800 dark:text-neutral-200">{{ $activity->details ?: 'İşlem yapıldı' }}</div>
                                    <div class="text-[10px] text-neutral-400 mt-0.5">{{ $activity->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-sm text-neutral-500 italic text-center py-4">Kayıtlı aktivite bulunamadı.</div>
                @endif
            </div>

        </div>

    </div>
</div>
@endsection
