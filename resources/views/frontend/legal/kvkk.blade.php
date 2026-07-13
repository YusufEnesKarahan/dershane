@extends('layouts.templates.landing')

@inject('demo', 'App\Core\Services\DemoContentService')

@php
    $legal = $demo->getLegal('kvkk');

    $seo = [
        'title' => 'KVKK Aydınlatma Metni',
        'description' => 'Kişisel Verilerin Korunması Kanunu kapsamında aydınlatma metnimiz.',
        'keywords' => 'kvkk, kişisel veriler, aydınlatma metni'
    ];
@endphp

@section('template-content')
    <x-section bg="dark" py="16" class="bg-gradient-to-br from-neutral-950 via-neutral-900 to-primary/20">
        <x-container>
            <h1 class="text-3xl font-display font-bold text-white">{{ $legal['title'] }}</h1>
        </x-container>
    </x-section>

    <x-section bg="white" py="16">
        <x-container>
            <div class="prose prose-neutral max-w-none">
                <p>{{ $legal['content'] }}</p>
                <!-- Genişletilmiş statik içerik eklenebilir -->
            </div>
        </x-container>
    </x-section>
@endsection
