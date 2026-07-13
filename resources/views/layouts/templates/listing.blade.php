@extends('layouts.frontend')

@section('content')
    <x-page-header :title="$title ?? ''" :breadcrumbs="isset($breadcrumbs) ? $breadcrumbs : []" />
    
    <x-container class="py-12 space-y-8">
        <!-- Filter Header block if available -->
        @if (isset($filters))
            <div class="mb-8">
                {{ $filters }}
            </div>
        @endif

        <!-- Listing grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {{ $slot ?? '' }}
            @yield('template-content')
        </div>

        <!-- Pagination slot -->
        @if (isset($pagination))
            <div class="mt-12">
                {{ $pagination }}
            </div>
        @endif
    </x-container>
@endsection
