@props([
    'cols' => '3', // 2, 3, 4
])

@php
    $colsClasses = [
        '2' => 'grid-cols-1 md:grid-cols-2',
        '3' => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3',
        '4' => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4',
    ];

    $classes = 'grid gap-6 sm:gap-8 w-full ' . ($colsClasses[$cols] ?? $colsClasses['3']);
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div>
