@extends('layouts.templates.landing')

@inject('demo', 'App\Core\Services\DemoContentService')

@php
    $events = $demo->getEvents();

    $seo = [
        'title' => 'Etkinlikler | Kurumsal Etkinlik Takvimi',
        'description' => 'Yaklaşan seminerler, veli toplantıları ve motivasyon etkinliklerimizi takip edin.',
        'keywords' => 'etkinlikler, seminer, veli toplantısı, motivasyon, eğitim takvimi'
    ];
@endphp

@section('template-content')
    <!-- PAGE HEADER -->
    <x-section bg="dark" py="20" class="relative overflow-hidden bg-gradient-to-br from-neutral-950 via-neutral-900 to-primary/20">
        <x-container class="relative z-10 text-center">
            <h1 class="text-4xl sm:text-5xl font-display font-bold text-white tracking-tight mb-4">Etkinlikler</h1>
            <p class="text-neutral-400 max-w-2xl mx-auto">Seminerler, konferanslar ve öğrenci motivasyon etkinliklerimizi takip edin.</p>
        </x-container>
    </x-section>

    <!-- EVENTS LIST -->
    <x-section bg="white" py="16">
        <x-container>
            <div class="max-w-4xl mx-auto space-y-6">
                @forelse ($events as $event)
                    <div class="flex flex-col md:flex-row bg-white rounded-premium-xl border border-neutral-100 shadow-premium-sm overflow-hidden group hover:shadow-premium-md transition-shadow">
                        <!-- Date Block -->
                        <div class="bg-primary/5 text-primary md:w-48 shrink-0 flex flex-col justify-center items-center p-6 border-b md:border-b-0 md:border-r border-primary/10">
                            @php
                                $dateParts = explode(' ', $event['date']);
                            @endphp
                            <span class="text-3xl font-display font-bold">{{ $dateParts[0] ?? '' }}</span>
                            <span class="text-sm font-semibold uppercase tracking-wider">{{ $dateParts[1] ?? '' }} {{ $dateParts[2] ?? '' }}</span>
                        </div>
                        <!-- Content -->
                        <div class="p-6 md:p-8 flex-1 flex flex-col justify-center">
                            <h3 class="text-xl font-bold font-display text-neutral mb-2 group-hover:text-primary transition-colors">{{ $event['title'] }}</h3>
                            <p class="text-sm text-neutral/70 mb-4">{{ $event['desc'] }}</p>
                            <div class="flex items-center text-sm font-medium text-neutral/60 mt-auto">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                                {{ $event['location'] }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 bg-neutral-50 rounded-premium-xl border border-neutral-100">
                        <svg class="h-12 w-12 text-neutral-400 mx-auto mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"></path></svg>
                        <h3 class="text-lg font-medium text-neutral mb-1">Yaklaşan Etkinlik Bulunmuyor</h3>
                        <p class="text-sm text-neutral/60">Yeni etkinlikler eklendiğinde burada listelenecektir.</p>
                    </div>
                @endforelse
            </div>
        </x-container>
    </x-section>
@endsection
