@extends('layouts.admin')

@section('title', 'Öğretmenler')

@section('content')
<div class="p-6 h-full flex flex-col">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Öğretmenler</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Sistemdeki öğretmenleri görüntüleyin ve yönetin.</p>
        </div>
        <div class="flex space-x-3">
            @can('create', App\Models\Teacher::class)
            <a href="{{ route('admin.teachers.create') }}" class="btn-primary flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Yeni Öğretmen Ekle
            </a>
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

    <!-- Search & Filter Card -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-5 mb-6">
        <form action="{{ route('admin.teachers.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Ara</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        class="pl-10 form-input w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                        placeholder="İsim, e-posta veya telefon ile ara...">
                </div>
            </div>

            <div class="w-full md:w-48">
                <label for="status" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Durum</label>
                <select name="status" id="status" class="form-select w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Tümü</option>
                    <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Aktif</option>
                    <option value="Inactive" {{ request('status') === 'Inactive' ? 'selected' : '' }}>Pasif</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="btn-secondary w-full md:w-auto h-[42px] px-6">
                    Filtrele
                </button>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 flex-1 flex flex-col overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                <thead class="bg-slate-50 dark:bg-slate-800/50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-10">
                            #
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Öğretmen
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            İletişim
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Branş / Uzmanlık
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Durum
                        </th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            İşlemler
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-slate-800">
                    @forelse($teachers as $teacher)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                            {{ $teacher->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-bold text-sm shrink-0">
                                    {{ collect(explode(' ', $teacher->user->name ?? 'X'))->map(fn($n) => substr($n, 0, 1))->take(2)->implode('') }}
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-slate-900 dark:text-white">
                                        {{ $teacher->user->name ?? 'Bilinmiyor' }}
                                    </div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">
                                        Katılım: {{ $teacher->created_at->format('d.m.Y') }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-slate-900 dark:text-white">{{ $teacher->user->email ?? '-' }}</div>
                            <div class="text-sm text-slate-500 dark:text-slate-400">{{ $teacher->user->phone ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-slate-900 dark:text-white">{{ $teacher->title ?? '-' }}</div>
                            <div class="text-xs text-slate-500 dark:text-slate-400 truncate max-w-[200px]" title="{{ $teacher->specialties }}">
                                {{ $teacher->specialties ?? '-' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if(($teacher->status ?? 'Active') === 'Active')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/30">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-600">
                                    Pasif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-3">
                                @can('view', $teacher)
                                <a href="{{ route('admin.teachers.show', $teacher->id) }}" class="text-slate-400 hover:text-primary-600 dark:hover:text-primary-400" title="Görüntüle">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                @endcan

                                @can('update', $teacher)
                                <a href="{{ route('admin.teachers.edit', $teacher->id) }}" class="text-slate-400 hover:text-amber-600 dark:hover:text-amber-400" title="Düzenle">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                @endcan

                                @can('delete', $teacher)
                                <form action="{{ route('admin.teachers.destroy', $teacher->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Bu öğretmeni silmek istediğinize emin misiniz? (Bu işlem geri alınabilir)');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-rose-600 dark:hover:text-rose-400" title="Sil">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                            <svg class="mx-auto h-12 w-12 text-slate-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <p class="text-base font-medium text-slate-900 dark:text-white mb-1">Öğretmen Bulunamadı</p>
                            <p class="text-sm">Arama kriterlerinize uyan bir öğretmen kaydı bulunamadı veya henüz öğretmen eklenmemiş.</p>
                            @can('create', App\Models\Teacher::class)
                            <div class="mt-6">
                                <a href="{{ route('admin.teachers.create') }}" class="btn-primary inline-flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    İlk Öğretmeni Ekle
                                </a>
                            </div>
                            @endcan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($teachers->hasPages())
        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
            {{ $teachers->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
