@props([
    'variant' => 'info', // feature, info, stat, team, blog, course
    'title' => null,
    'subtitle' => null,
    'footer' => null,
])

@php
    $cardClasses = 'bg-surface border border-neutral-100 rounded-premium-xl shadow-premium-md overflow-hidden transition-all duration-300';
    
    // Add dynamic classes based on variant card types
    $variantClasses = [
        'feature' => 'hover:-translate-y-1 hover:shadow-premium-lg bg-gradient-to-br from-white to-neutral-50/20',
        'info' => 'bg-white',
        'stat' => 'p-6 bg-white flex flex-col justify-between border-l-4 border-l-primary',
        'team' => 'text-center p-6 bg-white hover:shadow-premium-lg',
        'blog' => 'flex flex-col hover:shadow-premium-lg cursor-pointer',
        'course' => 'flex flex-col hover:shadow-premium-lg hover:border-primary/20',
    ];

    $classes = $cardClasses . ' ' . ($variantClasses[$variant] ?? $variantClasses['info']);
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    <!-- Card Header / Banner for Blog and Course -->
    @if (isset($image))
        <div class="aspect-video w-full overflow-hidden bg-neutral-100 relative">
            {{ $image }}
        </div>
    @endif

    <div class="p-6">
        @if ($title || $subtitle)
            <div class="mb-4">
                @if ($title)
                    <h3 class="text-lg font-display font-semibold text-neutral tracking-tight">
                        {{ $title }}
                    </h3>
                @endif
                @if ($subtitle)
                    <p class="text-xs text-neutral/60 font-sans mt-0.5">
                        {{ $subtitle }}
                    </p>
                @endif
            </div>
        @endif

        <div class="text-sm font-sans text-neutral/80 leading-relaxed">
            {{ $slot }}
        </div>
    </div>

    @if ($footer)
        <div class="px-6 py-4 bg-neutral-50/50 border-t border-neutral-100 flex items-center justify-between text-xs font-sans">
            {{ $footer }}
        </div>
    @endif
</div>
