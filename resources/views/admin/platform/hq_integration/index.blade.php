@extends('layouts.admin')
@section('title', 'HQ Integration')

@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-blue-900 to-slate-900 p-8 rounded-3xl text-white shadow-premium flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <h1 class="text-2xl font-black mt-2">HQ Integration Status</h1>
            <p class="text-xs text-slate-300 mt-1">Dershane ERP merkezi yönetim bağlantı ve sistem kimlik bilgileri.</p>
        </div>
        <div class="flex items-center gap-4">
            <div class="text-right">
                <span class="block text-[10px] text-blue-300 uppercase tracking-wider font-bold">Bağlantı Durumu</span>
                <span class="block text-xl font-mono font-bold text-neutral-400">Offline (Local)</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
            <h2 class="text-lg font-bold text-neutral-900 dark:text-white border-b border-neutral-100 dark:border-neutral-800 pb-2">Sistem Kimliği</h2>
            
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-neutral-500 uppercase tracking-wider font-bold">System UUID</p>
                    <p class="text-sm font-mono text-neutral-800 dark:text-neutral-300">{{ $identity->uuid }}</p>
                </div>
                <div>
                    <p class="text-xs text-neutral-500 uppercase tracking-wider font-bold">Installation UUID</p>
                    <p class="text-sm font-mono text-neutral-800 dark:text-neutral-300">{{ $identity->installation_uuid }}</p>
                </div>
                <div>
                    <p class="text-xs text-neutral-500 uppercase tracking-wider font-bold">Product Version</p>
                    <p class="text-sm font-mono text-neutral-800 dark:text-neutral-300">{{ $identity->product_version ?? 'Unknown' }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
            <h2 class="text-lg font-bold text-neutral-900 dark:text-white border-b border-neutral-100 dark:border-neutral-800 pb-2">Lisans & Sağlık</h2>
            
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-neutral-500 uppercase tracking-wider font-bold">License Status</p>
                    <span class="px-2 py-1 bg-neutral-100 dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300 rounded text-xs font-bold uppercase tracking-wider border border-neutral-200 dark:border-neutral-700">
                        {{ $licenseStatus['status'] ?? 'None' }}
                    </span>
                </div>
                <div>
                    <p class="text-xs text-neutral-500 uppercase tracking-wider font-bold">Enabled Features</p>
                    <div class="flex flex-wrap gap-2 mt-1">
                        @forelse($enabledFeatures as $feature)
                            <span class="px-2 py-1 bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 rounded text-[10px] font-bold border border-indigo-200 dark:border-indigo-800/50">{{ $feature }}</span>
                        @empty
                            <span class="text-xs text-neutral-500">Yok</span>
                        @endforelse
                    </div>
                </div>
                <div>
                    <p class="text-xs text-neutral-500 uppercase tracking-wider font-bold">Last Health Check</p>
                    <p class="text-sm text-green-600 dark:text-green-400 font-bold flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        {{ $healthSummary['status'] }} <span class="text-xs text-neutral-400 font-normal">({{ $healthSummary['last_check'] }})</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
