@props([
    'name',
    'title' => null,
    'size' => 'md', // sm, md, lg, xl, 2xl, 3xl, 4xl, full
])

@php
    $sizes = [
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
        '3xl' => 'sm:max-w-3xl',
        '4xl' => 'sm:max-w-4xl',
        'full' => 'sm:max-w-full sm:m-4',
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
    <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity duration-300" 
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
        <div class="relative bg-white dark:bg-slate-900 rounded-xl text-left overflow-hidden shadow-2xl border border-slate-200 dark:border-slate-800 transform transition-all sm:my-8 sm:align-middle w-full {{ $sizeClass }}"
             x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            
            <!-- Header -->
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-850">
                <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100 tracking-tight" id="modal_title_{{ $name }}">
                    {{ $title ?? 'İşlem Penceresi' }}
                </h3>
                <button type="button" @click="open = false" class="text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-lg p-1 transition-colors duration-150" aria-label="Kapat">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <div class="px-6 py-5 text-sm font-sans text-slate-700 dark:text-slate-300 leading-relaxed bg-white dark:bg-slate-900">
                {{ $slot }}
            </div>

            <!-- Footer -->
            @if (isset($footer))
                <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-850 flex justify-end gap-2">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>
