@props(['menu'])
<div x-data="{ open: false }" class="space-y-1">
    <button @click="open = !open" :class="miniSidebar ? 'justify-center px-2' : 'px-4'" class="w-full flex items-center py-2 text-sm font-medium text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800/50 transition-colors">
        <div class="shrink-0 flex items-center justify-center" :class="miniSidebar ? 'w-full' : 'w-6 mr-3'">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
        </div>
        <span x-show="!miniSidebar" x-transition.opacity class="flex-1 text-left truncate">{{ $menu['title'] }}</span>
        <svg x-show="!miniSidebar" :class="{ 'rotate-180': open }" class="w-4 h-4 shrink-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    </button>
    <div x-show="open && !miniSidebar" x-collapse class="pl-11 pr-4 py-1 space-y-1">
        @foreach($menu['children'] as $child)
            @if($child)
            <a href="{{ isset($child['route']) ? route($child['route']) : '#' }}" class="block py-2 text-sm {{ request()->routeIs($child['route'] ?? '') ? 'text-primary font-medium' : 'text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-200' }} transition-colors">
                {{ $child['title'] }}
            </a>
            @endif
        @endforeach
    </div>
</div>