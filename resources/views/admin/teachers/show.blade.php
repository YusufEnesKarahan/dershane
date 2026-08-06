@extends('layouts.admin')

@section('title', 'Öğretmen Profili: ' . ($teacher->user->name ?? ''))

@section('content')
<div class="p-6 h-full flex flex-col">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white flex items-center">
                <a href="{{ route('admin.teachers.index') }}" class="mr-3 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                Öğretmen Profili
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 ml-9">Öğretmen bilgilerini, performansını ve atanmış dersleri inceleyin.</p>
        </div>
        <div class="flex space-x-3">
            @can('update', $teacher)
            <a href="{{ route('admin.teachers.edit', $teacher->id) }}" class="btn-secondary flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Profili Düzenle
            </a>
            @endcan
            @can('delete', $teacher)
            <form action="{{ route('admin.teachers.destroy', $teacher->id) }}" method="POST" onsubmit="return confirm('Bu öğretmeni silmek istediğinize emin misiniz?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger flex items-center bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded-lg transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Sil
                </button>
            </form>
            @endcan
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4">
            <x-alert type="success" dismissible="true">
                {{ session('success') }}
            </x-alert>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4">
            <x-alert type="danger" dismissible="true">
                {{ session('error') }}
            </x-alert>
        </div>
    @endif

    <!-- Profile Overview Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 flex-1 overflow-y-auto">
        <!-- Sol Kolon: Temel Bilgiler -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Profil Kartı -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 text-center">
                <div class="w-24 h-24 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-bold text-3xl mx-auto mb-4 border-4 border-white dark:border-slate-800 shadow-sm">
                    {{ collect(explode(' ', $teacher->user->name ?? 'X'))->map(fn($n) => substr($n, 0, 1))->take(2)->implode('') }}
                </div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">{{ $teacher->user->name ?? 'Bilinmiyor' }}</h2>
                <p class="text-sm font-medium text-primary-600 dark:text-primary-400 mt-1">{{ $teacher->title ?? 'Branş Belirtilmemiş' }}</p>
                
                <div class="mt-4 flex justify-center">
                    @if(($teacher->status ?? 'Active') === 'Active')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 mr-1.5"></span> Aktif Eğitmen
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300">
                            <span class="w-2 h-2 rounded-full bg-slate-500 mr-1.5"></span> Pasif
                        </span>
                    @endif
                </div>

                <div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-700 space-y-4">
                    <div class="flex items-center text-sm text-slate-600 dark:text-slate-400">
                        <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span class="truncate">{{ $teacher->user->email ?? '-' }}</span>
                    </div>
                    <div class="flex items-center text-sm text-slate-600 dark:text-slate-400">
                        <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        {{ $teacher->user->phone ?? 'Telefon belirtilmemiş' }}
                    </div>
                    @if($teacher->birth_date)
                    <div class="flex items-center text-sm text-slate-600 dark:text-slate-400">
                        <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        {{ \Carbon\Carbon::parse($teacher->birth_date)->format('d.m.Y') }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- Profesyonel Profil -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-4 border-b border-slate-200 dark:border-slate-700 pb-2">Profesyonel Profil</h3>
                
                <div class="space-y-4">
                    <div>
                        <span class="block text-xs font-medium text-slate-500 dark:text-slate-400">Mezuniyet</span>
                        <span class="block text-sm font-medium text-slate-900 dark:text-white mt-1">{{ $teacher->education ?? 'Belirtilmemiş' }}</span>
                    </div>
                    
                    <div>
                        <span class="block text-xs font-medium text-slate-500 dark:text-slate-400">Uzmanlık Alanları</span>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @if($teacher->specialties)
                                @foreach(explode(',', $teacher->specialties) as $spec)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 border border-blue-100 dark:border-blue-800/30">
                                        {{ trim($spec) }}
                                    </span>
                                @endforeach
                            @else
                                <span class="text-sm text-slate-500">Belirtilmemiş</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-slate-50 dark:bg-slate-800/50 p-3 rounded-lg border border-slate-100 dark:border-slate-700">
                            <span class="block text-xs font-medium text-slate-500 dark:text-slate-400">Deneyim</span>
                            <span class="block text-lg font-bold text-slate-900 dark:text-white mt-1">{{ $teacher->experience_years ?? '0' }} Yıl</span>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800/50 p-3 rounded-lg border border-slate-100 dark:border-slate-700">
                            <span class="block text-xs font-medium text-slate-500 dark:text-slate-400">Sistem Kaydı</span>
                            <span class="block text-lg font-bold text-slate-900 dark:text-white mt-1">{{ $teacher->created_at->format('Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sağ Kolon: Aktiviteler & Sınıflar -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Son Aktiviteler -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                <div class="flex items-center justify-between mb-4 border-b border-slate-200 dark:border-slate-700 pb-2">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">Son Sistem Aktiviteleri</h3>
                </div>
                
                @if(isset($recent_activities) && count($recent_activities) > 0)
                <div class="flow-root">
                    <ul role="list" class="-mb-8">
                        @foreach($recent_activities as $idx => $activity)
                        <li>
                            <div class="relative pb-8">
                                @if(!$loop->last)
                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-slate-200 dark:bg-slate-700" aria-hidden="true"></span>
                                @endif
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center ring-8 ring-white dark:ring-slate-800">
                                            <svg class="w-4 h-4 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                        <div>
                                            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $activity->details ?? 'İşlem yapıldı.' }}</p>
                                        </div>
                                        <div class="whitespace-nowrap text-right text-xs text-slate-500 dark:text-slate-400">
                                            <time datetime="{{ $activity->created_at }}">{{ $activity->created_at->diffForHumans() }}</time>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @else
                <div class="text-center py-6">
                    <svg class="mx-auto h-12 w-12 text-slate-300 dark:text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Henüz aktivite kaydı bulunmuyor.</p>
                </div>
                @endif
            </div>

            <!-- Biyografi / Hakkında -->
            @if($teacher->bio)
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                <div class="flex items-center justify-between mb-4 border-b border-slate-200 dark:border-slate-700 pb-2">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">Hakkında (Biyografi)</h3>
                </div>
                <div class="prose dark:prose-invert max-w-none text-sm text-slate-600 dark:text-slate-300">
                    {{ $teacher->bio }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
