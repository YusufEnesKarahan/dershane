@props([
    'title' => 'Henüz Kayıt Bulunmuyor',
    'subtitle' => 'Sistemde henüz eklenmiş veri bulunamadı veya arama kriterlerinize uyan kayıt yok.',
    'icon' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center text-center p-8 sm:p-12 border-2 border-dashed border-neutral-200 dark:border-neutral-800 rounded-2xl bg-white/50 dark:bg-neutral-900/50 text-neutral-800 dark:text-neutral-200 font-sans']) }}>
    <!-- Search / empty icon placeholder -->
    <div class="h-14 w-14 rounded-2xl bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center mb-4 text-neutral-400 dark:text-neutral-500 shadow-inner">
        @if ($icon)
            {{ $icon }}
        @else
            <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        @endif
    </div>
    
    <h4 class="text-base font-semibold text-neutral-900 dark:text-neutral-100 tracking-tight">
        {{ $title }}
    </h4>
    <p class="text-xs text-neutral-500 dark:text-neutral-400 max-w-sm mt-1.5 leading-relaxed">
        {{ $subtitle }}
    </p>
    
    @if (isset($actions))
        <div class="mt-6 flex flex-wrap gap-2 justify-center">
            {{ $actions }}
        </div>
    @endif
</div>
