<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('settings.app.name', 'Dershane SaaS') }}</title>

    <!-- Brand theme styling dynamically injected -->
    @themeStyles

    <!-- Scripts and Styles (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900 bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-lg p-8">
        {{ $slot ?? '' }}
        @yield('content')
    </div>
</body>
</html>
