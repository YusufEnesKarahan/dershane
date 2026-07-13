<footer class="bg-white border-t border-neutral-100 font-sans mt-auto">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Branding -->
            <div class="md:col-span-2 space-y-4">
                <span class="font-display font-bold text-lg text-primary select-none">
                    {{ config('brand.name', 'Eğitim Kurumu SaaS') }}
                </span>
                <p class="text-xs text-neutral/50 max-w-sm leading-relaxed">
                    Eğitim merkezleri için geliştirilmiş, tek kod tabanı üzerinden çalışan, kurumsal ve modern yönetim paneli altyapısı.
                </p>
            </div>

            <!-- Links 1 -->
            <div>
                <h4 class="text-xs font-semibold text-neutral uppercase tracking-wider select-none">Kurumsal</h4>
                <ul class="mt-4 space-y-2 text-xs text-neutral/60">
                    <li><a href="#" class="hover:text-primary transition duration-150">Hakkımızda</a></li>
                    <li><a href="#" class="hover:text-primary transition duration-150">Programlarımız</a></li>
                </ul>
            </div>

            <!-- Links 2 -->
            <div>
                <h4 class="text-xs font-semibold text-neutral uppercase tracking-wider select-none">İletişim</h4>
                <ul class="mt-4 space-y-2 text-xs text-neutral/60">
                    <li><a href="#" class="hover:text-primary transition duration-150">İletişim Sayfası</a></li>
                    <li><a href="#" class="hover:text-primary transition duration-150">Ön Başvuru</a></li>
                </ul>
            </div>
        </div>

        <!-- Copyright -->
        <div class="mt-12 pt-8 border-t border-neutral-100 flex items-center justify-between text-xs text-neutral/40">
            <p>&copy; {{ date('Y') }} {{ config('brand.name', 'Eğitim Kurumu') }}. {{ config('brand.footer.copyright_text') }}</p>
            <div class="flex gap-4">
                <!-- Social placeholders -->
            </div>
        </div>
    </div>
</footer>
