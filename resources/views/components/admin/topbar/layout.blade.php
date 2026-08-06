<header class="flex items-center justify-between h-14 px-4 sm:px-6 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 shrink-0">
    <div class="flex items-center gap-3">
        {{-- Mobile Menu Toggle --}}
        <button @click="sidebarOpen = true" class="lg:hidden text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 focus:outline-none rounded-lg p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 transition" aria-label="Menüyü Aç">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>

        {{-- Breadcrumb --}}
        @php
            $menuService = app(\App\Domain\Auth\Services\AdminMenuService::class);
            $breadcrumbs = $menuService->getBreadcrumbs();
        @endphp
        <nav class="hidden md:flex items-center text-sm" aria-label="Breadcrumb">
            <ol class="flex items-center gap-1.5">
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/></svg>
                    </a>
                </li>
                @foreach($breadcrumbs as $crumb)
                    <li class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-slate-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        @if(!$loop->last && isset($crumb['url']))
                            <a href="{{ $crumb['url'] }}" class="text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 font-medium transition-colors">{{ $crumb['title'] }}</a>
                        @else
                            <span class="text-slate-700 dark:text-slate-200 font-medium">{{ $crumb['title'] }}</span>
                        @endif
                    </li>
                @endforeach
            </ol>
        </nav>
    </div>

    <div class="flex items-center gap-1.5 sm:gap-2">
        {{-- Search / Command Palette Trigger --}}
        <button
            x-data
            @click="$dispatch('open-command-palette')"
            class="flex items-center gap-2 px-3 py-1.5 text-sm text-slate-400 dark:text-slate-500 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors focus:outline-none"
            aria-label="Arama (Ctrl+K)"
        >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <span class="hidden sm:inline text-xs">Ara...</span>
            <kbd class="hidden sm:inline-flex items-center gap-0.5 px-1.5 py-0.5 text-[10px] font-medium text-slate-400 dark:text-slate-500 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded">
                <span class="text-xs">⌘</span>K
            </kbd>
        </button>

        {{-- Branch Switcher --}}
        @if(auth()->check() && auth()->user()->hasRole('Super Admin'))
            <div x-data="{ openBranch: false }" class="relative">
                <button @click="openBranch = !openBranch" @click.away="openBranch = false" aria-label="Şube Değiştir"
                        class="flex items-center gap-2 px-2.5 py-1.5 text-xs font-medium text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors focus:outline-none">
                    <svg class="w-4 h-4 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span class="hidden sm:inline max-w-[120px] truncate">{{ session('active_branch_name', auth()->user()->branch?->name ?? 'Tüm Şubeler') }}</span>
                    <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="openBranch" x-transition class="absolute right-0 mt-2 w-52 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 py-1 z-50">
                    <div class="px-3 py-2 border-b border-slate-100 dark:border-slate-700">
                        <p class="text-[11px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Aktif Şube</p>
                    </div>
                    <div class="max-h-60 overflow-y-auto custom-scrollbar">
                        @foreach(\App\Models\Branch::all() as $branch)
                            <form method="POST" action="{{ route('admin.branch.switch') }}">
                                @csrf
                                <input type="hidden" name="branch_id" value="{{ $branch->id }}">
                                <button type="submit" class="w-full text-left px-3 py-2 text-sm text-slate-600 dark:text-slate-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 transition-colors flex items-center justify-between">
                                    <span class="truncate">{{ $branch->name }}</span>
                                    @if(session('active_branch_id', auth()->user()->branch_id) == $branch->id)
                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    @endif
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Notification Bell --}}
        <button class="relative p-2 text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors focus:outline-none" aria-label="Bildirimler">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path stroke-linecap="round" stroke-linejoin="round" d="M13.73 21a2 2 0 01-3.46 0"/></svg>
            {{-- Notification dot --}}
            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-blue-600 rounded-full ring-2 ring-white dark:ring-slate-900"></span>
        </button>

        {{-- Theme Toggle --}}
        <button @click="darkMode = !darkMode; if(darkMode) document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark');" aria-label="Tema Değiştir"
                class="p-2 text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors focus:outline-none">
            <svg x-show="!darkMode" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
            <svg x-show="darkMode" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        </button>

        {{-- User Dropdown --}}
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" @click.away="open = false" aria-label="Kullanıcı Menüsü"
                    class="flex items-center gap-2 p-1 focus:outline-none rounded-full hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center text-blue-600 dark:text-blue-400 font-semibold text-sm">
                    {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                </div>
            </button>
            <div x-show="open" x-transition class="absolute right-0 mt-2 w-56 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 py-1 z-50">
                {{-- User Info --}}
                <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 truncate mt-0.5">{{ auth()->user()->email ?? '' }}</p>
                    @if(auth()->user()->roles->first())
                        <span class="inline-flex items-center mt-1.5 px-2 py-0.5 text-[10px] font-semibold text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-900/30 rounded-full">
                            {{ auth()->user()->roles->first()->name }}
                        </span>
                    @endif
                </div>
                {{-- Menu Items --}}
                <div class="py-1">
                    <a href="{{ route('admin.settings.institution.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Kurum Ayarları
                    </a>
                </div>
                {{-- Logout --}}
                <div class="border-t border-slate-100 dark:border-slate-700 pt-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors flex items-center gap-2.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Güvenli Çıkış
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>