@extends('layouts.admin')
@section('title', 'HQ Telemetry & Monitoring')

@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-blue-900 to-slate-900 p-8 rounded-3xl text-white shadow-premium flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <h1 class="text-2xl font-black mt-2">HQ Telemetry & Monitoring</h1>
            <p class="text-xs text-slate-300 mt-1">Sistem sağlık, kullanım ve performans verilerinin HQ paneline aktarıldığı güvenli, izole veri katmanı.</p>
        </div>
        <div>
            <form action="{{ route('admin.platform.telemetry.send') }}" method="POST">
                @csrf
                <button class="bg-white/10 hover:bg-white/20 text-white font-bold py-2 px-6 rounded-xl border border-white/20 shadow-premium transition-all active:scale-95 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Send Telemetry Snapshot
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-xl text-sm font-bold">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl text-sm font-bold">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-white dark:bg-neutral-900 p-4 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <span class="text-[10px] font-bold text-neutral-500 uppercase tracking-wider">System Health</span>
            <p class="text-lg font-black mt-1 {{ $currentHealth['status'] === 'healthy' ? 'text-green-600' : 'text-red-600' }}">
                {{ strtoupper($currentHealth['status']) }}
            </p>
        </div>
        <div class="bg-white dark:bg-neutral-900 p-4 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <span class="text-[10px] font-bold text-neutral-500 uppercase tracking-wider">Database Status</span>
            <p class="text-lg font-black mt-1 {{ $currentHealth['database_connected'] ? 'text-green-600' : 'text-red-600' }}">
                {{ $currentHealth['database_connected'] ? 'ONLINE' : 'OFFLINE' }}
            </p>
        </div>
        <div class="bg-white dark:bg-neutral-900 p-4 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <span class="text-[10px] font-bold text-neutral-500 uppercase tracking-wider">Storage Status</span>
            <p class="text-lg font-black mt-1 {{ $currentHealth['storage_writable'] ? 'text-green-600' : 'text-red-600' }}">
                {{ $currentHealth['storage_writable'] ? 'WRITABLE' : 'LOCKED' }}
            </p>
        </div>
        <div class="bg-white dark:bg-neutral-900 p-4 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <span class="text-[10px] font-bold text-neutral-500 uppercase tracking-wider">Current Version</span>
            <p class="text-lg font-black text-neutral-900 dark:text-white mt-1">{{ $currentSystem['app_version'] }}</p>
        </div>
        <div class="bg-white dark:bg-neutral-900 p-4 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <span class="text-[10px] font-bold text-neutral-500 uppercase tracking-wider">User Count (Total/Act)</span>
            <p class="text-lg font-black text-neutral-900 dark:text-white mt-1">
                {{ $currentUsage['total_users'] }} / <span class="text-blue-600">{{ $currentUsage['active_users'] }}</span>
            </p>
        </div>
        <div class="bg-white dark:bg-neutral-900 p-4 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <span class="text-[10px] font-bold text-neutral-500 uppercase tracking-wider">Last Telemetry</span>
            <p class="text-sm font-black text-neutral-900 dark:text-white mt-1 font-mono">
                {{ $lastLog ? $lastLog->generated_at->format('d M H:i') : 'Yok' }}
            </p>
        </div>
    </div>

    <!-- Telemetry Table -->
    <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-neutral-100 dark:border-neutral-800 flex justify-between items-center bg-neutral-50/50 dark:bg-neutral-800/50">
            <h3 class="font-bold text-neutral-800 dark:text-neutral-200">Telemetry Log History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-neutral-600 dark:text-neutral-400">
                <thead class="bg-neutral-50 dark:bg-neutral-800/50 text-neutral-500 font-bold uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Tarih</th>
                        <th class="px-6 py-4">UUID</th>
                        <th class="px-6 py-4">Type</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    @forelse($logs as $log)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                            <td class="px-6 py-4 text-xs font-mono">{{ $log->generated_at->format('d M Y, H:i:s') }}</td>
                            <td class="px-6 py-4 text-[10px] font-mono text-neutral-500">{{ \Illuminate\Support\Str::limit($log->uuid, 8) }}</td>
                            <td class="px-6 py-4 font-bold text-neutral-800 dark:text-neutral-300">{{ $log->type }}</td>
                            <td class="px-6 py-4 font-mono text-[10px] uppercase font-bold
                                {{ $log->status === 'success' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $log->status }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button onclick="alert('{{ addslashes(json_encode($log->payload, JSON_PRETTY_PRINT)) }}')" class="px-3 py-1 bg-neutral-100 hover:bg-neutral-200 text-neutral-700 rounded font-bold text-xs">Payload</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-neutral-500 font-bold">
                                Hiçbir telemetry kaydı bulunamadı.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-neutral-100 dark:border-neutral-800">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
