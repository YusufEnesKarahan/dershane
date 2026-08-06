<aside :class="miniSidebar ? 'w-20' : 'w-64'" class="fixed inset-y-0 left-0 z-50 flex flex-col transition-all duration-300 ease-in-out bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 lg:static lg:translate-x-0" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
    <div class="flex items-center justify-between h-16 px-4 border-b border-slate-200 dark:border-slate-800 shrink-0">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 font-bold text-xl text-blue-600 dark:text-blue-400 truncate">
            <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center font-black shadow-sm shrink-0">D</div>
            <span x-show="!miniSidebar" x-transition.opacity class="truncate tracking-tight text-slate-900 dark:text-slate-100 font-extrabold text-lg">Dershane<span class="text-blue-600 dark:text-blue-400">SaaS</span></span>
        </a>
        <button @click="miniSidebar = !miniSidebar" class="hidden lg:block p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-lg transition" aria-label="Menüyü Daralt">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
    </div>
    <div class="flex-1 overflow-y-auto py-4 px-2 space-y-1 custom-scrollbar">
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