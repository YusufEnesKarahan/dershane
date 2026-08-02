@extends('layouts.frontend')

@php
    $seo = [
        'title' => 'Oturum Zaman Aşımı',
        'robots' => 'noindex, nofollow'
    ];
@endphp

@section('content')
    <x-container class="py-24 flex flex-col items-center justify-center text-center font-sans min-h-[60vh]">
        <span class="text-xs font-semibold text-warning uppercase tracking-widest bg-warning/10 px-3 py-1 rounded-full border border-warning/20 mb-4 select-none">
            HATA 419
        </span>
        <h1 class="text-3xl sm:text-5xl font-display font-extrabold text-neutral tracking-tight mb-4 leading-none">
            Oturum Süresi Doldu
        </h1>
        <p class="text-xs sm:text-sm text-neutral/50 max-w-md mb-8 leading-relaxed">
            Güvenliğiniz için oturumunuzun süresi doldu. İşleminize devam etmek için lütfen tekrar giriş yapın.
        </p>
        <div class="flex gap-3">
            <x-button variant="primary" onclick="window.location.href='{{ route('login') }}'">Tekrar Giriş Yap</x-button>
        </div>
    </x-container>
@endsection
