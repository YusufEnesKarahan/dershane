@props(['menu'])
<a href="{{ (isset($menu['route']) && \Illuminate\Support\Facades\Route::has($menu['route'])) ? route($menu['route']) : '#' }}" class="flex items-center px-3.5 py-2.5 text-sm font-medium transition-all rounded-xl group {{ (isset($menu['route']) && request()->routeIs($menu['route'])) ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/50 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}">
    <div class="shrink-0 flex items-center justify-center" :class="miniSidebar ? 'w-full' : 'w-6 mr-3'">
        <svg class="w-5 h-5 {{ (isset($menu['route']) && request()->routeIs($menu['route'])) ? 'text-blue-600 dark:text-blue-400' : 'text-slate-400 dark:text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-300' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="12" r="9" stroke-width="1.75"/></svg>
    </div>
    <span x-show="!miniSidebar" x-transition.opacity class="truncate">{{ $menu['title'] }}</span>
</a>