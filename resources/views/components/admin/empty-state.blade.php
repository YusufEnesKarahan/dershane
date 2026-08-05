@props([
    'title' => 'Henüz kayıt bulunmuyor',
    'message' => 'Burada görüntülenecek herhangi bir veri bulunamadı.',
    'actionUrl' => null,
    'actionLabel' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center text-center p-8 sm:p-12 border border-dashed border-gray-300 rounded-lg bg-gray-50/50 text-gray-700 my-4']) }}>
    <div class="h-12 w-12 rounded-full bg-indigo-50 text-indigo-500 flex items-center justify-center mb-4">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
        </svg>
    </div>
    
    <h3 class="text-base font-semibold text-gray-900">
        {{ $title }}
    </h3>
    <p class="text-sm text-gray-500 max-w-sm mt-1">
        {{ $message }}
    </p>
    
    @if ($actionUrl && $actionLabel)
        <div class="mt-5">
            <a href="{{ $actionUrl }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ $actionLabel }}
            </a>
        </div>
    @elseif (isset($actions))
        <div class="mt-5 flex flex-wrap gap-2 justify-center">
            {{ $actions }}
        </div>
    @endif
</div>
