@props([
    'title' => 'Erken Kayıt Fırsatlarını Kaçırmayın!',
    'subtitle' => 'Online ön kaydınızı tamamlayarak indirimli kayıt dönemi avantajlarından hemen yararlanın.',
])

<div {{ $attributes->merge(['class' => 'bg-gradient-to-br from-neutral-900 via-neutral-800 to-primary/30 text-white rounded-premium-2xl p-8 sm:p-12 relative overflow-hidden border border-neutral-800 shadow-premium-xl']) }}>
    <!-- Subtle grid overlay -->
    <div class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff03_1px,transparent_1px),linear-gradient(to_bottom,#ffffff03_1px,transparent_1px)] bg-[size:24px_24px]" aria-hidden="true"></div>

    <div class="max-w-3xl relative z-10 space-y-6">
        <h3 class="text-2xl sm:text-4xl font-display font-extrabold tracking-tight">
            {{ $title }}
        </h3>
        <p class="text-xs sm:text-sm text-neutral-300 font-sans leading-relaxed max-w-xl">
            {{ $subtitle }}
        </p>
        <div class="pt-2 flex flex-wrap gap-3">
            {{ $slot }}
        </div>
    </div>
</div>
