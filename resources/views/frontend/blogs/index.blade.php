@extends('layouts.templates.landing')

@inject('demo', 'App\Core\Services\DemoContentService')

@php
    $blogs = $demo->getBlogs();

    $seo = [
        'title' => 'Blog & Rehberlik | Eğitim Dünyasından Haberler',
        'description' => 'Sınav stratejileri, çalışma taktikleri ve eğitim dünyasındaki en güncel gelişmeler.',
        'keywords' => 'blog, rehberlik, yks taktikleri, lgs taktikleri, eğitim haberleri'
    ];
@endphp

@section('template-content')
    <!-- PAGE HEADER -->
    <x-section bg="dark" py="20" class="relative overflow-hidden bg-gradient-to-br from-neutral-950 via-neutral-900 to-primary/20">
        <x-container class="relative z-10 text-center">
            <h1 class="text-4xl sm:text-5xl font-display font-bold text-white tracking-tight mb-4">Blog & Rehberlik</h1>
            <p class="text-neutral-400 max-w-2xl mx-auto">Rehberlik servisimizin hazırladığı sınav stratejileri ve eğitim dünyasından güncel makaleler.</p>
        </x-container>
    </x-section>

    <!-- BLOG LIST -->
    <x-section bg="gray" py="16">
        <x-container>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($blogs as $blog)
                    <article class="group bg-white rounded-premium-2xl border border-neutral-100 shadow-premium-sm overflow-hidden flex flex-col transition-all duration-300 hover:shadow-premium-md hover:-translate-y-1">
                        <div class="w-full aspect-[16/9] relative overflow-hidden bg-neutral-100">
                            <img src="/assets/branding/og-image.jpg" alt="{{ $blog['title'] }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" loading="lazy">
                        </div>
                        <div class="p-8 flex-1 flex flex-col">
                            <div class="flex items-center justify-between text-xs text-neutral/50 font-medium mb-3">
                                <span>{{ explode(' | ', $blog['meta'])[0] }}</span>
                                <span class="bg-primary/5 text-primary px-2 py-1 rounded-full">{{ explode(' | ', $blog['meta'])[1] }}</span>
                            </div>
                            <h3 class="text-xl font-display font-bold text-neutral mb-3 group-hover:text-primary transition-colors line-clamp-2">
                                <a href="{{ $blog['link'] }}" class="focus:outline-none">
                                    <span class="absolute inset-0" aria-hidden="true"></span>
                                    {{ $blog['title'] }}
                                </a>
                            </h3>
                            <p class="text-sm text-neutral/70 leading-relaxed mb-6 line-clamp-3 flex-1">{{ $blog['snippet'] }}</p>
                            <div class="flex items-center justify-between mt-auto pt-4 border-t border-neutral-100">
                                <span class="text-xs text-neutral/50 font-medium">{{ $blog['read_time'] }}</span>
                                <span class="text-sm font-semibold text-primary group-hover:text-primary-dark flex items-center transition-colors">
                                    Devamını Oku
                                    <svg class="h-4 w-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                </span>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </x-container>
    </x-section>
@endsection
