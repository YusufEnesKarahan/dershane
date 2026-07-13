@extends('layouts.templates.landing')

@inject('demo', 'App\Core\Services\DemoContentService')

@php
    $teachers = $demo->getTeachers();

    $seo = [
        'title' => 'Öğretmen Kadromuz | Uzman Eğitimciler',
        'description' => 'Alanında uzman, deneyimli ve yenilikçi öğretmen kadromuzla tanışın.',
        'keywords' => 'öğretmenler, uzman kadro, eğitimciler, öğretmen kadromuz'
    ];
@endphp

@section('template-content')
    <!-- PAGE HEADER -->
    <x-section bg="dark" py="20" class="relative overflow-hidden bg-gradient-to-br from-neutral-950 via-neutral-900 to-primary/20">
        <x-container class="relative z-10 text-center">
            <h1 class="text-4xl sm:text-5xl font-display font-bold text-white tracking-tight mb-4">Öğretmen Kadromuz</h1>
            <p class="text-neutral-400 max-w-2xl mx-auto">Başarının tesadüf olmadığına inanan, alanında uzman ve tecrübeli eğitim kadromuz.</p>
        </x-container>
    </x-section>

    <!-- TEACHERS GRID -->
    <x-section bg="white" py="16">
        <x-container>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach ($teachers as $teacher)
                    <div class="group bg-neutral-50 rounded-premium-2xl border border-neutral-100 shadow-premium-sm overflow-hidden text-center transition duration-300 hover:shadow-premium-md hover:-translate-y-1">
                        <div class="w-full aspect-square bg-neutral-200 relative overflow-hidden">
                            <!-- Placeholder Image -->
                            <img src="/assets/branding/og-image.jpg" alt="{{ $teacher['name'] }}" class="absolute inset-0 w-full h-full object-cover grayscale opacity-70 group-hover:grayscale-0 group-hover:opacity-100 transition-all duration-500" loading="lazy">
                        </div>
                        <div class="p-6">
                            <h3 class="text-lg font-bold font-display text-neutral mb-1">{{ $teacher['name'] }}</h3>
                            <p class="text-primary font-semibold text-sm mb-3">{{ $teacher['role'] }}</p>
                            <p class="text-xs text-neutral/70 leading-relaxed">{{ $teacher['bio'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-container>
    </x-section>
@endsection
