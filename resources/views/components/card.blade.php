@props([
    'variant' => 'info', // feature, info, stat, team, blog, course
    'title' => null,
    'subtitle' => null,
    'footer' => null,
])

@php
    $cardClasses = 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden transition-all duration-300';
    
    // Add dynamic classes based on variant card types
    $variantClasses = [
        'feature' => 'hover:-translate-y-1 hover:shadow-md bg-gradient-to-br from-white to-slate-50 dark:from-slate-900 dark:to-slate-950',
        'info' => 'bg-white dark:bg-slate-900',
        'stat' => 'p-6 bg-white dark:bg-slate-900 flex flex-col justify-between border-l-4 border-l-blue-600 dark:border-l-blue-500',
        'team' => 'text-center p-6 bg-white dark:bg-slate-900 hover:shadow-md',
        'blog' => 'flex flex-col hover:shadow-md cursor-pointer',
        'course' => 'flex flex-col hover:shadow-md hover:border-blue-500/30',
    ];

    $classes = $cardClasses . ' ' . ($variantClasses[$variant] ?? $variantClasses['info']);
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    <!-- Card Header / Banner for Blog and Course -->
    @if (isset($image))
        <div class="aspect-video w-full overflow-hidden bg-slate-100 dark:bg-slate-800 relative">
            {{ $image }}
        </div>
    @endif

    <div class="p-6">
        @if ($title || $subtitle)
            <div class="mb-4">
                @if ($title)
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 tracking-tight">
                        {{ $title }}
                    </h3>
                @endif
                @if ($subtitle)
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-sans mt-0.5">
                        {{ $subtitle }}
                    </p>
                @endif
            </div>
        @endif

        <div class="text-sm font-sans text-slate-700 dark:text-slate-300 leading-relaxed">
            {{ $slot }}
        </div>
    </div>

    @if ($footer)
        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-850 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs font-sans text-slate-600 dark:text-slate-400">
            {{ $footer }}
        </div>
    @endif
</div>
