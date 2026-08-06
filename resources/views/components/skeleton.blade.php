@props([
    'type' => 'card', // text, avatar, card, table, button
])

@php
    $baseClass = 'animate-pulse bg-slate-200 dark:bg-slate-800 rounded-lg';
    
    $types = [
        'text' => 'h-4 w-3/4',
        'avatar' => 'h-10 w-10 rounded-full',
        'card' => 'h-48 w-full rounded-xl',
        'table' => 'h-12 w-full rounded-lg',
        'button' => 'h-9 w-24 rounded-lg',
    ];

    $classes = $baseClass . ' ' . ($types[$type] ?? $types['text']);
@endphp

<div {{ $attributes->merge(['class' => $classes]) }} aria-hidden="true"></div>
