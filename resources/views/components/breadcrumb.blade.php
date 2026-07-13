@props([
    'items' => [], // array of label => url
])

<nav class="flex text-xs font-sans" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-2">
        <li class="inline-flex items-center">
            <a href="/" class="inline-flex items-center text-neutral/50 hover:text-primary transition-colors duration-200">
                <!-- Lucide Home Icon equivalent -->
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Ana Sayfa
            </a>
        </li>
        
        @foreach ($items as $label => $url)
            <li>
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-neutral/30 mx-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                    </svg>
                    @if ($loop->last)
                        <span class="ml-1 text-neutral/80 font-medium select-none" aria-current="page">{{ $label }}</span>
                    @else
                        <a href="{{ $url }}" class="ml-1 text-neutral/50 hover:text-primary transition-colors duration-200">{{ $label }}</a>
                    @endif
                </div>
            </li>
        @endforeach
    </ol>
</nav>
