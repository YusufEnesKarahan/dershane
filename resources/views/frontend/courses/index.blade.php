@extends('layouts.templates.landing')

@inject('demo', 'App\Core\Services\DemoContentService')

@php
    $programs = $demo->getPrograms();

    $seo = [
        'title' => 'Eğitim Programlarımız | Kurslar',
        'description' => 'YKS, LGS ve ara sınıflara özel hazırlanmış yoğunlaştırılmış eğitim programlarımız.',
        'keywords' => 'kurslar, yks kursu, lgs kursu, üniversiteye hazırlık, eğitim programları'
    ];
@endphp

@section('template-content')
    <!-- PAGE HEADER -->
    <x-section bg="dark" py="20" class="relative overflow-hidden bg-gradient-to-br from-neutral-950 via-neutral-900 to-primary/20">
        <x-container class="relative z-10 text-center">
            <h1 class="text-4xl sm:text-5xl font-display font-bold text-white tracking-tight mb-4">Eğitim Programlarımız</h1>
            <p class="text-neutral-400 max-w-2xl mx-auto">Geleceğinizi şekillendirecek, hedeflerinize uygun özel eğitim programları.</p>
        </x-container>
    </x-section>

    <!-- COURSES LIST -->
    <x-section bg="gray" py="16">
        <x-container>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach ($programs as $program)
                    <div class="group bg-white rounded-premium-2xl border border-neutral-100 shadow-premium-sm overflow-hidden flex flex-col transition-all duration-300 hover:shadow-premium-md hover:-translate-y-1 hover:border-primary/20">
                        <div class="p-8 flex-1">
                            <span class="inline-block px-3 py-1 bg-primary/10 text-primary text-xs font-semibold rounded-full mb-4">{{ $program['subtitle'] }}</span>
                            <h3 class="text-xl font-display font-bold text-neutral mb-3 group-hover:text-primary transition-colors">
                                <a href="{{ $program['link'] }}" class="focus:outline-none">
                                    <span class="absolute inset-0" aria-hidden="true"></span>
                                    {{ $program['title'] }}
                                </a>
                            </h3>
                            <p class="text-sm text-neutral/70 leading-relaxed">{{ $program['description'] }}</p>
                        </div>
                        <div class="px-8 py-4 bg-neutral-50 border-t border-neutral-100 flex items-center justify-between">
                            <span class="text-xs font-semibold text-neutral/60">{{ $program['footer_text'] }}</span>
                            <svg class="h-5 w-5 text-neutral-400 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-container>
    </x-section>
@endsection
