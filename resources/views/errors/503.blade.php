@extends('layouts.frontend')

@php
    $seo = [
        'title' => 'Bakım Çalışması',
        'robots' => 'noindex, nofollow'
    ];
@endphp

@section('content')
    <x-container class="py-24 flex flex-col items-center justify-center text-center font-sans min-h-[60vh]">
        <span class="text-xs font-semibold text-warning uppercase tracking-widest bg-warning/10 px-3 py-1 rounded-full border border-warning/20 mb-4 select-none">
            BAKIM ÇALIŞMASI
        </span>
        <h1 class="text-3xl sm:text-5xl font-display font-extrabold text-neutral tracking-tight mb-4 leading-none">
            Size Daha İyi Hizmet Vermek İçin Çalışıyoruz
        </h1>
        <p class="text-xs sm:text-sm text-neutral/50 max-w-md mb-8 leading-relaxed">
            Sistemimiz planlı bir güncelleme/bakım çalışması nedeniyle geçici olarak servis dışıdır. Lütfen daha sonra tekrar deneyin.
        </p>
        <div class="flex gap-3">
            <x-button variant="outline" onclick="window.location.reload()">Tekrar Dene</x-button>
        </div>
    </x-container>
@endsection
