@extends('layouts.templates.landing')

@inject('demo', 'App\Core\Services\DemoContentService')

@php
    $announcements = $demo->getAnnouncements();

    $seo = [
        'title' => 'Duyurular | Kurumsal Haberler',
        'description' => 'Kurumumuzla ilgili en son duyurular, haberler ve önemli gelişmeler.',
        'keywords' => 'duyurular, haberler, güncel, bildirimler, kurum haberleri'
    ];
@endphp

@section('template-content')
    <!-- PAGE HEADER -->
    <x-section bg="dark" py="20" class="relative overflow-hidden bg-gradient-to-br from-neutral-950 via-neutral-900 to-primary/20">
        <x-container class="relative z-10 text-center">
            <h1 class="text-4xl sm:text-5xl font-display font-bold text-white tracking-tight mb-4">Duyurular</h1>
            <p class="text-neutral-400 max-w-2xl mx-auto">Kurumumuzla ilgili en güncel haberler ve duyurular.</p>
        </x-container>
    </x-section>

    <!-- ANNOUNCEMENTS LIST -->
    <x-section bg="gray" py="16">
        <x-container>
            <div class="max-w-4xl mx-auto space-y-6">
                @forelse ($announcements as $announcement)
                    <div class="bg-white p-6 md:p-8 rounded-premium-xl border border-neutral-100 shadow-premium-sm relative pl-12 md:pl-16">
                        <div class="absolute left-0 top-0 bottom-0 w-2 bg-primary rounded-l-premium-xl"></div>
                        <div class="absolute left-[-16px] md:left-[-20px] top-8 bg-primary/20 text-primary p-2 md:p-3 rounded-full border-4 border-white shadow-sm">
                            <svg class="h-5 w-5 md:h-6 md:w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
                        </div>
                        <div class="flex items-center text-sm font-medium text-neutral/50 mb-2">
                            <span>{{ $announcement['date'] }}</span>
                        </div>
                        <h3 class="text-xl font-bold font-display text-neutral mb-3">{{ $announcement['title'] }}</h3>
                        <p class="text-sm text-neutral/70 leading-relaxed">{{ $announcement['content'] }}</p>
                    </div>
                @empty
                    <div class="text-center py-12 bg-neutral-50 rounded-premium-xl border border-neutral-100">
                        <svg class="h-12 w-12 text-neutral-400 mx-auto mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
                        <h3 class="text-lg font-medium text-neutral mb-1">Güncel Duyuru Bulunmuyor</h3>
                        <p class="text-sm text-neutral/60">Şu an için yayında olan bir duyuru bulunmamaktadır.</p>
                    </div>
                @endforelse
            </div>
        </x-container>
    </x-section>
@endsection
