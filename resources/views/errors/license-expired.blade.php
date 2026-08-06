@extends('layouts.frontend')

@php
    $seo = [
        'title' => 'Lisans Süresi Sona Erdi',
        'robots' => 'noindex, nofollow'
    ];
@endphp

@section('content')
    <x-container class="py-24 flex flex-col items-center justify-center text-center font-sans min-h-[60vh]">
        <div class="h-16 w-16 rounded-full bg-red-100 flex items-center justify-center mb-6 text-red-600">
            <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </div>
        <span class="text-xs font-semibold text-red-600 uppercase tracking-widest bg-red-50 px-3 py-1 rounded-full border border-red-200 mb-4 select-none">
            LİSANS UYARISI
        </span>
        <h1 class="text-3xl sm:text-4xl font-display font-extrabold text-slate-900 tracking-tight mb-4 leading-none">
            Lisans Süreniz Sona Ermiştir
        </h1>
        <p class="text-sm text-slate-600 max-w-md mb-8 leading-relaxed">
            Dershane abonelik ve lisans süreniz sona erdiği için bu işleme erişilemiyor. Lütfen sistem yöneticiniz ile iletişime geçin.
        </p>
        <div class="flex gap-3">
            <x-button variant="outline" onclick="history.back()">Geri Dön</x-button>
            <x-button variant="primary" onclick="window.location.href='/'">Ana Sayfaya Git</x-button>
        </div>
    </x-container>
@endsection
