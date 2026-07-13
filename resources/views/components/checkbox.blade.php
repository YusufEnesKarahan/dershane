@props([
    'name',
    'label' => null,
    'type' => 'checkbox', // checkbox, radio, switch
    'checked' => false,
    'value' => '1',
    'hint' => null,
])

@php
    $wrapperClasses = 'flex items-start gap-3 select-none cursor-pointer';
    
    $inputClasses = $type === 'radio'
        ? 'h-4 w-4 rounded-full border-neutral-300 text-primary focus:ring-primary transition-all duration-200'
        : ($type === 'switch' ? 'sr-only' : 'h-4 w-4 rounded border-neutral-300 text-primary focus:ring-primary transition-all duration-200');
@endphp

<label class="{{ $wrapperClasses }}">
    @if ($type === 'switch')
        <div class="relative flex items-center shrink-0 cursor-pointer" x-data="{ checked: {{ $checked ? 'true' : 'false' }} }">
            <input type="checkbox" 
                   name="{{ $name }}" 
                   value="{{ $value }}" 
                   class="sr-only" 
                   x-model="checked"
                   {{ $attributes }}>
            <div class="w-10 h-6 bg-neutral-200 rounded-full transition-colors duration-200" :class="checked ? 'bg-primary' : 'bg-neutral-200'"></div>
            <div class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform duration-200 shadow-premium-sm" :class="checked ? 'translate-x-4' : 'translate-x-0'"></div>
        </div>
    @else
        <input type="{{ $type === 'radio' ? 'radio' : 'checkbox' }}" 
               name="{{ $name }}" 
               value="{{ $value }}" 
               class="{{ $inputClasses }}" 
               @if($checked) checked @endif
               {{ $attributes }}>
    @endif

    @if ($label || $hint)
        <div class="text-sm font-sans leading-none">
            @if ($label)
                <span class="font-medium text-neutral">{{ $label }}</span>
            @endif
            @if ($hint)
                <p class="text-xs text-neutral/50 mt-1 leading-normal">{{ $hint }}</p>
            @endif
        </div>
    @endif
</label>
