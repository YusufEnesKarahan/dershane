@props([
    'title' => 'Kayıt Bulunamadı',
    'subtitle' => 'Aradığınız kriterlere uygun herhangi bir veri bulunamadı.',
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center text-center p-8 sm:p-12 border border-dashed border-neutral-200 rounded-premium-2xl bg-white/50 text-neutral font-sans']) }}>
    <!-- Search / empty icon placeholder -->
    <div class="h-12 w-12 rounded-full bg-neutral-100 flex items-center justify-center mb-4 text-neutral/40">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
    </div>
    
    <h4 class="text-sm font-semibold text-neutral tracking-tight">
        {{ $title }}
    </h4>
    <p class="text-xs text-neutral/50 max-w-sm mt-1 leading-relaxed">
        {{ $subtitle }}
    </p>
    
    @if (isset($actions))
        <div class="mt-6 flex flex-wrap gap-2 justify-center">
            {{ $actions }}
        </div>
    @endif
</div>
