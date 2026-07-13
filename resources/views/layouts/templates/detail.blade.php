@extends('layouts.frontend')

@section('content')
    <x-page-header :title="$title ?? ''" :breadcrumbs="isset($breadcrumbs) ? $breadcrumbs : []" />
    
    <x-container class="py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main details content -->
            <div class="lg:col-span-2 space-y-6">
                {{ $slot ?? '' }}
                @yield('template-content')
            </div>

            <!-- Sticky Sidebar metadata box -->
            @if (isset($sidebar))
                <div class="lg:col-span-1">
                    <div class="sticky top-24 space-y-6">
                        {{ $sidebar }}
                    </div>
                </div>
            @endif
        </div>
    </x-container>
@endsection
