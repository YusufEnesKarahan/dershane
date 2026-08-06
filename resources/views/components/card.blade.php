@props([
    'variant' => 'info', // feature, info, stat, team, blog, course
    'title' => null,
    'subtitle' => null,
    'footer' => null,
])

@php
    $cardClasses = 'bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 rounded-xl shadow-sm overflow-hidden transition-all duration-300';
    
    // Add dynamic classes based on variant card types
    $variantClasses = [
        'feature' => 'hover:-translate-y-1 hover:shadow-md bg-gradient-to-br from-white to-neutral-50 dark:from-neutral-900 dark:to-neutral-950',
        'info' => 'bg-white dark:bg-neutral-900',
        'stat' => 'p-6 bg-white dark:bg-neutral-900 flex flex-col justify-between border-l-4 border-l-blue-600 dark:border-l-blue-500',
        'team' => 'text-center p-6 bg-white dark:bg-neutral-900 hover:shadow-md',
        'blog' => 'flex flex-col hover:shadow-md cursor-pointer',
        'course' => 'flex flex-col hover:shadow-md hover:border-blue-500/30',
    ];

    $classes = $cardClasses . ' ' . ($variantClasses[$variant] ?? $variantClasses['info']);
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    <!-- Card Header / Banner for Blog and Course -->
    @if (isset($image))
        <div class="aspect-video w-full overflow-hidden bg-neutral-100 dark:bg-neutral-800 relative">
            {{ $image }}
        </div>
    @endif

    <div class="p-6">
        @if ($title || $subtitle)
            <div class="mb-4">
                @if ($title)
                    <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100 tracking-tight">
                        {{ $title }}
                    </h3>
                @endif
                @if ($subtitle)
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 font-sans mt-0.5">
                        {{ $subtitle }}
                    </p>
                @endif
            </div>
        @endif

        <div class="text-sm font-sans text-neutral-700 dark:text-neutral-300 leading-relaxed">
            {{ $slot }}
        </div>
    </div>

    @if ($footer)
        <div class="px-6 py-4 bg-neutral-50 dark:bg-neutral-850 border-t border-neutral-100 dark:border-neutral-800 flex items-center justify-between text-xs font-sans text-neutral-600 dark:text-neutral-400">
            {{ $footer }}
        </div>
    @endif
</div>
