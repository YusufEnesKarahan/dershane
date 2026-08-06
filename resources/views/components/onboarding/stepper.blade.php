@props(['currentStep' => 1, 'progress' => []])

@php
    $steps = [
        1 => ['title' => 'Kurum Bilgileri', 'route' => 'admin.onboarding.profile', 'key' => 'institution_profile_completed'],
        2 => ['title' => 'Akademik Yıl', 'route' => 'admin.onboarding.academic-year', 'key' => 'academic_year_created'],
        3 => ['title' => 'Öğretmen', 'route' => 'admin.onboarding.teacher', 'key' => 'teacher_added'],
        4 => ['title' => 'Sınıf', 'route' => 'admin.onboarding.classroom', 'key' => 'classroom_created'],
    ];
@endphp

<div class="mb-8 bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
    <div class="flex items-center justify-between mb-4">
        <div>
            <span class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wider">Kurum Kurulum Sihirbazı</span>
            <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100 mt-0.5">Adım {{ $currentStep }} / 4: {{ $steps[$currentStep]['title'] ?? '' }}</h2>
        </div>
        <div class="text-right">
            <span class="text-2xl font-black text-blue-600 dark:text-blue-400">%{{ $progress['percentage'] ?? 0 }}</span>
            <span class="block text-xs text-slate-500 dark:text-slate-400 font-medium">Tamamlandı</span>
        </div>
    </div>

    <!-- Progress Bar -->
    <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2.5 mb-6 overflow-hidden">
        <div class="bg-blue-600 dark:bg-blue-500 h-2.5 rounded-full transition-all duration-500 ease-out" style="width: {{ $progress['percentage'] ?? 0 }}%"></div>
    </div>

    <!-- Stepper Item List -->
    <nav class="grid grid-cols-1 sm:grid-cols-4 gap-3">
        @foreach($steps as $num => $s)
            @php
                $isCompleted = isset($progress['checklists'][$s['key']]) && $progress['checklists'][$s['key']];
                $isCurrent = $currentStep == $num;
            @endphp
            <a href="{{ route($s['route']) }}" 
               class="flex items-center p-3 rounded-xl border text-xs font-semibold transition-all {{ $isCurrent ? 'bg-blue-50 dark:bg-blue-950/40 border-blue-300 dark:border-blue-800 text-blue-900 dark:text-blue-200 shadow-sm' : ($isCompleted ? 'bg-emerald-50/70 dark:bg-emerald-950/30 border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300' : 'bg-slate-50 dark:bg-slate-800/40 border-slate-200/80 dark:border-slate-800 text-slate-400 dark:text-slate-500') }}">
                <span class="w-6 h-6 rounded-full flex items-center justify-center mr-2.5 text-xs font-bold {{ $isCurrent ? 'bg-blue-600 text-white' : ($isCompleted ? 'bg-emerald-500 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-400') }}">
                    @if($isCompleted && !$isCurrent)
                        ✓
                    @else
                        {{ $num }}
                    @endif
                </span>
                <span class="truncate">{{ $s['title'] }}</span>
            </a>
        @endforeach
    </nav>
</div>
