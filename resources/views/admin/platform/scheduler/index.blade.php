@extends('layouts.admin')
@section('title', 'HQ Scheduler & Tasks')

@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-slate-900 to-indigo-900 p-8 rounded-3xl text-white shadow-premium flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <h1 class="text-2xl font-black mt-2">HQ Scheduler & Auto Sync</h1>
            <p class="text-xs text-slate-300 mt-1">Sistemdeki periyodik arka plan görevlerinin durumunu ve geçmişini izleyin.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-neutral-900 p-4 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <span class="text-[10px] font-bold text-neutral-500 uppercase tracking-wider">Scheduler Status</span>
            <p class="text-lg font-black mt-1 {{ $metrics['scheduler_enabled'] ? 'text-green-600' : 'text-red-600' }}">
                {{ $metrics['scheduler_enabled'] ? 'ENABLED' : 'DISABLED' }}
            </p>
        </div>
        <div class="bg-white dark:bg-neutral-900 p-4 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <span class="text-[10px] font-bold text-neutral-500 uppercase tracking-wider">Last Telemetry Run</span>
            <p class="text-lg font-black text-neutral-900 dark:text-white mt-1">
                {{ $metrics['last_telemetry'] ? $metrics['last_telemetry']->finished_at->format('d M H:i') : 'Yok' }}
            </p>
        </div>
        <div class="bg-white dark:bg-neutral-900 p-4 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <span class="text-[10px] font-bold text-neutral-500 uppercase tracking-wider">Last Heartbeat</span>
            <p class="text-lg font-black text-neutral-900 dark:text-white mt-1">
                {{ $metrics['last_heartbeat'] ? $metrics['last_heartbeat']->finished_at->format('d M H:i') : 'Yok' }}
            </p>
        </div>
        <div class="bg-white dark:bg-neutral-900 p-4 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <span class="text-[10px] font-bold text-neutral-500 uppercase tracking-wider">Failed Tasks</span>
            <p class="text-lg font-black mt-1 {{ $metrics['failed_tasks'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                {{ $metrics['failed_tasks'] }}
            </p>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-neutral-100 dark:border-neutral-800 flex justify-between items-center bg-neutral-50/50 dark:bg-neutral-800/50">
            <h3 class="font-bold text-neutral-800 dark:text-neutral-200">Task Execution Logs</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-neutral-600 dark:text-neutral-400">
                <thead class="bg-neutral-50 dark:bg-neutral-800/50 text-neutral-500 font-bold uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Tarih</th>
                        <th class="px-6 py-4">Task Name</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Duration (ms)</th>
                        <th class="px-6 py-4 text-right">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    @forelse($logs as $log)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                            <td class="px-6 py-4 text-xs font-mono">{{ $log->started_at->format('d M Y, H:i:s') }}</td>
                            <td class="px-6 py-4 font-bold text-neutral-800 dark:text-neutral-300">{{ $log->task_name }}</td>
                            <td class="px-6 py-4 font-mono text-[10px] uppercase font-bold
                                {{ $log->status === 'success' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $log->status }}
                            </td>
                            <td class="px-6 py-4 text-xs font-mono">{{ $log->duration_ms }}</td>
                            <td class="px-6 py-4 text-right">
                                <button onclick="alert('{{ addslashes(json_encode($log->result ?? $log->error_message, JSON_PRETTY_PRINT)) }}')" class="px-3 py-1 bg-neutral-100 hover:bg-neutral-200 text-neutral-700 rounded font-bold text-xs">View</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-neutral-500 font-bold">
                                Hiçbir görev kaydı bulunamadı.
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
