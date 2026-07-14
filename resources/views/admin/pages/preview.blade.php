@extends('layouts.admin')
@section('title', 'Sayfa Önizleme')
@section('content')
    <x-admin.crud.index-layout title="Önizleme: {{ $page->title }}" description="Sayfanın web sitesinde nasıl görüneceğini önizleyin.">
        <x-slot name="actions">
            <a href="{{ route('admin.pages.index') }}" class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-sm font-medium rounded-xl transition">Geri Dön</a>
        </x-slot>

        <div class="bg-white dark:bg-neutral-900 p-8 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium max-w-4xl mx-auto prose dark:prose-invert">
            <h1>{{ $page->title }}</h1>
            @if($page->excerpt)
                <p class="lead text-neutral-500 font-medium border-l-4 border-primary pl-4 py-1">{{ $page->excerpt }}</p>
            @endif
            <div class="mt-6 text-neutral-800 dark:text-neutral-200">
                {!! nl2br(e($page->content)) !!}
            </div>
        </div>
    </x-admin.crud.index-layout>
@endsection
