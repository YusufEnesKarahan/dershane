@props(['title', 'description' => null, 'backRoute' => null])
<div class="space-y-6">
    <div class="bg-gradient-to-br from-emerald-900 via-slate-900 to-black p-6 rounded-2xl text-white shadow-lg flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border border-emerald-950 relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjIiIGZpbGw9IiNmZmZmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSIvPjwvc3ZnPg==')] opacity-50"></div>
        <div class="relative z-10 flex flex-col">
            <div class="flex items-center gap-3">
                @if($backRoute)
                    <a href="{{ $backRoute }}" class="p-2 -ml-2 rounded-xl text-emerald-100/70 hover:text-white hover:bg-white/10 transition-colors backdrop-blur-sm">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    </a>
                @endif
                <div class="flex flex-col">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] uppercase font-bold tracking-widest bg-emerald-500/20 text-emerald-300 rounded-full border border-emerald-500/30 mb-1 w-max">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        Data Entry
                    </span>
                    <h1 class="text-xl font-black text-white flex items-center gap-2 tracking-tight">{{ $title }}</h1>
                </div>
            </div>
            @if($description)
                <p class="mt-1.5 text-xs text-emerald-100/80 font-medium max-w-xl {{ $backRoute ? 'pl-11' : '' }}">{{ $description }}</p>
            @endif
        </div>
        <div class="relative z-10 mt-4 sm:mt-0 flex flex-wrap items-center gap-2">
            {{ $actions ?? '' }}
        </div>
    </div>
    
    <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm overflow-hidden">
        <div class="p-6 sm:p-8">
            {{ $slot }}
        </div>
    </div>
</div>
