@props([
    'align' => 'right', // left, right
    'width' => '48', // 48, 64
])

@php
    $alignmentClasses = match ($align) {
        'left' => 'origin-top-left left-0',
        default => 'origin-top-right right-0',
    };

    $widths = [
        '48' => 'w-48',
        '64' => 'w-64',
    ];

    $widthClass = $widths[$width] ?? $widths['48'];
@endphp

<div class="relative" x-data="{ open: false }" @click.away="open = false" @close.stop="open = false">
    <div @click="open = ! open">
        {{ $trigger }}
    </div>

    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute z-50 mt-2 {{ $widthClass }} rounded-premium-lg shadow-premium-lg border border-neutral-100 bg-white {{ $alignmentClasses }}"
         style="display: none;"
         @click="open = false">
        <div class="py-1">
            {{ $content }}
        </div>
    </div>
</div>
