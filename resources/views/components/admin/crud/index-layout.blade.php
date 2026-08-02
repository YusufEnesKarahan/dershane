@props(['title', 'description' => null])
<div class="space-y-6">
    <!-- Header Area -->
    <div class="bg-gradient-to-br from-emerald-900 via-slate-900 to-black p-6 rounded-2xl text-white shadow-lg flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border border-emerald-950 relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjIiIGZpbGw9IiNmZmZmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSIvPjwvc3ZnPg==')] opacity-50"></div>
        <div class="relative z-10">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] uppercase font-bold tracking-widest bg-emerald-500/20 text-emerald-300 rounded-full border border-emerald-500/30 mb-2">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                Module
            </span>
            <h1 class="text-xl font-black text-white flex items-center gap-2 tracking-tight">{{ $title }}</h1>
            @if($description)
                <p class="text-xs text-emerald-100/80 mt-1.5 font-medium max-w-xl">{{ $description }}</p>
            @endif
        </div>
        <div class="relative z-10 mt-4 sm:mt-0 flex flex-wrap items-center gap-2">
            {{ $actions ?? '' }}
        </div>
    </div>

    <!-- Filters & Search Area (if any) -->
    @if(isset($filters) || isset($search))
        <div class="bg-white dark:bg-neutral-900 p-4 rounded-xl border border-neutral-100 dark:border-neutral-800 shadow-sm flex flex-col md:flex-row gap-4 items-center justify-between">
            <div class="flex-1 w-full flex items-center gap-3">
                @if(isset($search))
                    {{ $search }}
                @else
                    <!-- Default Search Input if just $filters is used, or maybe $search slot is explicit -->
                @endif
                {{ $filters ?? '' }}
            </div>
        </div>
    @endif

    <!-- Content / Table Area -->
    <div>
        {{ $slot }}
    </div>
</div>