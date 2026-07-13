@extends('layouts.frontend')

@section('content')
    <x-page-header :title="$title ?? ''" :breadcrumbs="isset($breadcrumbs) ? $breadcrumbs : []" />
    
    <x-container class="py-12">
        {{ $slot ?? '' }}
        @yield('template-content')
    </x-container>
@endsection
