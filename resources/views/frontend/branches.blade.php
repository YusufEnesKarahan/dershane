@extends('layouts.templates.landing')

@inject('demo', 'App\Core\Services\DemoContentService')

@php
    $branches = $demo->getBranches();

    $seo = [
        'title' => 'Şubelerimiz | Kurumumuza Ulaşın',
        'description' => 'Size en yakın şubemizi bulun ve iletişime geçin.',
        'keywords' => 'şubelerimiz, iletişim, adres, telefon, konum'
    ];
@endphp

@section('template-content')
    <!-- PAGE HEADER -->
    <x-section bg="dark" py="20" class="relative overflow-hidden bg-gradient-to-br from-neutral-950 via-neutral-900 to-primary/20">
        <x-container class="relative z-10 text-center">
            <h1 class="text-4xl sm:text-5xl font-display font-bold text-white tracking-tight mb-4">Şubelerimiz</h1>
            <p class="text-neutral-400 max-w-2xl mx-auto">Size en yakın şubemizi ziyaret edebilir, eğitim sistemimiz hakkında detaylı bilgi alabilirsiniz.</p>
        </x-container>
    </x-section>

    <!-- BRANCHES LIST -->
    <x-section bg="gray" py="16">
        <x-container>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @foreach ($branches as $branch)
                    <div class="group bg-white rounded-premium-2xl border border-neutral-100 shadow-premium-sm overflow-hidden flex flex-col sm:flex-row transition-all duration-300 hover:shadow-premium-md hover:border-primary/20">
                        <div class="w-full sm:w-1/3 h-48 sm:h-auto shrink-0 relative overflow-hidden bg-neutral-100">
                            <img src="{{ $branch['image'] }}" alt="{{ $branch['name'] }}" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" loading="lazy">
                        </div>
                        <div class="p-6 sm:w-2/3 flex flex-col justify-center">
                            <h3 class="text-xl font-display font-bold text-neutral mb-4">{{ $branch['name'] }}</h3>
                            <div class="space-y-3 text-sm text-neutral/70">
                                <div class="flex items-start gap-3">
                                    <svg class="h-5 w-5 text-primary shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                                    <span>{{ $branch['address'] }}</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <svg class="h-5 w-5 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-2.896-1.596-5.48-4.18-7.076-7.076l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg>
                                    <a href="tel:{{ str_replace(' ', '', $branch['phone']) }}" class="hover:text-primary transition-colors">{{ $branch['phone'] }}</a>
                                </div>
                                <div class="flex items-center gap-3">
                                    <svg class="h-5 w-5 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                                    <span>Şube Müdürü: {{ $branch['manager'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-container>
    </x-section>
@endsection
