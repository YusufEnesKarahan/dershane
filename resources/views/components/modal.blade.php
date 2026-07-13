@props([
    'name',
    'title' => null,
    'size' => 'md', // sm, md, lg, xl
])

@php
    $sizes = [
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
    ];

    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

<div x-data="{ open: false }"
     x-show="open"
     x-on:open-modal.window="if ($event.detail.name === '{{ $name }}') open = true"
     x-on:close-modal.window="if ($event.detail.name === '{{ $name }}') open = false"
     x-on:keydown.escape.window="open = false"
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;"
     role="dialog"
     aria-modal="true"
     aria-labelledby="modal_title_{{ $name }}">
    
    <!-- Backdrop Overlay -->
    <div class="fixed inset-0 bg-neutral-900/40 backdrop-blur-sm transition-opacity duration-300" 
         x-show="open"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="open = false"></div>

    <!-- Modal Wrapper -->
    <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
        <div class="relative bg-white rounded-premium-xl text-left overflow-hidden shadow-premium-xl transform transition-all sm:my-8 sm:align-middle w-full {{ $sizeClass }}"
             x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            
            <!-- Header -->
            <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between bg-neutral-50/50">
                <h3 class="text-base font-display font-semibold text-neutral tracking-tight" id="modal_title_{{ $name }}">
                    {{ $title ?? 'İşlem Penceresi' }}
                </h3>
                <button type="button" @click="open = false" class="text-neutral/40 hover:text-neutral/60 focus:outline-none transition-colors duration-150" aria-label="Kapat">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <div class="px-6 py-5 text-sm font-sans text-neutral/80 leading-relaxed bg-white">
                {{ $slot }}
            </div>

            <!-- Footer -->
            @if (isset($footer))
                <div class="px-6 py-4 border-t border-neutral-100 bg-neutral-50/50 flex justify-end gap-2">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>
