<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, minimum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="{{ app('saas.theme')->getPrimaryColor() }}">

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ app('saas.theme')->getFaviconPath() }}" type="image/x-icon">

    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Dynamic SEO & Metadata (Supports custom array or fallbacks) -->
    @seo($seo ?? [])

    <!-- Brand theme custom CSS variables injected -->
    @themeStyles

    <!-- Vite Styles and Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Additional Custom Styles Stack -->
    @stack('styles')
</head>
<body class="h-full bg-background text-neutral font-sans antialiased flex flex-col min-h-screen">
    <!-- Navbar Layout -->
    <x-navbar />

    <!-- Main Content Area -->
    <main class="flex-grow flex flex-col" id="main-content" role="main">
        @if (isset($slot))
            {{ $slot }}
        @else
            @yield('content')
        @endif
    </main>

    <!-- Footer Layout -->
    <x-footer />

    <!-- Additional Custom Scripts Stack -->
    @stack('scripts')
</body>
</html>
