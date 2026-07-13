@extends('layouts.templates.landing')

@inject('demo', 'App\Core\Services\DemoContentService')

@php
    $contact = $demo->getContact();

    $seo = [
        'title' => 'İletişim | Bize Ulaşın',
        'description' => 'Eğitim programlarımız hakkında detaylı bilgi almak ve kurumumuzu ziyaret etmek için iletişim bilgilerimiz.',
        'keywords' => 'iletişim, adres, telefon, harita, bize ulaşın'
    ];
@endphp

@section('template-content')
    <!-- PAGE HEADER -->
    <x-section bg="dark" py="20" class="relative overflow-hidden bg-gradient-to-br from-neutral-950 via-neutral-900 to-primary/20">
        <x-container class="relative z-10 text-center">
            <h1 class="text-4xl sm:text-5xl font-display font-bold text-white tracking-tight mb-4">İletişim</h1>
            <p class="text-neutral-400 max-w-2xl mx-auto">Geleceğiniz için atacağınız bu önemli adımda yanınızdayız. Bize ulaşın, detayları konuşalım.</p>
        </x-container>
    </x-section>

    <!-- CONTACT CONTENT -->
    <x-section bg="gray" py="16">
        <x-container>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Info & Form -->
                <div class="space-y-12">
                    <!-- Contact Details -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="p-6 bg-white rounded-premium-xl border border-neutral-100 shadow-premium-sm group hover:border-primary/30 transition-colors">
                            <div class="h-10 w-10 bg-primary/10 text-primary flex items-center justify-center rounded-lg mb-4">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                            </div>
                            <h4 class="font-bold text-neutral mb-2">Adres</h4>
                            <p class="text-sm text-neutral/70">{{ $contact['address'] }}</p>
                        </div>
                        <div class="p-6 bg-white rounded-premium-xl border border-neutral-100 shadow-premium-sm group hover:border-primary/30 transition-colors">
                            <div class="h-10 w-10 bg-primary/10 text-primary flex items-center justify-center rounded-lg mb-4">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-2.896-1.596-5.48-4.18-7.076-7.076l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg>
                            </div>
                            <h4 class="font-bold text-neutral mb-2">Telefon</h4>
                            <p class="text-sm text-neutral/70"><a href="tel:{{ str_replace(' ', '', $contact['phone']) }}" class="hover:text-primary transition-colors">{{ $contact['phone'] }}</a></p>
                        </div>
                        <div class="p-6 bg-white rounded-premium-xl border border-neutral-100 shadow-premium-sm group hover:border-primary/30 transition-colors sm:col-span-2">
                            <div class="h-10 w-10 bg-primary/10 text-primary flex items-center justify-center rounded-lg mb-4">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                            </div>
                            <h4 class="font-bold text-neutral mb-2">E-Posta</h4>
                            <p class="text-sm text-neutral/70"><a href="mailto:{{ $contact['mail'] }}" class="hover:text-primary transition-colors">{{ $contact['mail'] }}</a></p>
                        </div>
                    </div>

                    <!-- Contact Form -->
                    <div class="bg-white p-8 rounded-premium-2xl border border-neutral-100 shadow-premium-sm">
                        <h3 class="text-2xl font-display font-bold text-neutral mb-6">Bize Mesaj Gönderin</h3>
                        <form action="#" method="POST" class="space-y-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-neutral mb-2">Ad Soyad</label>
                                    <input type="text" id="name" name="name" class="w-full px-4 py-3 rounded-xl border border-neutral-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all" placeholder="Adınız Soyadınız">
                                </div>
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-neutral mb-2">Telefon</label>
                                    <input type="tel" id="phone" name="phone" class="w-full px-4 py-3 rounded-xl border border-neutral-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all" placeholder="05XX XXX XX XX">
                                </div>
                            </div>
                            <div>
                                <label for="subject" class="block text-sm font-medium text-neutral mb-2">Konu</label>
                                <select id="subject" name="subject" class="w-full px-4 py-3 rounded-xl border border-neutral-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all bg-white">
                                    <option value="">Konu Seçin</option>
                                    <option value="kayit">Kayıt Bilgisi</option>
                                    <option value="burs">Bursluluk Sınavı</option>
                                    <option value="diger">Diğer</option>
                                </select>
                            </div>
                            <div>
                                <label for="message" class="block text-sm font-medium text-neutral mb-2">Mesajınız</label>
                                <textarea id="message" name="message" rows="4" class="w-full px-4 py-3 rounded-xl border border-neutral-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all resize-none" placeholder="Mesajınızı buraya yazabilirsiniz..."></textarea>
                            </div>
                            <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-8 py-3 border border-transparent text-base font-medium rounded-xl shadow-premium-sm text-white bg-primary hover:bg-primary-dark transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                Gönder
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Map -->
                <div class="h-96 lg:h-auto bg-neutral-200 rounded-premium-2xl overflow-hidden shadow-premium-sm border border-neutral-100 relative">
                    <!-- Placeholder Map -->
                    <div class="absolute inset-0 flex items-center justify-center bg-neutral-100 text-neutral-400">
                        <div class="text-center">
                            <svg class="h-12 w-12 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z" /></svg>
                            <span class="font-medium">Google Maps Integrasyonu<br><small class="opacity-70">(Yönetim panelinden eklenecek iframe buraya gelecek)</small></span>
                        </div>
                    </div>
                </div>
            </div>
        </x-container>
    </x-section>
@endsection
