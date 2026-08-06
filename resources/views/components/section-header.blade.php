@props([
    'title',
    'subtitle' => null,
    'align' => 'center', // center, left
])

@php
    $alignClasses = $align === 'left' ? 'text-left' : 'text-center mx-auto';
    $subtitleColor = $attributes->get('dark') ? 'text-slate-400' : 'text-slate/60';
    $titleColor = $attributes->get('dark') ? 'text-white' : 'text-slate';
@endphp

<div {{ $attributes->merge(['class' => 'max-w-3xl space-y-3 mb-12 sm:mb-16 ' . $alignClasses]) }}>
    <h2 class="text-2xl sm:text-4xl font-display font-bold tracking-tight {{ $titleColor }}">
        {{ $title }}
    </h2>
    @if ($subtitle)
        <p class="text-sm sm:text-base leading-relaxed {{ $subtitleColor }}">
            {{ $subtitle }}
        </p>
    @endif
</div>
