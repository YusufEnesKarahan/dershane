<header class="flex items-center justify-between h-16 px-4 sm:px-6 bg-white dark:bg-neutral-900 border-b border-neutral-200 dark:border-neutral-800 shrink-0 transition-colors">
    <div class="flex items-center gap-4">
        <button @click="sidebarOpen = true" class="lg:hidden text-neutral-500 hover:text-neutral-700 dark:hover:text-neutral-300 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-lg p-1" aria-label="Menüyü Aç">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <!-- Quick Info / Brand Subtitle -->
        <div class="hidden md:flex items-center gap-2 text-xs text-neutral-500 dark:text-neutral-400 font-sans">
            <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>Dershane SaaS Management System</span>
        </div>
    </div>
    <div class="flex items-center gap-3 sm:gap-4">
        <!-- Branch Switcher -->
        @if(auth()->check() && auth()->user()->hasRole('Super Admin'))
            <div x-data="{ openBranch: false }" class="relative">
                <button @click="openBranch = !openBranch" @click.away="openBranch = false" aria-label="Şube Değiştir" class="flex items-center gap-2 px-3 py-1.5 text-xs sm:text-sm font-medium text-neutral-700 bg-neutral-100 dark:bg-neutral-800 dark:text-neutral-300 rounded-lg hover:bg-neutral-200 dark:hover:bg-neutral-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span class="max-w-[100px] sm:max-w-[140px] truncate">{{ session('active_branch_name', auth()->user()->branch?->name ?? 'Tüm Şubeler') }}</span>
                    <svg class="w-3.5 h-3.5 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="openBranch" x-transition class="absolute right-0 mt-2 w-56 bg-white dark:bg-neutral-800 rounded-xl shadow-xl border border-neutral-200 dark:border-neutral-700 py-1 z-50">
                    <div class="px-4 py-2 border-b border-neutral-100 dark:border-neutral-700">
                        <p class="text-[11px] font-semibold text-neutral-500 uppercase tracking-wider">Aktif Şube Seçin</p>
                    </div>
                    <div class="max-h-60 overflow-y-auto">
                        @foreach(\App\Models\Branch::all() as $branch)
                            <form method="POST" action="{{ route('admin.branch.switch') }}">
                                @csrf
                                <input type="hidden" name="branch_id" value="{{ $branch->id }}">
                                <button type="submit" class="w-full text-left px-4 py-2 text-xs sm:text-sm text-neutral-700 dark:text-neutral-300 hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:text-blue-600 transition-colors flex items-center justify-between">
                                    <span class="truncate">{{ $branch->name }}</span>
                                    @if(session('active_branch_id', auth()->user()->branch_id) == $branch->id)
                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    @endif
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Theme Toggle -->
        <button @click="darkMode = !darkMode; if(darkMode) document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark');" aria-label="Karanlık/Aydınlık Mod" class="p-2 text-neutral-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <svg x-show="!darkMode" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
            <svg x-show="darkMode" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        </button>

        <!-- User Dropdown -->
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" @click.away="open = false" aria-label="Kullanıcı Menüsü" class="flex items-center gap-2 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-full">
                <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center text-blue-600 dark:text-blue-300 font-bold text-sm border border-blue-200 dark:border-blue-800">
                    {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                </div>
            </button>
            <div x-show="open" x-transition class="absolute right-0 mt-2 w-52 bg-white dark:bg-neutral-800 rounded-xl shadow-xl border border-neutral-200 dark:border-neutral-700 py-1 z-50">
                <div class="px-4 py-2.5 border-b border-neutral-100 dark:border-neutral-700">
                    <p class="text-sm font-semibold text-neutral-900 dark:text-white truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 truncate mt-0.5">{{ auth()->user()->email ?? 'admin@test.com' }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-xs font-semibold text-red-600 dark:text-red-400 hover:bg-neutral-50 dark:hover:bg-neutral-700/50 transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Güvenli Çıkış
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>