@props([
    'title' => 'Henüz Kayıt Bulunmuyor',
    'subtitle' => 'Sistemde henüz eklenmiş veri bulunamadı veya arama kriterlerinize uyan kayıt yok.',
    'icon' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center text-center p-8 sm:p-12 border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-2xl bg-white/50 dark:bg-slate-900/50 text-slate-800 dark:text-slate-200 font-sans']) }}>
    <!-- Search / empty icon placeholder -->
    <div class="h-14 w-14 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4 text-slate-400 dark:text-slate-500 shadow-inner">
        @if ($icon)
            @if(str_contains($icon, '<svg') || str_contains($icon, '<i'))
                {{ $icon }}
            @else
                <i data-lucide="{{ $icon }}" class="h-7 w-7"></i>
            @endif
        @else
            <i data-lucide="search" class="h-7 w-7"></i>
        @endif
    </div>
    
    <h4 class="text-base font-semibold text-slate-900 dark:text-slate-100 tracking-tight">
        {{ $title }}
    </h4>
    <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mt-1.5 leading-relaxed">
        {{ $subtitle }}
    </p>
    
    @if (isset($actions))
        <div class="mt-6 flex flex-wrap gap-2 justify-center">
            {{ $actions }}
        </div>
    @endif
</div>
