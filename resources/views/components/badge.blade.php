@props([
    'color' => 'primary', // primary, secondary, gray, red, green, yellow, blue
    'size' => 'md', // sm, md
])

@php
    $baseClasses = 'inline-flex items-center font-medium font-sans rounded-full border tracking-wide transition-colors duration-150';

    $colors = [
        'primary' => 'bg-primary/10 text-primary border-primary/20',
        'secondary' => 'bg-secondary/10 text-secondary border-secondary/20',
        'gray' => 'bg-neutral-100 text-neutral border-neutral-200/50',
        'red' => 'bg-danger/10 text-danger border-danger/20',
        'green' => 'bg-success/10 text-success border-success/20',
        'yellow' => 'bg-warning/10 text-warning border-warning/20',
        'blue' => 'bg-info/10 text-info border-info/20',
    ];

    $sizes = [
        'sm' => 'px-2 py-0.5 text-[10px]',
        'md' => 'px-2.5 py-0.5 text-xs',
    ];

    $classes = $baseClasses . ' ' . ($colors[$color] ?? $colors['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
