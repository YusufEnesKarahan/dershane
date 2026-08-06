@props([
    'type' => 'button',
    'variant' => 'primary', // primary, secondary, outline, ghost, danger, warning, success, info, link
    'size' => 'md', // sm, md, lg
    'disabled' => false,
    'loading' => false,
])

@php
    $baseStyles = 'inline-flex items-center justify-center font-sans font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-slate-900 disabled:opacity-50 disabled:pointer-events-none cursor-pointer active:scale-[0.98]';

    $variants = [
        'primary' => 'text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-500 shadow-sm border border-transparent focus:ring-blue-500',
        'secondary' => 'text-slate-800 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 border border-transparent focus:ring-slate-400',
        'outline' => 'text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 border border-slate-300 dark:border-slate-700 shadow-sm focus:ring-blue-500',
        'ghost' => 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 focus:ring-slate-400',
        'danger' => 'text-white bg-red-600 hover:bg-red-700 dark:bg-red-600 dark:hover:bg-red-500 shadow-sm border border-transparent focus:ring-red-500',
        'warning' => 'text-white bg-amber-600 hover:bg-amber-700 dark:bg-amber-600 dark:hover:bg-amber-500 shadow-sm border border-transparent focus:ring-amber-500',
        'success' => 'text-white bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-600 dark:hover:bg-emerald-500 shadow-sm border border-transparent focus:ring-emerald-500',
        'info' => 'text-white bg-cyan-600 hover:bg-cyan-700 dark:bg-cyan-600 dark:hover:bg-cyan-500 shadow-sm border border-transparent focus:ring-cyan-500',
        'link' => 'text-blue-600 dark:text-blue-400 hover:underline bg-transparent p-0 border-transparent shadow-none focus:ring-0 focus:ring-offset-0',
    ];

    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs rounded-lg gap-1.5',
        'md' => 'px-4 py-2 text-sm rounded-lg gap-2',
        'lg' => 'px-6 py-2.5 text-base rounded-xl gap-2.5',
    ];

    $classes = $baseStyles . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }} @if($disabled || $loading) disabled @endif>
    @if ($loading)
        <i data-lucide="loader-2" class="animate-spin -ml-1 mr-2 h-4 w-4 text-current"></i>
    @endif
    {{ $slot }}
</button>
