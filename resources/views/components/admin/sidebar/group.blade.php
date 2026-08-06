@props(['menu'])
@php
    // Check if any child route is currently active
    $childActive = false;
    if (isset($menu['children'])) {
        foreach ($menu['children'] as $child) {
            if (isset($child['route']) && request()->routeIs($child['route'])) {
                $childActive = true;
                break;
            }
        }
    }

    // Lucide icon mapping (same as item.blade.php)
    $iconMap = [
        'home' => 'M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z||M9 22V12h6v10',
        'users' => 'M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2||M9 7a4 4 0 100-8 4 4 0 000 8z||M16 3.13a4 4 0 010 7.75',
        'academic-cap' => 'M22 10v6M2 10l10-5 10 5-10 5z||M6 12v5c3 3 7 3 12 0v-5',
        'chart-bar' => 'M18 20V10||M12 20V4||M6 20v-6',
        'server' => 'M2 2h20v8H2z||M2 14h20v8H2z||M6 6h.01||M6 18h.01',
        'user-plus' => 'M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2||M8.5 7a4 4 0 100-8 4 4 0 000 8z||M20 8v6||M23 11h-6',
        'cog' => 'M12 15a3 3 0 100-6 3 3 0 000 6z||M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 112.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 114 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z',
        'folder-open' => 'M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z',
        'archive' => 'M21 8v13H3V8||M1 3h22v5H1z||M10 12h4',
    ];

    $iconKey = $menu['icon'] ?? 'home';
    $paths = $iconMap[$iconKey] ?? $iconMap['home'];
@endphp
<div x-data="{ open: {{ $childActive ? 'true' : 'false' }} }" class="space-y-0.5">
    {{-- Group Toggle Button --}}
    <button @click="open = !open"
            class="group relative w-full flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all duration-150
            {{ $childActive
                ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-950/20'
                : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
            :class="miniSidebar ? 'justify-center px-2' : ''"
    >
        {{-- Icon --}}
        <div class="shrink-0 flex items-center justify-center w-5 h-5">
            <svg class="w-[18px] h-[18px] {{ $childActive ? 'text-blue-600 dark:text-blue-400' : 'text-slate-400 dark:text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-300' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                @foreach(explode('||', $paths) as $path)
                    <path d="{{ $path }}"></path>
                @endforeach
            </svg>
        </div>

        {{-- Label --}}
        <span x-show="!miniSidebar" x-transition.opacity class="flex-1 text-left truncate">{{ $menu['title'] }}</span>

        {{-- Chevron --}}
        <svg x-show="!miniSidebar" :class="{ 'rotate-90': open }" class="w-3.5 h-3.5 shrink-0 transition-transform duration-200 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>

        {{-- Mini Sidebar Tooltip --}}
        <div x-show="miniSidebar" x-transition.opacity
             class="absolute left-full ml-2 px-2.5 py-1.5 bg-slate-900 dark:bg-slate-700 text-white text-xs font-medium rounded-lg shadow-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-50">
            {{ $menu['title'] }}
        </div>
    </button>

    {{-- Children Items --}}
    <div x-show="open && !miniSidebar" x-collapse class="ml-4 pl-4 border-l border-slate-200 dark:border-slate-700/50 space-y-0.5">
        @foreach($menu['children'] as $child)
            @if($child)
                @php
                    $childIsActive = isset($child['route']) && request()->routeIs($child['route']);
                    $childHref = (isset($child['route']) && \Illuminate\Support\Facades\Route::has($child['route'])) ? route($child['route']) : '#';
                @endphp
                <a href="{{ $childHref }}"
                   class="block py-1.5 px-3 text-[13px] rounded-md transition-colors duration-150
                   {{ $childIsActive
                       ? 'text-blue-600 dark:text-blue-400 font-semibold bg-blue-50/50 dark:bg-blue-950/20'
                       : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/40' }}"
                   @if($childIsActive) aria-current="page" @endif
                >
                    {{ $child['title'] }}
                    @if(!empty($child['badge']))
                        <span class="ml-auto inline-flex items-center justify-center min-w-[16px] h-4 px-1 text-[10px] font-bold text-white bg-blue-600 rounded-full leading-none">
                            {{ $child['badge'] }}
                        </span>
                    @endif
                </a>
            @endif
        @endforeach
    </div>
</div>