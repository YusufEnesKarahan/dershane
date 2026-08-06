@props([
    'title' => 'Henüz kayıt bulunmuyor',
    'message' => 'Burada görüntülenecek herhangi bir veri bulunamadı.',
    'actionUrl' => null,
    'actionLabel' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center text-center p-8 sm:p-12 border border-dashed border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50/50 dark:bg-slate-800/20 my-4']) }}>
    <div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 flex items-center justify-center mb-4">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
        </svg>
    </div>
    
    <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100">
        {{ $title }}
    </h3>
    <p class="text-sm text-slate-500 dark:text-slate-400 max-w-sm mt-1">
        {{ $message }}
    </p>
    
    @if ($actionUrl && $actionLabel)
        <div class="mt-5">
            <a href="{{ $actionUrl }}" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-slate-900 transition">
                {{ $actionLabel }}
            </a>
        </div>
    @elseif (isset($actions))
        <div class="mt-5 flex flex-wrap gap-2 justify-center">
            {{ $actions }}
        </div>
    @endif
</div>
