@extends('layouts.frontend')

@php
    $seo = [
        'title' => 'Yetkisiz Erişim',
        'robots' => 'noindex, nofollow'
    ];
@endphp

@section('content')
    <x-container class="py-24 flex flex-col items-center justify-center text-center font-sans min-h-[60vh]">
        <span class="text-xs font-semibold text-danger uppercase tracking-widest bg-danger/10 px-3 py-1 rounded-full border border-danger/20 mb-4 select-none">
            HATA 401
        </span>
        <h1 class="text-3xl sm:text-5xl font-display font-extrabold text-slate tracking-tight mb-4 leading-none">
            Yetkisiz Erişim
        </h1>
        <p class="text-xs sm:text-sm text-slate/50 max-w-md mb-8 leading-relaxed">
            Bu sayfayı görüntülemek için giriş yapmanız gerekmektedir. Lütfen giriş yaparak tekrar deneyin.
        </p>
        <div class="flex gap-3">
            <x-button variant="primary" onclick="window.location.href='{{ route('login') }}'">Giriş Yap</x-button>
        </div>
    </x-container>
@endsection
