@props([
    'title', 
    'value', 
    'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',
    'trend' => null, 
    'color' => 'primary',
    'trendText' => 'vs geçen ay'
])

@php
    $colors = [
        'primary' => ['bg' => 'bg-blue-50 dark:bg-blue-950/30', 'text' => 'text-blue-600 dark:text-blue-400'],
        'green' => ['bg' => 'bg-emerald-50 dark:bg-emerald-950/30', 'text' => 'text-emerald-600 dark:text-emerald-400'],
        'blue' => ['bg' => 'bg-blue-50 dark:bg-blue-950/30', 'text' => 'text-blue-600 dark:text-blue-400'],
        'amber' => ['bg' => 'bg-amber-50 dark:bg-amber-950/30', 'text' => 'text-amber-600 dark:text-amber-400'],
        'rose' => ['bg' => 'bg-rose-50 dark:bg-rose-950/30', 'text' => 'text-rose-600 dark:text-rose-400'],
    ];

    $colorClass = $colors[$color] ?? $colors['primary'];
@endphp

<div class="bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow duration-300 group">
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400 group-hover:text-slate-700 dark:group-hover:text-slate-300 transition-colors">
            {{ $title }}
        </h3>
        <div class="p-2 rounded-lg {{ $colorClass['bg'] }} {{ $colorClass['text'] }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                <path d="{{ $icon }}"></path>
            </svg>
        </div>
    </div>
    
    <div class="flex flex-col gap-1">
        <div class="text-2xl font-semibold text-slate-900 dark:text-white tracking-tight">
            {{ $value }}
        </div>
        
        @if($trend !== null)
            <div class="flex items-center gap-2 mt-1">
                @if($trend > 0)
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 text-xs font-medium">
                        <svg class="w-3 h-3 mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" /></svg>
                        {{ $trend }}%
                    </span>
                @elseif($trend < 0)
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-md bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 text-xs font-medium">
                        <svg class="w-3 h-3 mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" /></svg>
                        {{ abs($trend) }}%
                    </span>
                @else
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-xs font-medium">
                        0%
                    </span>
                @endif
                <span class="text-xs text-slate-400 dark:text-slate-500">{{ $trendText }}</span>
            </div>
        @endif
    </div>
</div>