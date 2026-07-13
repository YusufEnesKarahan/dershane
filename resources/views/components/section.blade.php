@props([
    'bg' => 'white', // white, gray, dark
    'py' => '16', // 12, 16, 24
])

@php
    $bgClasses = [
        'white' => 'bg-white',
        'gray' => 'bg-neutral-50/50',
        'dark' => 'bg-neutral-900 text-white',
    ];

    $pyClasses = [
        '12' => 'py-12',
        '16' => 'py-16 sm:py-20',
        '24' => 'py-24 sm:py-32',
    ];

    $classes = 'relative overflow-hidden w-full ' . ($bgClasses[$bg] ?? $bgClasses['white']) . ' ' . ($pyClasses[$py] ?? $pyClasses['16']);
@endphp

<section {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</section>
