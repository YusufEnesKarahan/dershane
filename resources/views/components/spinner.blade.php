@props([
    'size' => 'md', // sm, md, lg, xl
    'color' => 'primary', // primary, secondary, current, white
])

@php
    $sizes = [
        'sm' => 'h-4 w-4',
        'md' => 'h-6 w-6',
        'lg' => 'h-8 w-8',
        'xl' => 'h-12 w-12',
    ];

    $colors = [
        'primary' => 'text-blue-600 dark:text-blue-400',
        'secondary' => 'text-neutral-500 dark:text-neutral-400',
        'current' => 'text-current',
        'white' => 'text-white',
    ];

    $classes = 'animate-spin ' . ($sizes[$size] ?? $sizes['md']) . ' ' . ($colors[$color] ?? $colors['primary']);
@endphp

<svg {{ $attributes->merge(['class' => $classes]) }} fill="none" viewBox="0 0 24 24" role="status" aria-label="Yükleniyor">
    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
</svg>
