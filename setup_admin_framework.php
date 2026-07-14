<?php

function createView($path, $content) {
    $dir = dirname(__DIR__ . '/resources/views/' . $path);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    file_put_contents(__DIR__ . '/resources/views/' . $path, $content);
}

// Admin Layout
$views = [
    'layouts/admin.blade.php' => '<!DOCTYPE html>
<html lang="{{ str_replace(\'_\', \'-\', app()->getLocale()) }}" class="h-full bg-gray-50 dark:bg-neutral-900" x-data="{ darkMode: false, sidebarOpen: false, miniSidebar: false }" x-init="darkMode = JSON.parse(localStorage.getItem(\'darkMode\')) || false; $watch(\'darkMode\', val => localStorage.setItem(\'darkMode\', val)); if(darkMode) document.documentElement.classList.add(\'dark\'); else document.documentElement.classList.remove(\'dark\')">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield(\'title\', config(\'app.name\')) - Admin Panel</title>
    @vite([\'resources/css/app.css\', \'resources/js/app.js\'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full text-neutral-800 dark:text-neutral-200 antialiased selection:bg-primary selection:text-white" :class="{ \'dark\': darkMode }">
    <div id="app" class="flex h-screen overflow-hidden">
        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen" class="fixed inset-0 z-40 bg-neutral-900/80 backdrop-blur-sm lg:hidden" @click="sidebarOpen = false" x-transition.opacity></div>

        <!-- Sidebar -->
        <x-admin.sidebar.layout />

        <!-- Main Content Wrapper -->
        <div class="flex flex-col flex-1 w-full min-w-0 transition-all duration-300 ease-in-out">
            <!-- Topbar -->
            <x-admin.topbar.layout />

            <!-- Content Area -->
            <main class="flex-1 overflow-y-auto bg-neutral-50 dark:bg-neutral-950 p-4 sm:p-6 lg:p-8">
                @yield(\'content\')
            </main>
        </div>
    </div>
</body>
</html>',

    // Sidebar components
    'components/admin/sidebar/layout.blade.php' => '<aside :class="miniSidebar ? \'w-20\' : \'w-64\'" class="fixed inset-y-0 left-0 z-50 flex flex-col transition-all duration-300 ease-in-out bg-white dark:bg-neutral-900 border-r border-neutral-200 dark:border-neutral-800 lg:static lg:translate-x-0" :class="sidebarOpen ? \'translate-x-0\' : \'-translate-x-full\'">
    <div class="flex items-center justify-between h-16 px-4 border-b border-neutral-200 dark:border-neutral-800 shrink-0">
        <a href="{{ route(\'dashboard\') }}" class="flex items-center gap-2 font-display font-bold text-xl text-primary truncate">
            <svg class="w-8 h-8 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            <span x-show="!miniSidebar" x-transition.opacity class="truncate">Dershane</span>
        </a>
        <button @click="miniSidebar = !miniSidebar" class="hidden lg:block text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
    </div>
    <div class="flex-1 overflow-y-auto py-4 space-y-1 custom-scrollbar">
        @php
            $menuService = app(\App\Domain\Auth\Services\AdminMenuService::class);
            $menus = $menuService->getSidebarMenu();
        @endphp
        @foreach($menus as $menu)
            @if(isset($menu[\'children\']))
                <x-admin.sidebar.group :menu="$menu" />
            @else
                <x-admin.sidebar.item :menu="$menu" />
            @endif
        @endforeach
    </div>
</aside>',

    'components/admin/sidebar/item.blade.php' => '@props([\'menu\'])
<a href="{{ isset($menu[\'route\']) ? route($menu[\'route\']) : \'#\' }}" class="flex items-center px-4 py-2 text-sm font-medium transition-colors group {{ request()->routeIs($menu[\'route\'] ?? \'\') ? \'text-primary bg-primary/10 dark:bg-primary/20 border-r-2 border-primary\' : \'text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800/50 hover:text-neutral-900 dark:hover:text-neutral-200\' }}">
    <div class="shrink-0 flex items-center justify-center" :class="miniSidebar ? \'w-full\' : \'w-6 mr-3\'">
        <!-- Generic Icon Placeholder -->
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="12" r="10"/></svg>
    </div>
    <span x-show="!miniSidebar" x-transition.opacity class="truncate">{{ $menu[\'title\'] }}</span>
</a>',

    'components/admin/sidebar/group.blade.php' => '@props([\'menu\'])
<div x-data="{ open: false }" class="space-y-1">
    <button @click="open = !open" :class="miniSidebar ? \'justify-center px-2\' : \'px-4\'" class="w-full flex items-center py-2 text-sm font-medium text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800/50 transition-colors">
        <div class="shrink-0 flex items-center justify-center" :class="miniSidebar ? \'w-full\' : \'w-6 mr-3\'">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
        </div>
        <span x-show="!miniSidebar" x-transition.opacity class="flex-1 text-left truncate">{{ $menu[\'title\'] }}</span>
        <svg x-show="!miniSidebar" :class="{ \'rotate-180\': open }" class="w-4 h-4 shrink-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    </button>
    <div x-show="open && !miniSidebar" x-collapse class="pl-11 pr-4 py-1 space-y-1">
        @foreach($menu[\'children\'] as $child)
            @if($child)
            <a href="{{ isset($child[\'route\']) ? route($child[\'route\']) : \'#\' }}" class="block py-2 text-sm {{ request()->routeIs($child[\'route\'] ?? \'\') ? \'text-primary font-medium\' : \'text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-200\' }} transition-colors">
                {{ $child[\'title\'] }}
            </a>
            @endif
        @endforeach
    </div>
</div>',

    // Topbar components
    'components/admin/topbar/layout.blade.php' => '<header class="flex items-center justify-between h-16 px-4 bg-white dark:bg-neutral-900 border-b border-neutral-200 dark:border-neutral-800 shrink-0 transition-colors">
    <div class="flex items-center gap-4">
        <button @click="sidebarOpen = true" class="lg:hidden text-neutral-500 hover:text-neutral-700 dark:hover:text-neutral-300">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <!-- Search -->
        <button class="hidden sm:flex items-center gap-2 px-3 py-1.5 text-sm text-neutral-400 bg-neutral-100 dark:bg-neutral-800 rounded-lg hover:bg-neutral-200 dark:hover:bg-neutral-700 transition-colors w-64">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <span>Search... (Ctrl+K)</span>
        </button>
    </div>
    <div class="flex items-center gap-4">
        <!-- Theme Toggle -->
        <button @click="darkMode = !darkMode; if(darkMode) document.documentElement.classList.add(\'dark\'); else document.documentElement.classList.remove(\'dark\');" class="text-neutral-500 hover:text-primary transition-colors">
            <svg x-show="!darkMode" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
            <svg x-show="darkMode" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        </button>
        <!-- User Dropdown -->
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" @click.away="open = false" class="flex items-center gap-2 focus:outline-none">
                <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-sm">
                    {{ substr(auth()->user()->name ?? \'A\', 0, 1) }}
                </div>
            </button>
            <div x-show="open" x-transition class="absolute right-0 mt-2 w-48 bg-white dark:bg-neutral-800 rounded-xl shadow-premium border border-neutral-100 dark:border-neutral-700 py-1 z-50">
                <div class="px-4 py-2 border-b border-neutral-100 dark:border-neutral-700">
                    <p class="text-sm font-medium text-neutral-900 dark:text-white">{{ auth()->user()->name ?? \'Admin\' }}</p>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 truncate">{{ auth()->user()->email ?? \'admin@test.com\' }}</p>
                </div>
                <form method="POST" action="{{ route(\'logout\') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-neutral-50 dark:hover:bg-neutral-700/50 transition-colors">Çıkış Yap</button>
                </form>
            </div>
        </div>
    </div>
</header>',

    // Widgets
    'components/admin/widgets/stat.blade.php' => '@props([\'title\', \'value\', \'icon\' => \'chart-bar\', \'trend\' => null])
<div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
    <div class="flex items-center justify-between">
        <div class="text-neutral-500 dark:text-neutral-400 text-sm font-medium">{{ $title }}</div>
        <div class="p-2 bg-primary/10 text-primary rounded-lg shrink-0">
            <!-- Icon placeholder -->
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="12" r="10"/></svg>
        </div>
    </div>
    <div class="mt-4 flex items-baseline gap-2">
        <div class="text-3xl font-display font-bold text-neutral-900 dark:text-white">{{ $value }}</div>
        @if($trend)
            <span class="text-sm font-medium {{ $trend > 0 ? \'text-green-500\' : \'text-red-500\' }}">
                {{ $trend > 0 ? \'+\' : \'\' }}{{ $trend }}%
            </span>
        @endif
    </div>
</div>',
    
    'components/admin/widgets/chart-placeholder.blade.php' => '@props([\'title\'])
<div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm h-72 flex flex-col">
    <h3 class="text-lg font-display font-semibold text-neutral-900 dark:text-white mb-4">{{ $title }}</h3>
    <div class="flex-1 bg-neutral-50 dark:bg-neutral-800/50 rounded-xl flex items-center justify-center border border-dashed border-neutral-200 dark:border-neutral-700">
        <p class="text-neutral-400 text-sm">Chart Placeholder</p>
    </div>
</div>',

    // Table System
    'components/admin/table/layout.blade.php' => '<div class="overflow-hidden bg-white dark:bg-neutral-900 shadow-premium-sm ring-1 ring-neutral-200 dark:ring-neutral-800 sm:rounded-2xl">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-800">
            <thead class="bg-neutral-50 dark:bg-neutral-900/50">
                {{ $head }}
            </thead>
            <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800 bg-white dark:bg-neutral-900">
                {{ $body }}
            </tbody>
        </table>
    </div>
    @if(isset($pagination))
        <div class="border-t border-neutral-200 dark:border-neutral-800 px-4 py-3 sm:px-6">
            {{ $pagination }}
        </div>
    @endif
</div>',
    
    'components/admin/table/th.blade.php' => '<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider whitespace-nowrap">
    {{ $slot }}
</th>',

    'components/admin/table/td.blade.php' => '<td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-700 dark:text-neutral-300">
    {{ $slot }}
</td>',

    // CRUD Layouts
    'components/admin/crud/index-layout.blade.php' => '@props([\'title\', \'description\' => null])
<div class="space-y-6">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-display font-bold text-neutral-900 dark:text-white">{{ $title }}</h1>
            @if($description)
                <p class="mt-2 text-sm text-neutral-600 dark:text-neutral-400">{{ $description }}</p>
            @endif
        </div>
        <div class="mt-4 sm:mt-0 flex gap-3">
            {{ $actions ?? \'\' }}
        </div>
    </div>
    {{ $slot }}
</div>',

    // Form System
    'components/admin/form/layout.blade.php' => '@props([\'action\', \'method\' => \'POST\'])
<form action="{{ $action }}" method="{{ $method === \'GET\' ? \'GET\' : \'POST\' }}" {{ $attributes->merge([\'class\' => \'space-y-6\']) }}>
    @if($method !== \'GET\')
        @csrf
        @if(!in_array(strtoupper($method), [\'GET\', \'POST\']))
            @method($method)
        @endif
    @endif
    {{ $slot }}
</form>',
    
    'components/admin/form/field-group.blade.php' => '@props([\'label\', \'id\' => null, \'error\' => null])
<div>
    <label {{ $id ? \'for=\'.$id : \'\' }} class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">
        {{ $label }}
    </label>
    {{ $slot }}
    @if($error)
        <p class="mt-1 text-sm text-red-500">{{ $error }}</p>
    @endif
</div>',

    // Dashboard Test Page (to verify)
    'admin/dashboard.blade.php' => '@extends(\'layouts.admin\')
@section(\'title\', \'Dashboard\')
@section(\'content\')
    <x-admin.crud.index-layout title="Overview" description="Welcome back to your administration dashboard.">
        <x-slot name="actions">
            <button class="px-4 py-2 bg-primary text-white text-sm font-medium rounded-xl hover:bg-primary-dark transition shadow-sm">
                Generate Report
            </button>
        </x-slot>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <x-admin.widgets.stat title="Total Students" value="1,240" trend="12" />
            <x-admin.widgets.stat title="Active Courses" value="48" trend="4" />
            <x-admin.widgets.stat title="New Leads" value="156" trend="-2" />
            <x-admin.widgets.stat title="Revenue" value="₺450K" trend="18" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
            <x-admin.widgets.chart-placeholder title="Revenue Growth" />
            <x-admin.widgets.chart-placeholder title="Student Enrollment" />
        </div>
        
        <div class="mt-8">
            <h3 class="text-lg font-medium text-neutral-900 dark:text-white mb-4">Recent Registrations</h3>
            <x-admin.table.layout>
                <x-slot name="head">
                    <x-admin.table.th>Name</x-admin.table.th>
                    <x-admin.table.th>Course</x-admin.table.th>
                    <x-admin.table.th>Status</x-admin.table.th>
                    <x-admin.table.th>Date</x-admin.table.th>
                </x-slot>
                <x-slot name="body">
                    <tr>
                        <x-admin.table.td>Ahmet Yılmaz</x-admin.table.td>
                        <x-admin.table.td>YKS Sayısal</x-admin.table.td>
                        <x-admin.table.td><span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Active</span></x-admin.table.td>
                        <x-admin.table.td>Today</x-admin.table.td>
                    </tr>
                    <tr>
                        <x-admin.table.td>Ayşe Demir</x-admin.table.td>
                        <x-admin.table.td>LGS Hazırlık</x-admin.table.td>
                        <x-admin.table.td><span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">Pending</span></x-admin.table.td>
                        <x-admin.table.td>Yesterday</x-admin.table.td>
                    </tr>
                </x-slot>
            </x-admin.table.layout>
        </div>
    </x-admin.crud.index-layout>
@endsection'
];

foreach ($views as $path => $content) {
    createView($path, $content);
}

echo "Admin framework views created.\n";
