@extends('layouts.templates.landing')

@inject('demo', 'App\Core\Services\DemoContentService')

@php
    $gallery = $demo->getGallery();

    $seo = [
        'title' => 'Galeri | Kurumumuzdan Kareler',
        'description' => 'Modern eğitim sınıflarımız, kütüphanemiz ve sosyal alanlarımızı inceleyin.',
        'keywords' => 'galeri, sınıflar, kütüphane, eğitim ortamı, fotoğraflar'
    ];
@endphp

@section('template-content')
    <!-- PAGE HEADER -->
    <x-section bg="dark" py="20" class="relative overflow-hidden bg-gradient-to-br from-neutral-950 via-neutral-900 to-primary/20">
        <x-container class="relative z-10 text-center">
            <h1 class="text-4xl sm:text-5xl font-display font-bold text-white tracking-tight mb-4">Galeri</h1>
            <p class="text-neutral-400 max-w-2xl mx-auto">Kurumumuzun modern eğitim alanlarını, butik sınıflarını ve sosyal ortamlarını keşfedin.</p>
        </x-container>
    </x-section>

    <!-- GALLERY GRID -->
    <x-section bg="gray" py="16">
        <x-container>
            <div class="columns-1 sm:columns-2 lg:columns-3 gap-6 space-y-6">
                @foreach ($gallery as $item)
                    <!-- We duplicate items slightly just to show a masonry effect in demo -->
                    @for($i = 0; $i < 2; $i++)
                        <div class="relative group rounded-premium-xl overflow-hidden break-inside-avoid">
                            <!-- Image Mock (Randomized aspect ratio for masonry look) -->
                            <div class="w-full bg-neutral-200 {{ $loop->index % 2 == 0 ? 'aspect-[4/3]' : 'aspect-square' }}">
                                <img src="/assets/branding/og-image.jpg" alt="{{ $item['title'] }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" loading="lazy">
                            </div>
                            <!-- Overlay -->
                            <div class="absolute inset-0 bg-gradient-to-t from-neutral-950/80 via-neutral-950/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-6">
                                <h3 class="text-white font-bold text-lg translate-y-4 group-hover:translate-y-0 transition-transform duration-300">{{ $item['title'] }}</h3>
                                <p class="text-white/80 text-sm translate-y-4 group-hover:translate-y-0 transition-transform duration-300 delay-75">{{ $item['subtitle'] }}</p>
                            </div>
                        </div>
                    @endfor
                @endforeach
            </div>
        </x-container>
    </x-section>
@endsection
