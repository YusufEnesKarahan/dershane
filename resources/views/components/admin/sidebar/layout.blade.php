<aside :class="miniSidebar ? 'w-[68px]' : 'w-64'" class="fixed inset-y-0 left-0 z-50 flex flex-col transition-all duration-300 ease-in-out bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 lg:static lg:translate-x-0" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
    {{-- ─── Logo & Brand ─── --}}
    <div class="flex items-center justify-between h-14 px-4 border-b border-slate-200 dark:border-slate-800 shrink-0">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 truncate">
            <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold text-sm shadow-sm shrink-0">
                D
            </div>
            <span x-show="!miniSidebar" x-transition.opacity class="truncate tracking-tight text-slate-900 dark:text-slate-100 font-semibold text-sm">
                Dershane<span class="text-blue-600 dark:text-blue-400">SaaS</span>
            </span>
        </a>
        <div class="flex items-center gap-1">
            {{-- Mobile Close --}}
            <button @click="sidebarOpen = false" class="lg:hidden p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition" aria-label="Menüyü Kapat">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            {{-- Desktop Mini Toggle --}}
            <button @click="miniSidebar = !miniSidebar" class="hidden lg:flex p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition" aria-label="Menüyü Daralt">
                <svg x-show="!miniSidebar" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7"/><path stroke-linecap="round" stroke-linejoin="round" d="M18 19V5"/></svg>
                <svg x-show="miniSidebar" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 5v14"/></svg>
            </button>
        </div>
    </div>

    {{-- ─── Menu Items ─── --}}
    <div class="flex-1 overflow-y-auto py-3 px-2 space-y-0.5 custom-scrollbar">
        @php
            $menuService = app(\App\Domain\Auth\Services\AdminMenuService::class);
            $menus = $menuService->getSidebarMenu();
        @endphp
        @foreach($menus as $index => $menu)
            @if($index > 0 && isset($menu['children']))
                <div class="pt-3 pb-1 px-3" x-show="!miniSidebar" x-transition.opacity>
                    <div class="border-t border-slate-200 dark:border-slate-700/50"></div>
                </div>
                <div class="pt-2 pb-1 flex justify-center" x-show="miniSidebar" x-transition.opacity>
                    <div class="w-5 border-t border-slate-200 dark:border-slate-700/50"></div>
                </div>
            @endif
            @if(isset($menu['children']))
                <x-admin.sidebar.group :menu="$menu" />
            @else
                <x-admin.sidebar.item :menu="$menu" />
            @endif
        @endforeach
    </div>

    {{-- ─── User Footer ─── --}}
    <div class="shrink-0 border-t border-slate-200 dark:border-slate-800 p-2">
        <div x-show="!miniSidebar" x-transition.opacity class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800/50 transition cursor-default">
            <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center text-blue-600 dark:text-blue-400 font-semibold text-xs shrink-0">
                {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-slate-900 dark:text-slate-100 truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">{{ auth()->user()->email ?? '' }}</p>
            </div>
        </div>
        <div x-show="miniSidebar" x-transition.opacity class="flex justify-center">
            <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center text-blue-600 dark:text-blue-400 font-semibold text-xs">
                {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
            </div>
        </div>
    </div>
</aside>