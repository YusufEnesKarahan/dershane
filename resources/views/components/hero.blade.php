@props([
    'title' => 'Geleceğin Eğitim Platformu',
    'subtitle' => 'Modern, hızlı ve kurumsal yapısıyla dershane ve kurs merkezleri için tasarlanmış hepsi-bir-arada yönetim ERP sistemi.',
])

<div class="bg-gradient-to-br from-neutral-900 via-neutral-800 to-primary/20 text-white py-24 px-6 relative overflow-hidden">
    <!-- Subtle grid overlay -->
    <div class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff03_1px,transparent_1px),linear-gradient(to_bottom,#ffffff03_1px,transparent_1px)] bg-[size:24px_24px]" aria-hidden="true"></div>

    <div class="max-w-4xl mx-auto text-center relative z-10 space-y-6">
        <h1 class="text-4xl sm:text-6xl font-display font-extrabold tracking-tight leading-none bg-clip-text text-transparent bg-gradient-to-r from-white via-white to-neutral-200">
            {{ $title }}
        </h1>
        <p class="text-base sm:text-lg text-neutral-300 font-sans max-w-2xl mx-auto leading-relaxed">
            {{ $subtitle }}
        </p>
        <div class="pt-4 flex flex-wrap justify-center gap-4">
            {{ $slot }}
        </div>
    </div>
</div>
