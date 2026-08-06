<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-100 dark:bg-slate-950" x-data="{ darkMode: false, sidebarOpen: false, miniSidebar: false }" x-init="darkMode = JSON.parse(localStorage.getItem('darkMode')) || false; $watch('darkMode', val => localStorage.setItem('darkMode', val)); if(darkMode) document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark')">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name')) - Admin Panel</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full text-slate-900 dark:text-slate-100 antialiased selection:bg-blue-600 selection:text-white font-sans" :class="{ 'dark': darkMode }">
    <div id="app" class="flex h-screen overflow-hidden">
        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen" class="fixed inset-0 z-40 bg-slate-900/80 backdrop-blur-sm lg:hidden" @click="sidebarOpen = false" x-transition.opacity></div>

        <!-- Sidebar -->
        <x-admin.sidebar.layout />

        <!-- Main Content Wrapper -->
        <div class="flex flex-col flex-1 w-full min-w-0 transition-all duration-300 ease-in-out">
            <!-- Flash Messages -->
            <x-admin.flash-messages />
            
            <!-- Topbar -->
            <x-admin.topbar.layout />

            <!-- Content Area -->
            <main class="flex-1 overflow-y-auto bg-slate-100 dark:bg-slate-950 p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>