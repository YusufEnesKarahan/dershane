<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Yönetim Paneli - {{ config('settings.app.name', 'Dershane SaaS') }}</title>

    <!-- Brand theme styling dynamically injected -->
    @themeStyles

    <!-- Scripts and Styles (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900 bg-gray-100">
    <div class="min-h-screen flex flex-col md:flex-row">
        <!-- Admin Sidebar Placeholder -->
        <aside class="w-full md:w-64 bg-slate-900 text-slate-100 flex-shrink-0">
            <div class="p-4 font-bold border-b border-slate-800">
                {{ config('settings.app.name', 'Dershane Admin') }}
            </div>
            <nav class="p-4 space-y-2">
                <!-- Navigation links go here -->
            </nav>
        </aside>

        <div class="flex-grow flex flex-col min-w-0">
            <!-- Header bar -->
            <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-6">
                <div>
                    <!-- Breadcrumb space -->
                    @yield('breadcrumb')
                </div>
                <div>
                    <!-- User menu -->
                </div>
            </header>

            <!-- Page Main Content -->
            <main class="p-6 flex-grow">
                {{ $slot ?? '' }}
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
