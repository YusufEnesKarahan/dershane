<!DOCTYPE html>
<html lang="tr" class="h-full bg-slate-50 dark:bg-slate-950" x-data="{ darkMode: false }" x-init="darkMode = JSON.parse(localStorage.getItem('darkMode')) || false; if(darkMode) document.documentElement.classList.add('dark')">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dershane Kurulum Sihirbazı</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-slate-900 dark:text-slate-100" :class="{ 'dark': darkMode }">
    <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="flex justify-center mb-4">
                <div class="w-10 h-10 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold text-lg shadow-sm">D</div>
            </div>
            <h2 class="text-center text-2xl font-semibold text-slate-900 dark:text-slate-100">
                Dershane SaaS Platformu
            </h2>
            <p class="mt-2 text-center text-sm text-slate-500 dark:text-slate-400">
                Saniyeler içinde dijital dershane kurumunuzu oluşturun
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white dark:bg-slate-900 py-8 px-4 shadow-sm sm:rounded-xl sm:px-10 border border-slate-200 dark:border-slate-800">
                @if(session('error'))
                    <div class="mb-4 p-4 text-sm text-red-700 dark:text-red-300 bg-red-50 dark:bg-red-950/30 rounded-lg border border-red-200 dark:border-red-800">
                        {{ session('error') }}
                    </div>
                @endif
                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
