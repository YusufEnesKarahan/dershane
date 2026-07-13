@props([
    'type' => 'info', // success, warning, danger, info
    'dismissible' => false,
])

@php
    $baseClasses = 'p-4 rounded-premium-md border text-sm font-sans flex items-start gap-3 shadow-premium-sm transition-all duration-200';
    
    $types = [
        'success' => 'bg-green-50/50 text-green-800 border-green-100',
        'warning' => 'bg-yellow-50/50 text-yellow-800 border-yellow-100',
        'danger' => 'bg-red-50/50 text-red-800 border-red-100',
        'info' => 'bg-blue-50/50 text-blue-800 border-blue-100',
    ];

    $icons = [
        'success' => '<svg class="h-5 w-5 text-green-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
        'warning' => '<svg class="h-5 w-5 text-yellow-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
        'danger' => '<svg class="h-5 w-5 text-red-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
        'info' => '<svg class="h-5 w-5 text-blue-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
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
        <button type="button" @click="show = false" class="text-neutral/40 hover:text-neutral/60 focus:outline-none transition-colors duration-150" aria-label="Kapat">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    @endif
</div>
