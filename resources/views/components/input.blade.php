@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'hint' => null,
    'required' => false,
    'error' => null,
])

@php
    $hasError = $errors->has($name) || $error;
    $errorMessage = $errors->first($name) ?? $error;

    $inputBase = 'block w-full text-sm font-sans rounded-lg shadow-sm border transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-1 dark:focus:ring-offset-slate-900 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 disabled:bg-slate-100 dark:disabled:bg-slate-800 disabled:text-slate-400 dark:disabled:text-slate-600';
    
    $inputStates = $hasError 
        ? 'border-red-500 text-red-900 dark:text-red-300 placeholder-red-300 focus:ring-red-500 focus:border-red-500' 
        : 'border-slate-300 dark:border-slate-700 focus:ring-blue-500 focus:border-blue-500';

    $classes = $inputBase . ' ' . $inputStates;
@endphp

<div class="space-y-1">
    @if ($label)
        <label for="input_{{ $name }}" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 select-none">
            {{ $label }}
            @if ($required)
                <span class="text-red-500 dark:text-red-400" aria-hidden="true">*</span>
            @endif
        </label>
    @endif

    <div class="relative rounded-lg">
        @if ($type === 'textarea')
            <textarea name="{{ $name }}" 
                      id="input_{{ $name }}" 
                      placeholder="{{ $placeholder }}"
                      @if($required) required @endif
                      @if($hasError) aria-invalid="true" aria-describedby="error_{{ $name }}" @endif
                      {{ $attributes->merge(['class' => $classes, 'rows' => 3]) }}>{{ old($name, $value) }}</textarea>
        @elseif ($type === 'select')
            <select name="{{ $name }}" 
                    id="input_{{ $name }}" 
                    @if($required) required @endif
                    @if($hasError) aria-invalid="true" aria-describedby="error_{{ $name }}" @endif
                    {{ $attributes->merge(['class' => $classes]) }}>
                {{ $slot }}
            </select>
        @else
            <input type="{{ $type }}" 
                   name="{{ $name }}" 
                   id="input_{{ $name }}" 
                   value="{{ old($name, $value) }}"
                   placeholder="{{ $placeholder }}"
                   @if($required) required @endif
                   @if($hasError) aria-invalid="true" aria-describedby="error_{{ $name }}" @endif
                   {{ $attributes->merge(['class' => $classes]) }}>
        @endif
    </div>

    @if ($hasError)
        <p class="text-xs text-red-600 dark:text-red-400 font-medium flex items-center gap-1 select-none" id="error_{{ $name }}" role="alert">
            <i data-lucide="circle-alert" class="h-3.5 w-3.5 text-red-500 shrink-0"></i>
            {{ $errorMessage }}
        </p>
    @elseif ($hint)
        <p class="text-xs text-slate-500 dark:text-slate-400 font-sans select-none">{{ $hint }}</p>
    @endif
</div>
