@extends('layouts.frontend')

@php
    $seo = [
        'title' => 'Sistem Hatası',
        'robots' => 'noindex, nofollow'
    ];
@endphp

@section('content')
    <x-container class="py-24 flex flex-col items-center justify-center text-center font-sans min-h-[60vh]">
        <span class="text-xs font-semibold text-danger uppercase tracking-widest bg-danger/10 px-3 py-1 rounded-full border border-danger/20 mb-4 select-none">
            HATA 500
        </span>
        <h1 class="text-3xl sm:text-5xl font-display font-extrabold text-neutral tracking-tight mb-4 leading-none">
            Sunucu Hatası
        </h1>
        <p class="text-xs sm:text-sm text-neutral/50 max-w-md mb-8 leading-relaxed">
            Şu anda sistemsel bir sorun yaşıyoruz. Mühendislerimiz konuyla ilgili bilgilendirildi ve sorunu çözmek için çalışıyorlar.
        </p>
        <div class="flex gap-3">
            <x-button variant="outline" onclick="window.location.reload()">Sayfayı Yenile</x-button>
            <x-button variant="primary" onclick="window.location.href='/'">Ana Sayfaya Git</x-button>
        </div>
    </x-container>
@endsection
