@props([
    'type' => 'button',
    'variant' => 'primary', // primary, secondary, outline, ghost, danger, success, link
    'size' => 'md', // sm, md, lg
    'disabled' => false,
    'loading' => false,
])

@php
    $baseStyles = 'inline-flex items-center justify-center font-sans font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none cursor-pointer';

    $variants = [
        'primary' => 'text-white bg-primary hover:bg-primary/95 shadow-premium-sm border border-transparent focus:ring-primary',
        'secondary' => 'text-neutral bg-neutral-100 hover:bg-neutral-200/80 border border-transparent focus:ring-neutral-200',
        'outline' => 'text-neutral bg-white hover:bg-neutral-50/50 border border-neutral-200 shadow-premium-sm focus:ring-primary',
        'ghost' => 'text-neutral hover:bg-neutral-100/60 focus:ring-neutral-200',
        'danger' => 'text-white bg-danger hover:bg-danger/95 shadow-premium-sm border border-transparent focus:ring-danger',
        'success' => 'text-white bg-success hover:bg-success/95 shadow-premium-sm border border-transparent focus:ring-success',
        'link' => 'text-primary hover:underline bg-transparent p-0 border-transparent shadow-none focus:ring-0 focus:ring-offset-0',
    ];

    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs rounded-premium-sm gap-1',
        'md' => 'px-4 py-2 text-sm rounded-premium-md gap-2',
        'lg' => 'px-6 py-3 text-base rounded-premium-lg gap-2',
    ];

    $classes = $baseStyles . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }} @if($disabled || $loading) disabled @endif>
    @if ($loading)
        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-current" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    @endif
    {{ $slot }}
</button>
