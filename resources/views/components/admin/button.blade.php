@props([
    'type' => 'button',
    'variant' => 'primary', // primary, secondary, danger, success, ghost
    'size' => 'md', // sm, md, lg
    'icon' => null,
    'loading' => false,
    'disabled' => false,
    'fullWidth' => false,
    'href' => null
])

@php
    $baseClasses = "inline-flex items-center justify-center font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-slate-900 disabled:opacity-60 disabled:cursor-not-allowed";
    
    $sizeClasses = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-6 py-3 text-base',
    ][$size];

    $variantClasses = [
        'primary' => 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500 shadow-sm hover:shadow',
        'secondary' => 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 focus:ring-slate-500',
        'danger' => 'bg-rose-600 text-white hover:bg-rose-700 focus:ring-rose-500 shadow-sm hover:shadow',
        'success' => 'bg-emerald-600 text-white hover:bg-emerald-700 focus:ring-emerald-500 shadow-sm hover:shadow',
        'ghost' => 'bg-transparent text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white',
    ][$variant];

    $classes = "{$baseClasses} {$sizeClasses} {$variantClasses} " . ($fullWidth ? 'w-full' : '');
    
    // x-data for loading state
    $alpineData = "x-data=\"{ loading: false }\" @submit.prevent=\"loading = true; \$el.closest('form').submit()\"";
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)
            <svg class="w-4 h-4 mr-1.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}" /></svg>
        @endif
        {{ $slot }}
    </a>
@else
    <button 
        type="{{ $type }}" 
        {{ $attributes->merge(['class' => $classes]) }} 
        @if($disabled) disabled @endif
        @if($type === 'submit')
            x-data="{ loading: false }" 
            x-on:click="setTimeout(() => { if($el.closest('form').checkValidity()) loading = true }, 50)"
        @endif
    >
        <!-- Spinner -->
        @if($type === 'submit')
            <svg x-show="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" x-cloak>
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        @endif
        
        <!-- Icon -->
        @if($icon)
            <svg @if($type === 'submit') x-show="!loading" @endif class="w-4 h-4 mr-1.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}" />
            </svg>
        @endif

        <span @if($type === 'submit') :class="{ 'opacity-80': loading }" @endif>
            {{ $slot }}
        </span>
    </button>
@endif
