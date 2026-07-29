@extends('layouts.admin')
@section('title', 'License Details - ' . $license->license_key)

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-neutral-900 dark:text-white">{{ $license->plan }} Plan</h1>
            <p class="text-xs text-neutral-500 font-mono">{{ $license->license_key }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.platform.hq_central.licenses.index') }}" class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-neutral-700 dark:text-neutral-300 rounded-xl text-xs font-bold transition-colors">
                &larr; Back to Licenses
            </a>
            
            @if($license->status === 'active')
                <form action="{{ route('admin.platform.hq_central.licenses.suspend', $license->id) }}" method="POST" onsubmit="return confirm('Suspend this license?');">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-xl text-xs font-bold transition-colors border border-red-200">
                        Suspend License
                    </button>
                </form>
            @else
                <form action="{{ route('admin.platform.hq_central.licenses.activate', $license->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-100 hover:bg-green-200 text-green-700 rounded-xl text-xs font-bold transition-colors border border-green-200">
                        Activate License
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-4 rounded-xl text-sm font-bold border border-green-100">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Identity -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm lg:col-span-1">
            <h3 class="text-xs font-black text-neutral-500 uppercase tracking-wider mb-4 border-b border-neutral-100 dark:border-neutral-800 pb-2">Information</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-[10px] uppercase font-bold text-neutral-500">Status</span>
                    <span class="px-2 py-1 rounded text-[10px] font-bold uppercase 
                        @if($license->status === 'active') bg-green-100 text-green-700 
                        @elseif($license->status === 'expired') bg-red-100 text-red-700 
                        @elseif($license->status === 'suspended') bg-amber-100 text-amber-700
                        @else bg-neutral-100 text-neutral-700 @endif">
                        {{ $license->status }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[10px] uppercase font-bold text-neutral-500">Tenant</span>
                    <span class="text-sm font-bold text-indigo-600">{{ $license->tenant->name ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[10px] uppercase font-bold text-neutral-500">Instance</span>
                    <span class="text-sm font-bold text-neutral-900 dark:text-white">{{ $license->systemInstance->system_name ?? 'Not Assigned' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[10px] uppercase font-bold text-neutral-500">Start Date</span>
                    <span class="text-sm font-bold text-neutral-900 dark:text-white">{{ $license->starts_at ? $license->starts_at->format('Y-m-d') : 'N/A' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[10px] uppercase font-bold text-neutral-500">Expiration</span>
                    <span class="text-sm font-bold {{ $license->isExpired() ? 'text-red-600' : 'text-green-600' }}">
                        {{ $license->expires_at ? $license->expires_at->format('Y-m-d') : 'Lifetime' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Features -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm lg:col-span-2">
            <h3 class="text-xs font-black text-neutral-500 uppercase tracking-wider mb-4 border-b border-neutral-100 dark:border-neutral-800 pb-2">Module Permissions</h3>
            
            <div class="mb-6">
                <form action="{{ route('admin.platform.hq_central.licenses.toggleFeature', $license->id) }}" method="POST" class="flex gap-2">
                    @csrf
                    <input type="text" name="feature_name" placeholder="E.g. accounting, crm, remote_commands" required class="flex-1 bg-neutral-50 dark:bg-neutral-800 border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 text-neutral-900 dark:text-white font-mono">
                    <input type="hidden" name="enabled" value="1">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition-colors">Grant Feature</button>
                </form>
            </div>

            <ul class="space-y-3">
                @forelse($license->licenseFeatures as $feature)
                    <li class="flex items-center justify-between p-4 rounded-xl border {{ $feature->enabled ? 'border-green-200 bg-green-50 dark:border-green-900 dark:bg-green-900/20' : 'border-neutral-200 bg-neutral-50 dark:border-neutral-800 dark:bg-neutral-800' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full {{ $feature->enabled ? 'bg-green-500' : 'bg-neutral-400' }}"></div>
                            <span class="font-mono text-sm font-bold text-neutral-900 dark:text-white">{{ $feature->feature_name }}</span>
                        </div>
                        <form action="{{ route('admin.platform.hq_central.licenses.toggleFeature', $license->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="feature_name" value="{{ $feature->feature_name }}">
                            <input type="hidden" name="enabled" value="{{ $feature->enabled ? '0' : '1' }}">
                            <button type="submit" class="text-[10px] uppercase font-bold px-3 py-1 rounded {{ $feature->enabled ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }} transition-colors">
                                {{ $feature->enabled ? 'Revoke' : 'Enable' }}
                            </button>
                        </form>
                    </li>
                @empty
                    <li class="text-center text-xs font-bold text-neutral-500 py-4">No granular features specifically granted/revoked.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
