@extends('layouts.admin')

@section('title', 'Sınıf Detayı: ' . $classroom->name)

@section('content')
<div class="p-6 h-full flex flex-col space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="flex items-center">
            <div class="h-16 w-16 rounded-xl flex items-center justify-center font-bold text-2xl text-white shadow-sm" style="background-color: {{ $classroom->color_code ?? '#4F46E5' }}">
                {{ substr($classroom->name, 0, 2) }}
            </div>
            <div class="ml-4">
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white flex items-center">
                    {{ $classroom->name }}
                    @if($classroom->is_active)
                        <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">
                            Aktif
                        </span>
                    @else
                        <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300">
                            Pasif
                        </span>
                    @endif
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kod: {{ $classroom->code ?? '-' }} | Tür: {{ $classroom->type->name ?? '-' }}</p>
            </div>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.classrooms.index') }}" class="btn-secondary">Listeye Dön</a>
            @can('update', $classroom)
            <a href="{{ route('admin.classrooms.students', $classroom->id) }}" class="btn-primary bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500">Öğrenci Yönetimi</a>
            <a href="{{ route('admin.classrooms.edit', $classroom->id) }}" class="btn-primary bg-amber-600 hover:bg-amber-700 focus:ring-amber-500 border-amber-600">Düzenle</a>
            @endcan
        </div>
    </div>

    @if(session('success'))
        <x-alert type="success" dismissible="true">
            {{ session('success') }}
        </x-alert>
    @endif
    @if(session('error'))
        <x-alert type="danger" dismissible="true">
            {{ session('error') }}
        </x-alert>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Info Card -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 md:col-span-1">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4">Sınıf Bilgileri</h3>
            <div class="space-y-4">
                <div>
                    <span class="block text-sm font-medium text-slate-500 dark:text-slate-400">Sorumlu Öğretmen</span>
                    <span class="block text-base font-semibold text-slate-900 dark:text-white mt-1">
                        {{ $classroom->teacher ? $classroom->teacher->user->name : 'Atanmamış' }}
                    </span>
                </div>
                <div>
                    <span class="block text-sm font-medium text-slate-500 dark:text-slate-400">Kapasite Durumu</span>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="text-base font-semibold text-slate-900 dark:text-white">{{ $classroom->students->count() }} Öğrenci</span>
                        <span class="text-sm text-slate-500 dark:text-slate-400">Max {{ $classroom->capacity }}</span>
                    </div>
                    <div class="mt-2 w-full bg-slate-200 rounded-full h-2 dark:bg-slate-700">
                        @php
                            $percentage = $classroom->capacity > 0 ? min(100, round(($classroom->students->count() / $classroom->capacity) * 100)) : 0;
                            $colorClass = $percentage >= 90 ? 'bg-rose-500' : ($percentage >= 75 ? 'bg-amber-500' : 'bg-primary-500');
                        @endphp
                        <div class="{{ $colorClass }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
                <div>
                    <span class="block text-sm font-medium text-slate-500 dark:text-slate-400">Oluşturulma</span>
                    <span class="block text-base text-slate-900 dark:text-white mt-1">{{ $classroom->created_at->format('d M Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Enrolled Students List -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 md:col-span-2 flex flex-col h-full">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Kayıtlı Öğrenciler ({{ $classroom->students->count() }})</h3>
                @can('update', $classroom)
                <a href="{{ route('admin.classrooms.students', $classroom->id) }}" class="text-sm font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400">
                    Öğrenci Ekle/Çıkar &rarr;
                </a>
                @endcan
            </div>
            
            <div class="flex-1 overflow-y-auto min-h-[300px]">
                @if($classroom->students->count() > 0)
                <ul class="divide-y divide-slate-200 dark:divide-slate-700">
                    @foreach($classroom->students as $student)
                    <li class="py-3 flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="h-8 w-8 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-300 font-bold text-xs">
                                {{ collect(explode(' ', $student->user->name ?? 'X'))->map(fn($n) => substr($n, 0, 1))->take(2)->implode('') }}
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $student->user->name ?? 'Bilinmiyor' }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $student->student_number ?? 'Öğrenci No Yok' }}</p>
                            </div>
                        </div>
                        <div>
                            <!-- Placeholder for student actions (e.g. view profile) -->
                        </div>
                    </li>
                    @endforeach
                </ul>
                @else
                <div class="h-full flex flex-col items-center justify-center text-slate-500 dark:text-slate-400 py-10">
                    <svg class="h-12 w-12 mb-3 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <p class="text-sm">Bu sınıfa henüz öğrenci eklenmemiş.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
