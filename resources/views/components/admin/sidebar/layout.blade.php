<aside :class="miniSidebar ? 'w-20' : 'w-64'" class="fixed inset-y-0 left-0 z-50 flex flex-col transition-all duration-300 ease-in-out bg-white dark:bg-neutral-900 border-r border-neutral-200 dark:border-neutral-800 lg:static lg:translate-x-0" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
    <div class="flex items-center justify-between h-16 px-4 border-b border-neutral-200 dark:border-neutral-800 shrink-0">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 font-display font-bold text-xl text-primary truncate">
            <svg class="w-8 h-8 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            <span x-show="!miniSidebar" x-transition.opacity class="truncate">Dershane</span>
        </a>
        <button @click="miniSidebar = !miniSidebar" class="hidden lg:block text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
    </div>
    <div class="flex-1 overflow-y-auto py-4 space-y-1 custom-scrollbar">
        @php
            $menuService = app(\App\Domain\Auth\Services\AdminMenuService::class);
            $menus = $menuService->getSidebarMenu();
        @endphp
        @foreach($menus as $menu)
            @if(isset($menu['children']))
                <x-admin.sidebar.group :menu="$menu" />
            @else
                <x-admin.sidebar.item :menu="$menu" />
            @endif
        @endforeach
    </div>
</aside>