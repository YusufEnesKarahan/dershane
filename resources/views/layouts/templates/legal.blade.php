@extends('layouts.frontend')

@section('content')
    <x-page-header :title="$title ?? 'Yasal Metin'" :breadcrumbs="isset($breadcrumbs) ? $breadcrumbs : []" />
    
    <x-container class="py-12">
        <div class="max-w-3xl mx-auto bg-white p-8 rounded-premium-xl border border-slate-100 shadow-premium-md prose prose-neutral">
            <div class="text-xs text-slate/40 mb-4 select-none">
                Son Güncelleme: {{ isset($updated_at) ? $updated_at : date('d.m.Y') }}
            </div>
            
            <div class="text-sm font-sans text-slate/80 leading-relaxed space-y-6">
                {{ $slot ?? '' }}
                @yield('template-content')
            </div>
        </div>
    </x-container>
@endsection
