@extends('layouts.admin')
@section('title', 'HQ Synchronization Queue')

@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-blue-900 to-slate-900 p-8 rounded-3xl text-white shadow-premium flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <h1 class="text-2xl font-black mt-2">HQ Synchronization Queue</h1>
            <p class="text-xs text-slate-300 mt-1">Lokalde biriken veri senkronizasyon olaylarının (event) geçmişi.</p>
        </div>
    </div>

    <!-- Summary Widgets -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-neutral-500 uppercase tracking-wider">Bekleyen (Pending)</p>
                <p class="text-2xl font-black text-neutral-900 dark:text-white mt-1">{{ $metrics['pending'] }}</p>
            </div>
            <div class="w-10 h-10 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 dark:text-blue-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-neutral-500 uppercase tracking-wider">Tamamlanan</p>
                <p class="text-2xl font-black text-neutral-900 dark:text-white mt-1">{{ $metrics['completed'] }}</p>
            </div>
            <div class="w-10 h-10 rounded-full bg-green-50 dark:bg-green-900/20 flex items-center justify-center text-green-600 dark:text-green-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
        </div>

        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-neutral-500 uppercase tracking-wider">Başarısız (Failed)</p>
                <p class="text-2xl font-black text-neutral-900 dark:text-white mt-1">{{ $metrics['failed'] }}</p>
            </div>
            <div class="w-10 h-10 rounded-full bg-red-50 dark:bg-red-900/20 flex items-center justify-center text-red-600 dark:text-red-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
        </div>
    </div>

    <!-- Latest 20 Events -->
    <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-neutral-100 dark:border-neutral-800 flex justify-between items-center bg-neutral-50/50 dark:bg-neutral-800/50">
            <h3 class="font-bold text-neutral-800 dark:text-neutral-200">Son 20 İşlem Kaydı</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-neutral-600 dark:text-neutral-400">
                <thead class="bg-neutral-50 dark:bg-neutral-800/50 text-neutral-500 font-bold uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="px-6 py-4">ID</th>
                        <th class="px-6 py-4">Olay (Event)</th>
                        <th class="px-6 py-4">Durum</th>
                        <th class="px-6 py-4">Deneme</th>
                        <th class="px-6 py-4">Oluşturulma</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    @forelse($events as $event)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                            <td class="px-6 py-4 font-mono text-xs">{{ $event->id }}</td>
                            <td class="px-6 py-4 font-semibold text-neutral-900 dark:text-neutral-300">{{ $event->event_type }}</td>
                            <td class="px-6 py-4">
                                @if($event->status === 'pending')
                                    <span class="px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-700 font-bold text-[10px] uppercase">Bekliyor</span>
                                @elseif($event->status === 'processing')
                                    <span class="px-2.5 py-0.5 rounded-full bg-yellow-100 text-yellow-700 font-bold text-[10px] uppercase">İşleniyor</span>
                                @elseif($event->status === 'completed')
                                    <span class="px-2.5 py-0.5 rounded-full bg-green-100 text-green-700 font-bold text-[10px] uppercase">Tamamlandı</span>
                                @elseif($event->status === 'failed')
                                    <span class="px-2.5 py-0.5 rounded-full bg-red-100 text-red-700 font-bold text-[10px] uppercase" title="{{ $event->last_error }}">Başarısız</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs">{{ $event->retry_count }}</td>
                            <td class="px-6 py-4 text-xs">{{ $event->created_at->format('d M Y, H:i:s') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-neutral-500">
                                Henüz hiçbir kuyruk işlemi bulunmuyor.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
