@extends('layouts.admin')
@section('title', 'License Status')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-neutral-900 dark:text-white">License Status</h1>
            <p class="text-xs text-neutral-500">Current license and feature enforcement state</p>
        </div>
        <a href="{{ route('admin.platform.hq_central.index') }}" class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-neutral-700 dark:text-neutral-300 rounded-xl text-xs font-bold transition-colors">
            HQ Dashboard
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- License Status -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <h3 class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-3">Status</h3>
            @if($cache)
                <span class="inline-flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full {{ $cache->isActive() ? 'bg-green-500 animate-pulse' : 'bg-red-500' }}"></div>
                    <span class="text-xl font-black {{ $cache->isActive() ? 'text-green-600' : 'text-red-600' }}">
                        {{ ucfirst($cache->status) }}
                    </span>
                </span>
            @else
                <span class="text-xl font-black text-neutral-400">Unknown</span>
                <p class="text-[10px] text-neutral-500 mt-1">No license check performed yet</p>
            @endif
        </div>

        <!-- Current Plan -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <h3 class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-3">Current Plan</h3>
            <p class="text-xl font-black text-indigo-600">{{ $cache?->plan ?? 'Not Set' }}</p>
        </div>

        <!-- Expiration -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <h3 class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-3">Expiration</h3>
            @if($cache && $cache->expires_at)
                <p class="text-xl font-black {{ $daysUntilExpiry !== null ? ($daysUntilExpiry > 30 ? 'text-green-600' : ($daysUntilExpiry > 7 ? 'text-amber-600' : 'text-red-600')) : 'text-red-600' }}">
                    @if($daysUntilExpiry !== null)
                        {{ $daysUntilExpiry }} days
                    @else
                        Expired
                    @endif
                </p>
                <p class="text-[10px] text-neutral-500 mt-1">{{ $cache->expires_at->format('Y-m-d') }}</p>
            @else
                <p class="text-xl font-black text-green-600">Lifetime</p>
            @endif
        </div>

        <!-- HQ Connection -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <h3 class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-3">HQ Connection</h3>
            <span class="inline-flex items-center gap-2">
                <div class="w-3 h-3 rounded-full {{ $hqConnected ? 'bg-green-500' : 'bg-red-500' }}"></div>
                <span class="text-xl font-black {{ $hqConnected ? 'text-green-600' : 'text-red-600' }}">
                    {{ $hqConnected ? 'Connected' : 'Disconnected' }}
                </span>
            </span>
            @if($cache && $cache->last_checked_at)
                <p class="text-[10px] text-neutral-500 mt-1">Last check: {{ $cache->last_checked_at->diffForHumans() }}</p>
            @endif
        </div>
    </div>

    <!-- Enabled Features -->
    <div class="bg-white dark:bg-neutral-900 p-6 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
        <h3 class="text-xs font-black text-neutral-500 uppercase tracking-wider mb-4 border-b border-neutral-100 dark:border-neutral-800 pb-2">Enabled Features</h3>
        @if(count($enabledFeatures) > 0)
            <div class="flex flex-wrap gap-2">
                @foreach($enabledFeatures as $feature)
                    <span class="px-3 py-1.5 bg-green-50 text-green-700 rounded-lg text-xs font-bold border border-green-200 dark:bg-green-900/20 dark:text-green-400 dark:border-green-800">
                        ✓ {{ $feature }}
                    </span>
                @endforeach
            </div>
        @else
            <p class="text-xs text-neutral-500 font-bold">No features cached yet.</p>
        @endif

        @if(count($disabledFeatures) > 0)
            <h4 class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mt-6 mb-3">Disabled Features</h4>
            <div class="flex flex-wrap gap-2">
                @foreach($disabledFeatures as $feature)
                    <span class="px-3 py-1.5 bg-neutral-50 text-neutral-500 rounded-lg text-xs font-bold border border-neutral-200 dark:bg-neutral-800 dark:text-neutral-400 dark:border-neutral-700">
                        ✗ {{ $feature }}
                    </span>
                @endforeach
            </div>
        @endif
    </div>

    <!-- System Identity -->
    <div class="bg-white dark:bg-neutral-900 p-6 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
        <h3 class="text-xs font-black text-neutral-500 uppercase tracking-wider mb-4 border-b border-neutral-100 dark:border-neutral-800 pb-2">System Identity</h3>
        @if($identity)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex justify-between items-center">
                    <span class="text-[10px] uppercase font-bold text-neutral-500">System UUID</span>
                    <span class="text-xs font-mono font-bold text-neutral-700 dark:text-neutral-300">{{ substr($identity->uuid, 0, 16) }}...</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[10px] uppercase font-bold text-neutral-500">License Key</span>
                    <span class="text-xs font-mono font-bold text-neutral-700 dark:text-neutral-300">{{ $identity->license_key ? substr($identity->license_key, 0, 12) . '...' : 'N/A' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[10px] uppercase font-bold text-neutral-500">Product</span>
                    <span class="text-xs font-bold text-neutral-700 dark:text-neutral-300">{{ $identity->product_name }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[10px] uppercase font-bold text-neutral-500">Version</span>
                    <span class="text-xs font-bold text-neutral-700 dark:text-neutral-300">{{ $identity->product_version }}</span>
                </div>
            </div>
        @else
            <p class="text-xs text-neutral-500 font-bold">No system identity configured.</p>
        @endif
    </div>
</div>
@endsection
