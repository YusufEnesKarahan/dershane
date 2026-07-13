<nav class="bg-white border-b border-neutral-100 sticky top-0 z-40 shadow-premium-sm" x-data="{ open: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center gap-8">
                <!-- Brand logo / title -->
                <a href="/" class="flex items-center font-display font-bold text-lg text-primary tracking-tight shrink-0 select-none">
                    {{ config('brand.name', 'Eğitim Kurumu SaaS') }}
                </a>

                <!-- Desktop menu links -->
                <div class="hidden md:flex items-center gap-6">
                    <a href="/" class="text-xs font-semibold text-neutral hover:text-primary transition duration-150">
                        Ana Sayfa
                    </a>
                </div>
            </div>

            <!-- Header right actions -->
            <div class="hidden md:flex items-center gap-4">
                <a href="/login" class="text-xs font-semibold text-neutral hover:text-primary transition duration-150">
                    Giriş Yap
                </a>
                <a href="/register" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-xs font-semibold rounded-premium-md text-white bg-primary hover:bg-primary/95 shadow-premium-sm transition duration-150">
                    Ön Kayıt Ol
                </a>
            </div>

            <!-- Mobile menu button -->
            <div class="flex items-center md:hidden">
                <button type="button" @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-premium-md text-neutral/50 hover:text-primary focus:outline-none transition duration-150" aria-label="Menü">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" x-show="!open">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" x-show="open" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Drawer -->
    <div class="md:hidden border-t border-neutral-100 bg-white" x-show="open" style="display: none;">
        <div class="px-2 pt-2 pb-3 space-y-1">
            <a href="/" class="block px-3 py-2 rounded-premium-md text-xs font-semibold text-neutral hover:bg-neutral-50 hover:text-primary transition duration-150">
                Ana Sayfa
            </a>
            <div class="border-t border-neutral-100 my-2 pt-2 flex flex-col gap-2 px-3">
                <a href="/login" class="block text-center text-xs font-semibold text-neutral py-2 rounded-premium-md hover:bg-neutral-50 transition duration-150">
                    Giriş Yap
                </a>
                <a href="/register" class="block text-center text-xs font-semibold text-white bg-primary py-2 rounded-premium-md hover:bg-primary/95 transition duration-150">
                    Ön Kayıt Ol
                </a>
            </div>
        </div>
    </div>
</nav>
