@extends('layouts.admin')

@section('title', 'Sınıflar')

@section('content')
<div class="p-6 h-full flex flex-col">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Sınıflar</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Sistemdeki sınıfları görüntüleyin ve yönetin.</p>
        </div>
        <div class="flex space-x-3">
            @can('create', App\Models\Classroom::class)
            <a href="{{ route('admin.classrooms.create') }}" class="btn-primary flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Yeni Sınıf Ekle
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
                            Sınıf
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Sorumlu Öğretmen
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Öğrenci / Kapasite
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
                    @forelse($classrooms as $classroom)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                            {{ $classroom->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-lg flex items-center justify-center font-bold text-sm shrink-0 text-white" style="background-color: {{ $classroom->color_code ?? '#4F46E5' }}">
                                    {{ substr($classroom->name, 0, 2) }}
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-slate-900 dark:text-white">
                                        {{ $classroom->name }}
                                    </div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">
                                        Kod: {{ $classroom->code ?? '-' }} | Tür: {{ $classroom->type->name ?? '-' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700 dark:text-slate-300">
                            {{ $classroom->teacher ? $classroom->teacher->user->name : 'Atanmamış' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-2">
                                <div class="w-24 bg-slate-200 rounded-full h-2.5 dark:bg-slate-700">
                                    @php
                                        $percentage = $classroom->capacity > 0 ? min(100, round(($classroom->students_count / $classroom->capacity) * 100)) : 0;
                                        $colorClass = $percentage >= 90 ? 'bg-rose-500' : ($percentage >= 75 ? 'bg-amber-500' : 'bg-primary-500');
                                    @endphp
                                    <div class="{{ $colorClass }} h-2.5 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                                <span class="text-xs text-slate-500 dark:text-slate-400">
                                    {{ $classroom->students_count }} / {{ $classroom->capacity }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($classroom->is_active)
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
                                @can('update', $classroom)
                                <a href="{{ route('admin.classrooms.students', $classroom->id) }}" class="text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400" title="Öğrenci Yönetimi">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                </a>
                                @endcan

                                @can('view', $classroom)
                                <a href="{{ route('admin.classrooms.show', $classroom->id) }}" class="text-slate-400 hover:text-primary-600 dark:hover:text-primary-400" title="Görüntüle">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                @endcan

                                @can('update', $classroom)
                                <a href="{{ route('admin.classrooms.edit', $classroom->id) }}" class="text-slate-400 hover:text-amber-600 dark:hover:text-amber-400" title="Düzenle">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                @endcan

                                @can('delete', $classroom)
                                <form action="{{ route('admin.classrooms.destroy', $classroom->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Bu sınıfı silmek istediğinize emin misiniz? Öğrencilerin atamaları kaldırılacaktır.');">
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <p class="text-base font-medium text-slate-900 dark:text-white mb-1">Sınıf Bulunamadı</p>
                            <p class="text-sm">Henüz sistemde hiç sınıf oluşturulmamış.</p>
                            @can('create', App\Models\Classroom::class)
                            <div class="mt-6">
                                <a href="{{ route('admin.classrooms.create') }}" class="btn-primary inline-flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    İlk Sınıfı Ekle
                                </a>
                            </div>
                            @endcan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($classrooms->hasPages())
        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
            {{ $classrooms->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
