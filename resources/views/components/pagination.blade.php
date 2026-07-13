@props([
    'paginator',
])

@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Sayfalama" class="flex items-center justify-between px-4 py-3 bg-white border border-neutral-100 rounded-premium-lg shadow-premium-sm sm:px-6">
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 text-xs font-medium text-neutral/40 bg-neutral-50 border border-neutral-200 rounded-premium-md cursor-default">
                    Geri
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-xs font-medium text-neutral bg-white border border-neutral-200 rounded-premium-md hover:bg-neutral-50 transition duration-150">
                    Geri
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 ml-3 text-xs font-medium text-neutral bg-white border border-neutral-200 rounded-premium-md hover:bg-neutral-50 transition duration-150">
                    İleri
                </a>
            @else
                <span class="relative inline-flex items-center px-4 py-2 ml-3 text-xs font-medium text-neutral/40 bg-neutral-50 border border-neutral-200 rounded-premium-md cursor-default">
                    İleri
                </span>
            @endif
        </div>

        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-xs text-neutral/60">
                    Toplam <span class="font-medium text-neutral">{{ $paginator->total() }}</span> kayıttan 
                    <span class="font-medium text-neutral">{{ $paginator->firstItem() }}</span> ile 
                    <span class="font-medium text-neutral">{{ $paginator->lastItem() }}</span> arası gösteriliyor.
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex shadow-premium-sm rounded-premium-md overflow-hidden">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-neutral/30 bg-neutral-50/50 border border-neutral-200 cursor-default" aria-disabled="true">
                            <span class="sr-only">Önceki</span>
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-neutral bg-white border border-neutral-200 hover:bg-neutral-50 transition duration-150">
                            <span class="sr-only">Önceki</span>
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
                        </a>
                    @endif

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-neutral bg-white border border-neutral-200 hover:bg-neutral-50 transition duration-150">
                            <span class="sr-only">Sonraki</span>
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    @else
                        <span class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-neutral/30 bg-neutral-50/50 border border-neutral-200 cursor-default" aria-disabled="true">
                            <span class="sr-only">Sonraki</span>
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
