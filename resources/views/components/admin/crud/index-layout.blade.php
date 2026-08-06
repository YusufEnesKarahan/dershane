@props(['title', 'description' => null])
<div class="space-y-6">
    {{-- ─── Page Header ─── --}}
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5 sm:p-6 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-lg font-semibold text-slate-900 dark:text-slate-100 tracking-tight flex items-center gap-2">
                {{ $title }}
            </h1>
            @if($description)
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-xl">{{ $description }}</p>
            @endif
        </div>
        <div class="flex flex-wrap items-center gap-2 shrink-0">
            {{ $actions ?? '' }}
        </div>
    </div>

    {{-- ─── Filters & Search Area ─── --}}
    @if(isset($filters) || isset($search))
        <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col md:flex-row gap-4 items-center justify-between">
            <div class="flex-1 w-full flex items-center gap-3">
                @if(isset($search))
                    {{ $search }}
                @endif
                {{ $filters ?? '' }}
            </div>
        </div>
    @endif

    {{-- ─── Content / Table Area ─── --}}
    <div>
        {{ $slot }}
    </div>
</div>