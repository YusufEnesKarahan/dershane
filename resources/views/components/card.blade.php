@props([
    'title' => null,
    'footer' => null,
])

<div {{ $attributes->merge(['class' => 'bg-white overflow-hidden shadow rounded-lg border border-gray-200']) }}>
    @if ($title)
        <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $title }}</h3>
        </div>
    @endif

    <div class="px-4 py-5 sm:p-6">
        {{ $slot }}
    </div>

    @if ($footer)
        <div class="px-4 py-4 bg-gray-50 border-t border-gray-200 sm:px-6">
            {{ $footer }}
        </div>
    @endif
</div>
