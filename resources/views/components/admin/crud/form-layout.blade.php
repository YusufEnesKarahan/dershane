@props(['title', 'description' => null, 'backRoute' => null])
<div class="space-y-6">
    {{-- ─── Page Header ─── --}}
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5 sm:p-6 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            @if($backRoute)
                <a href="{{ $backRoute }}" class="p-2 -ml-2 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" aria-label="Geri">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                </a>
            @endif
            <div>
                <h1 class="text-lg font-semibold text-slate-900 dark:text-slate-100 tracking-tight">{{ $title }}</h1>
                @if($description)
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-xl">{{ $description }}</p>
                @endif
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2 shrink-0">
            {{ $actions ?? '' }}
        </div>
    </div>
    
    {{-- ─── Form Content ─── --}}
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-6 sm:p-8">
            {{ $slot }}
        </div>
    </div>
</div>
