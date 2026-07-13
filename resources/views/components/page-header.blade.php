@props([
    'title',
    'breadcrumbs' => [],
])

<div {{ $attributes->merge(['class' => 'bg-gradient-to-br from-neutral-900 via-neutral-800 to-primary/20 text-white py-12 relative overflow-hidden']) }}>
    <!-- Grid pattern overlay -->
    <div class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff02_1px,transparent_1px),linear-gradient(to_bottom,#ffffff02_1px,transparent_1px)] bg-[size:24px_24px]" aria-hidden="true"></div>

    <x-container class="relative z-10 flex flex-col gap-3">
        <!-- Render Breadcrumbs if items present -->
        @if (!empty($breadcrumbs))
            @breadcrumbs($breadcrumbs)
        @endif

        <h1 class="text-2xl sm:text-4xl font-display font-extrabold tracking-tight">
            {{ $title }}
        </h1>
    </x-container>
</div>
