@props([
    'variant' => 'neutral', // neutral, primary, success, warning, danger, info
    'size' => 'md', // sm, md, lg
    'dot' => false
])

@php
    $baseClasses = "inline-flex items-center font-medium rounded-full";
    
    $sizeClasses = [
        'sm' => 'px-2 py-0.5 text-[10px] leading-4',
        'md' => 'px-2.5 py-0.5 text-xs',
        'lg' => 'px-3 py-1 text-sm',
    ][$size];

    $variantClasses = [
        'neutral' => 'bg-neutral-100 text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300',
        'primary' => 'bg-primary-50 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400',
        'success' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
        'warning' => 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
        'danger'  => 'bg-rose-50 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400',
        'info'    => 'bg-sky-50 text-sky-700 dark:bg-sky-900/30 dark:text-sky-400',
    ][$variant];

    $dotColors = [
        'neutral' => 'bg-neutral-500',
        'primary' => 'bg-primary-500',
        'success' => 'bg-emerald-500',
        'warning' => 'bg-amber-500',
        'danger'  => 'bg-rose-500',
        'info'    => 'bg-sky-500',
    ][$variant];
@endphp

<span {{ $attributes->merge(['class' => "{$baseClasses} {$sizeClasses} {$variantClasses}"]) }}>
    @if($dot)
        <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $dotColors }}"></span>
    @endif
    {{ $slot }}
</span>
