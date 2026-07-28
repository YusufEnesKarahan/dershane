@extends('layouts.admin')
@section('title', 'HQ Communication (HTTP Sync)')

@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-blue-900 to-slate-900 p-8 rounded-3xl text-white shadow-premium flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <h1 class="text-2xl font-black mt-2">HQ HTTP Communication</h1>
            <p class="text-xs text-slate-300 mt-1">HQ Central Platform ile manuel, imzalı ve güvenli HTTP haberleşme arayüzü.</p>
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

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
            <h2 class="text-lg font-bold border-b border-neutral-100 dark:border-neutral-800 pb-2">Bağlantı Bilgileri</h2>
            
            <div class="space-y-3">
                <div class="flex justify-between items-center border-b border-neutral-100 dark:border-neutral-800 pb-2">
                    <span class="text-sm font-bold text-neutral-500">HQ URL</span>
                    <span class="text-sm font-mono font-bold">{{ $hqUrl }}</span>
                </div>
                <div class="flex justify-between items-center border-b border-neutral-100 dark:border-neutral-800 pb-2">
                    <span class="text-sm font-bold text-neutral-500">Bağlantı Durumu</span>
                    <span class="text-[10px] uppercase font-black px-2 py-0.5 rounded {{ $isConnected ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $isConnected ? 'Aktif' : 'Bağlı Değil (veya süre doldu)' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
            <h2 class="text-lg font-bold border-b border-neutral-100 dark:border-neutral-800 pb-2">Manuel Aksiyonlar</h2>
            
            <div class="grid grid-cols-2 gap-3">
                <form action="{{ route('admin.platform.communication.ping') }}" method="POST">
                    @csrf
                    <button class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-sm shadow transition">
                        Ping HQ
                    </button>
                </form>
                <form action="{{ route('admin.platform.communication.health') }}" method="POST">
                    @csrf
                    <button class="w-full px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-bold text-sm shadow transition">
                        Send Health
                    </button>
                </form>
                <form action="{{ route('admin.platform.communication.register') }}" method="POST">
                    @csrf
                    <button class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-sm shadow transition">
                        Register
                    </button>
                </form>
                <form action="{{ route('admin.platform.communication.sync') }}" method="POST">
                    @csrf
                    <button class="w-full px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-sm shadow transition">
                        Manual Sync
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Latest 20 Event Logs -->
    <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-neutral-100 dark:border-neutral-800 flex justify-between items-center bg-neutral-50/50 dark:bg-neutral-800/50">
            <h3 class="font-bold text-neutral-800 dark:text-neutral-200">Son 20 HTTP İsteği (Log)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-neutral-600 dark:text-neutral-400">
                <thead class="bg-neutral-50 dark:bg-neutral-800/50 text-neutral-500 font-bold uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Tarih</th>
                        <th class="px-6 py-4">Metod/URL</th>
                        <th class="px-6 py-4">Event</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Süre</th>
                        <th class="px-6 py-4">Sonuç</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    @forelse($logs as $log)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                            <td class="px-6 py-4 text-xs font-mono">{{ $log->created_at->format('d M Y, H:i:s') }}</td>
                            <td class="px-6 py-4">
                                <span class="font-black text-[10px] uppercase text-blue-600 mr-1">{{ $log->request_method }}</span>
                                <span class="font-mono text-xs">{{ \Illuminate\Support\Str::limit($log->request_url, 30) }}</span>
                            </td>
                            <td class="px-6 py-4 font-bold text-neutral-800 dark:text-neutral-300">{{ $log->event_type }}</td>
                            <td class="px-6 py-4 font-mono text-xs {{ $log->response_status == 200 ? 'text-green-600' : 'text-red-600' }}">{{ $log->response_status ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-xs">{{ $log->duration_ms }} ms</td>
                            <td class="px-6 py-4">
                                @if($log->success)
                                    <span class="px-2.5 py-0.5 rounded-full bg-green-100 text-green-700 font-bold text-[10px] uppercase">Başarılı</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full bg-red-100 text-red-700 font-bold text-[10px] uppercase">Hata</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-neutral-500 font-bold">
                                Hiçbir HTTP iletişim kaydı bulunamadı.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
