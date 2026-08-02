@props([
    'title', 
    'value', 
    'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6', // Default to trending-up chart
    'trend' => null, 
    'color' => 'primary',
    'trendText' => 'vs geçen ay'
])

@php
    $colors = [
        'primary' => ['bg' => 'bg-primary/10', 'text' => 'text-primary', 'darkBg' => 'dark:bg-primary/20'],
        'green' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'darkBg' => 'dark:bg-emerald-900/30'],
        'blue' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-600', 'darkBg' => 'dark:bg-blue-900/30'],
        'amber' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'darkBg' => 'dark:bg-amber-900/30'],
        'purple' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-600', 'darkBg' => 'dark:bg-purple-900/30'],
        'rose' => ['bg' => 'bg-rose-100', 'text' => 'text-rose-600', 'darkBg' => 'dark:bg-rose-900/30'],
    ];

    $colorClass = $colors[$color] ?? $colors['primary'];
@endphp

<div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm hover:shadow-md transition-shadow duration-300 group">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-medium text-neutral-500 dark:text-neutral-400 group-hover:text-neutral-700 dark:group-hover:text-neutral-300 transition-colors">
            {{ $title }}
        </h3>
        <div class="p-2.5 rounded-xl {{ $colorClass['bg'] }} {{ $colorClass['darkBg'] }} {{ $colorClass['text'] }}">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="{{ $icon }}"></path>
            </svg>
        </div>
    </div>
    
    <div class="flex flex-col gap-1">
        <div class="text-3xl font-display font-bold text-neutral-900 dark:text-white tracking-tight">
            {{ $value }}
        </div>
        
        @if($trend !== null)
            <div class="flex items-center gap-2 mt-1">
                @if($trend > 0)
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 text-xs font-semibold">
                        <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" /></svg>
                        {{ $trend }}%
                    </span>
                @elseif($trend < 0)
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-md bg-rose-50 dark:bg-rose-900/30 text-rose-600 text-xs font-semibold">
                        <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" /></svg>
                        {{ abs($trend) }}%
                    </span>
                @else
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-md bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400 text-xs font-semibold">
                        <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14" /></svg>
                        0%
                    </span>
                @endif
                <span class="text-xs text-neutral-400 dark:text-neutral-500">{{ $trendText }}</span>
            </div>
        @endif
    </div>
</div>