@extends('layouts.admin')
@section('title', 'Sistem Sağlığı')

@section('content')
<div class="space-y-6">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-8 rounded-3xl text-slate-900 dark:text-slate-100 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <h1 class="text-2xl font-black mt-2">Sistem Sağlığı</h1>
            <p class="text-xs text-slate-300 mt-1">Platform altyapısının çalışma durumu, cron izi ve kaynak kontrolleri.</p>
        </div>
        <a href="{{ route('admin.saas.tenants.index') }}" class="bg-white/20 hover:bg-white/30 text-white font-semibold py-2 px-4 rounded-lg text-sm">
            Tenantlara Dön
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <x-card>
            <span class="text-xs text-slate-500 block mb-1">Laravel Versiyonu</span>
            <div class="text-lg font-black text-slate-900 dark:text-white">{{ $metrics['laravel_version'] }}</div>
        </x-card>
        <x-card>
            <span class="text-xs text-slate-500 block mb-1">PHP Versiyonu</span>
            <div class="text-lg font-black text-slate-900 dark:text-white">{{ $metrics['php_version'] }}</div>
        </x-card>
        <x-card>
            <span class="text-xs text-slate-500 block mb-1">Environment</span>
            <div class="text-lg font-black text-slate-900 dark:text-white">{{ strtoupper($metrics['environment']) }}</div>
        </x-card>
        <x-card>
            <span class="text-xs text-slate-500 block mb-1">Cache Driver</span>
            <div class="text-lg font-black text-slate-900 dark:text-white">{{ $metrics['cache_driver'] }}</div>
        </x-card>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <x-card>
            <h3 class="text-lg font-bold mb-4 border-b pb-2 dark:border-slate-700">Servis Durumları</h3>
            <div class="space-y-3 text-sm">
                <div class="flex items-center justify-between">
                    <span>Queue</span>
                    <x-admin.badge variant="{{ $metrics['queue']['status'] === 'healthy' ? 'success' : 'warning' }}">{{ ucfirst($metrics['queue']['status']) }}</x-admin.badge>
                </div>
                <div class="flex items-center justify-between">
                    <span>Storage</span>
                    <x-admin.badge variant="{{ $metrics['storage']['status'] === 'healthy' ? 'success' : 'danger' }}">{{ ucfirst($metrics['storage']['status']) }}</x-admin.badge>
                </div>
                <div class="flex items-center justify-between">
                    <span>Database</span>
                    <x-admin.badge variant="{{ $metrics['database']['status'] === 'healthy' ? 'success' : 'danger' }}">{{ ucfirst($metrics['database']['status']) }}</x-admin.badge>
                </div>
                <div class="flex items-center justify-between">
                    <span>Genel Durum</span>
                    <x-admin.badge variant="{{ $metrics['overall_status'] === 'healthy' ? 'success' : ($metrics['overall_status'] === 'warning' ? 'warning' : 'danger') }}">{{ ucfirst($metrics['overall_status']) }}</x-admin.badge>
                </div>
            </div>
        </x-card>

        <x-card>
            <h3 class="text-lg font-bold mb-4 border-b pb-2 dark:border-slate-700">Teknik Detaylar</h3>
            <div class="space-y-3 text-sm">
                <div>
                    <span class="text-xs text-slate-500 block">Queue Driver</span>
                    <span class="font-bold">{{ $metrics['queue']['driver'] }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 block">Storage Disk</span>
                    <span class="font-bold">{{ $metrics['storage']['disk'] }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 block">Database Connection</span>
                    <span class="font-bold">{{ $metrics['database']['connection'] }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 block">Son Başarılı Cron</span>
                    <span class="font-bold">{{ $metrics['last_successful_cron_at'] ? $metrics['last_successful_cron_at']->format('d M Y, H:i') : '-' }}</span>
                </div>
            </div>
        </x-card>

        <x-card>
            <h3 class="text-lg font-bold mb-4 border-b pb-2 dark:border-slate-700">Erişilebilirlik</h3>
            <div class="space-y-3 text-sm">
                <div>
                    <span class="text-xs text-slate-500 block">Storage</span>
                    <span class="font-bold">{{ $metrics['storage']['path'] ?? $metrics['storage']['driver'] }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 block">Queue Pending Jobs</span>
                    <span class="font-bold">{{ $metrics['queue']['pending_jobs'] ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 block">Cache</span>
                    <span class="font-bold">{{ $metrics['cache_driver'] }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 block">Database Error</span>
                    <span class="font-bold">{{ $metrics['database']['error'] ?? '-' }}</span>
                </div>
            </div>
        </x-card>
    </div>
</div>
@endsection