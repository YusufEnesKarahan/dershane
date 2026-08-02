<header class="flex items-center justify-between h-16 px-4 bg-white dark:bg-neutral-900 border-b border-neutral-200 dark:border-neutral-800 shrink-0 transition-colors">
    <div class="flex items-center gap-4">
        <button @click="sidebarOpen = true" class="lg:hidden text-neutral-500 hover:text-neutral-700 dark:hover:text-neutral-300">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <!-- Search -->
        <button class="hidden sm:flex items-center gap-2 px-3 py-1.5 text-sm text-neutral-400 bg-neutral-100 dark:bg-neutral-800 rounded-lg hover:bg-neutral-200 dark:hover:bg-neutral-700 transition-colors w-64">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <span>Search... (Ctrl+K)</span>
        </button>
    </div>
    <div class="flex items-center gap-4">
        <!-- Branch Switcher -->
        @if(auth()->check() && auth()->user()->hasRole('Super Admin'))
            <div x-data="{ openBranch: false }" class="relative">
                <button @click="openBranch = !openBranch" @click.away="openBranch = false" class="flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-neutral-700 bg-neutral-100 dark:bg-neutral-800 dark:text-neutral-300 rounded-lg hover:bg-neutral-200 dark:hover:bg-neutral-700 transition-colors focus:outline-none">
                    <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span class="max-w-[120px] truncate">{{ session('active_branch_name', auth()->user()->branch?->name ?? 'Default Branch') }}</span>
                    <svg class="w-4 h-4 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="openBranch" x-transition class="absolute right-0 mt-2 w-56 bg-white dark:bg-neutral-800 rounded-xl shadow-premium border border-neutral-100 dark:border-neutral-700 py-1 z-50">
                    <div class="px-4 py-2 border-b border-neutral-100 dark:border-neutral-700">
                        <p class="text-xs font-semibold text-neutral-500 uppercase tracking-wider">Switch Branch</p>
                    </div>
                    <div class="max-h-60 overflow-y-auto">
                        @foreach(\App\Models\Branch::all() as $branch)
                            <form method="POST" action="{{ route('admin.branch.switch') }}">
                                @csrf
                                <input type="hidden" name="branch_id" value="{{ $branch->id }}">
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-neutral-700 dark:text-neutral-300 hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary transition-colors flex items-center justify-between">
                                    <span class="truncate">{{ $branch->name }}</span>
                                    @if(session('active_branch_id', auth()->user()->branch_id) == $branch->id)
                                        <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    @endif
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Theme Toggle -->
        <button @click="darkMode = !darkMode; if(darkMode) document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark');" class="text-neutral-500 hover:text-primary transition-colors">
            <svg x-show="!darkMode" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
            <svg x-show="darkMode" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        </button>
        <!-- User Dropdown -->
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" @click.away="open = false" class="flex items-center gap-2 focus:outline-none">
                <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-sm">
                    {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                </div>
            </button>
            <div x-show="open" x-transition class="absolute right-0 mt-2 w-48 bg-white dark:bg-neutral-800 rounded-xl shadow-premium border border-neutral-100 dark:border-neutral-700 py-1 z-50">
                <div class="px-4 py-2 border-b border-neutral-100 dark:border-neutral-700">
                    <p class="text-sm font-medium text-neutral-900 dark:text-white">{{ auth()->user()->name ?? 'Admin' }}</p>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 truncate">{{ auth()->user()->email ?? 'admin@test.com' }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-neutral-50 dark:hover:bg-neutral-700/50 transition-colors">Çıkış Yap</button>
                </form>
            </div>
        </div>
    </div>
</header>