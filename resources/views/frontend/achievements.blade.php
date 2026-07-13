@extends('layouts.templates.landing')

@inject('demo', 'App\Core\Services\DemoContentService')

@php
    $stories = $demo->getSuccessStories();

    $seo = [
        'title' => 'Başarılarımız | Gurur Tablomuz',
        'description' => 'Geçtiğimiz dönem hayallerindeki üniversitelere ve liselere yerleştirdiğimiz öğrencilerimizin başarıları.',
        'keywords' => 'başarılar, derece, ygs, yks, lgs, gurur tablomuz'
    ];
@endphp

@section('template-content')
    <!-- PAGE HEADER -->
    <x-section bg="dark" py="20" class="relative overflow-hidden bg-gradient-to-br from-neutral-950 via-neutral-900 to-primary/20">
        <x-container class="relative z-10 text-center">
            <h1 class="text-4xl sm:text-5xl font-display font-bold text-white tracking-tight mb-4">Gurur Tablomuz</h1>
            <p class="text-neutral-400 max-w-2xl mx-auto">Hayallerindeki okullara yerleşerek hem kendilerini hem de bizi gururlandıran başarılı öğrencilerimiz.</p>
        </x-container>
    </x-section>

    <!-- SUCCESS STORIES GRID -->
    <x-section bg="gray" py="16">
        <x-container>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($stories as $story)
                    <div class="p-6 bg-white rounded-premium-xl border border-neutral-100 shadow-premium-sm text-center space-y-3 transition duration-300 hover:shadow-premium-md hover:-translate-y-1">
                        <span class="inline-flex px-3 py-1 bg-green-50 text-green-800 border border-green-200 text-xs font-semibold rounded-full select-none">{{ $story['badge'] }}</span>
                        <h4 class="text-lg font-bold text-neutral">{{ $story['student'] }}</h4>
                        <p class="text-sm text-neutral/60">{{ $story['destination'] }}</p>
                    </div>
                @endforeach
            </div>
        </x-container>
    </x-section>
@endsection
