@props([
    'type' => 'info', // success, warning, danger, error, info
    'dismissible' => false,
])

@php
    $type = $type === 'error' ? 'danger' : $type;
    $baseClasses = 'p-4 rounded-xl border text-sm font-sans flex items-start gap-3 shadow-sm transition-all duration-200';
    
    $types = [
        'success' => 'bg-emerald-50 text-emerald-900 border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-200 dark:border-emerald-800',
        'warning' => 'bg-amber-50 text-amber-900 border-amber-200 dark:bg-amber-950/50 dark:text-amber-200 dark:border-amber-800',
        'danger' => 'bg-red-50 text-red-900 border-red-200 dark:bg-red-950/50 dark:text-red-200 dark:border-red-800',
        'info' => 'bg-blue-50 text-blue-900 border-blue-200 dark:bg-blue-950/50 dark:text-blue-200 dark:border-blue-800',
    ];

    $icons = [
        'success' => '<i data-lucide="check-circle" class="h-5 w-5 text-emerald-600 dark:text-emerald-400 shrink-0"></i>',
        'warning' => '<i data-lucide="alert-triangle" class="h-5 w-5 text-amber-600 dark:text-amber-400 shrink-0"></i>',
        'danger' => '<i data-lucide="x-circle" class="h-5 w-5 text-red-600 dark:text-red-400 shrink-0"></i>',
        'info' => '<i data-lucide="info" class="h-5 w-5 text-blue-600 dark:text-blue-400 shrink-0"></i>',
    ];

    $classes = $baseClasses . ' ' . ($types[$type] ?? $types['info']);
@endphp

<div x-data="{ show: true }" x-show="show" {{ $attributes->merge(['class' => $classes]) }} role="alert" aria-live="polite">
    <!-- Icon -->
    {!! $icons[$type] ?? $icons['info'] !!}

    <div class="flex-grow leading-normal">
        {{ $slot }}
    </div>

    @if ($dismissible)
        <button type="button" @click="show = false" class="text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 focus:outline-none transition-colors duration-150 p-0.5 rounded" aria-label="Kapat">
            <i data-lucide="x" class="h-5 w-5"></i>
        </button>
    @endif
</div>
