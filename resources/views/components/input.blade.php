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

    $inputBase = 'block w-full text-sm font-sans rounded-premium-md shadow-premium-sm border transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-1 disabled:bg-neutral-50 disabled:text-neutral/40';
    
    $inputStates = $hasError 
        ? 'border-danger text-danger-800 placeholder-danger/40 focus:ring-danger focus:border-danger' 
        : 'border-neutral-200 text-neutral placeholder-neutral/30 focus:ring-primary focus:border-primary';

    $classes = $inputBase . ' ' . $inputStates;
@endphp

<div class="space-y-1">
    @if ($label)
        <label for="input_{{ $name }}" class="block text-xs font-semibold text-neutral/80 select-none">
            {{ $label }}
            @if ($required)
                <span class="text-danger" aria-hidden="true">*</span>
            @endif
        </label>
    @endif

    <div class="relative rounded-premium-md">
        @if ($type === 'textarea')
            <textarea name="{{ $name }}" 
                      id="input_{{ $name }}" 
                      placeholder="{{ $placeholder }}"
                      @if($required) required @endif
                      {{ $attributes->merge(['class' => $classes, 'rows' => 3]) }}>{{ old($name, $value) }}</textarea>
        @elseif ($type === 'select')
            <select name="{{ $name }}" 
                    id="input_{{ $name }}" 
                    @if($required) required @endif
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
                   {{ $attributes->merge(['class' => $classes]) }}>
        @endif
    </div>

    @if ($hasError)
        <p class="text-xs text-danger font-medium flex items-center gap-1 select-none" id="error_{{ $name }}" role="alert">
            <svg class="h-3.5 w-3.5 text-danger shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            {{ $errorMessage }}
        </p>
    @elseif ($hint)
        <p class="text-xs text-neutral/50 font-sans select-none">{{ $hint }}</p>
    @endif
</div>
