@extends('layouts.templates.landing')
@section('template-content')
    <x-section bg="gray" py="24" class="min-h-screen flex flex-col items-center justify-center text-center">
        <h1 class="text-6xl font-display font-bold text-neutral mb-4">419</h1>
        <p class="text-xl text-neutral-600 mb-8">Oturumunuzun süresi doldu. Lütfen tekrar deneyin.</p>
        <a href="{{ route('login') }}" class="inline-flex px-6 py-3 bg-primary text-white font-medium rounded-xl hover:bg-primary-dark transition">Tekrar Giriş Yap</a>
    </x-section>
@endsection
