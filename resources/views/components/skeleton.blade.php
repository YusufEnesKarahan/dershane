@props([
    'type' => 'card', // text, avatar, card, table
])

@php
    $baseClass = 'animate-pulse bg-neutral-200/80 rounded-premium-sm';
    
    $types = [
        'text' => 'h-4 w-3/4',
        'avatar' => 'h-12 w-12 rounded-full',
        'card' => 'h-48 w-full rounded-premium-xl',
        'table' => 'h-10 w-full rounded-premium-md',
    ];

    $classes = $baseClass . ' ' . ($types[$type] ?? $types['text']);
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}></div>
