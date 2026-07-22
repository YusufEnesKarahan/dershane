@extends('layouts.admin')
@section('title', 'Sınıflarım & Öğrencilerim')
@section('content')
    <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm space-y-6">
        <div>
            <h1 class="text-lg font-bold text-neutral-900 dark:text-white">Aktif Sınıflarım & Derslerim</h1>
            <p class="text-xs text-neutral-500 mt-1">Ders vermekte olduğunuz şube, sınıf ve müfredat detaylarını inceleyin.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($assignedClasses as $asg)
                <div class="p-6 bg-neutral-50 dark:bg-neutral-800/40 rounded-2xl border border-neutral-100/80 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="px-2 py-0.5 text-[10px] font-bold bg-primary/10 text-primary rounded-full">{{ $asg->course->code ?? 'KOD' }}</span>
                        <span class="text-xs font-bold text-neutral-400 font-mono">{{ $asg->classroom->code ?? 'SINIF-KOD' }}</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-neutral-900 dark:text-white">{{ $asg->classroom->name }}</h3>
                        <p class="text-xs text-neutral-500 mt-0.5">{{ $asg->course->name }}</p>
                    </div>

                    <div class="pt-4 border-t border-neutral-100 flex items-center justify-between">
                        <span class="text-xs text-neutral-600 font-semibold">Kapasite: {{ $asg->classroom->capacity ?? '30' }} Öğrenci</span>
                        <a href="{{ route('teacher.attendance', ['session_id' => 1]) }}" class="text-xs font-bold text-primary hover:underline">
                            Yoklama Listesini Aç &rarr;
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-center text-xs text-neutral-400 py-6">Atanmış aktif sınıf bulunamadı.</div>
            @endforelse
        </div>
    </div>
@endsection
