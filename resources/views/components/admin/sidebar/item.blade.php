@props(['menu'])
<a href="{{ isset($menu['route']) ? route($menu['route']) : '#' }}" class="flex items-center px-4 py-2 text-sm font-medium transition-colors group {{ request()->routeIs($menu['route'] ?? '') ? 'text-primary bg-primary/10 dark:bg-primary/20 border-r-2 border-primary' : 'text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800/50 hover:text-neutral-900 dark:hover:text-neutral-200' }}">
    <div class="shrink-0 flex items-center justify-center" :class="miniSidebar ? 'w-full' : 'w-6 mr-3'">
        <!-- Generic Icon Placeholder -->
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="12" r="10"/></svg>
    </div>
    <span x-show="!miniSidebar" x-transition.opacity class="truncate">{{ $menu['title'] }}</span>
</a>