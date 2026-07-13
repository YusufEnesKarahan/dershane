@extends('layouts.frontend')

@section('content')
    <!-- Full width sections without global container limitations -->
    <div class="w-full flex flex-col">
        {{ $slot ?? '' }}
        @yield('template-content')
    </div>
@endsection
