@extends('layouts.admin')
@section('title', 'HQ Remote Commands')

@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-blue-900 to-slate-900 p-8 rounded-3xl text-white shadow-premium flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <h1 class="text-2xl font-black mt-2">HQ Remote Commands</h1>
            <p class="text-xs text-slate-300 mt-1">HQ Central Platform tarafından gönderilen sistem komutlarının manuel denetim ve onay arayüzü.</p>
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

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <span class="text-xs font-bold text-neutral-500 uppercase tracking-wider">Bekleyen (Pending)</span>
            <p class="text-2xl font-black text-neutral-900 dark:text-white mt-1 text-blue-600">{{ $statistics['pending'] }}</p>
        </div>
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <span class="text-xs font-bold text-neutral-500 uppercase tracking-wider">Başarısız (Failed)</span>
            <p class="text-2xl font-black text-neutral-900 dark:text-white mt-1 text-red-600">{{ $statistics['failed'] }}</p>
        </div>
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <span class="text-xs font-bold text-neutral-500 uppercase tracking-wider">Toplam Komut</span>
            <p class="text-2xl font-black text-neutral-900 dark:text-white mt-1">{{ $statistics['total'] }}</p>
        </div>
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <span class="text-xs font-bold text-neutral-500 uppercase tracking-wider">Son Çalıştırma</span>
            <p class="text-sm font-black text-neutral-900 dark:text-white mt-1 font-mono">
                {{ $statistics['last_execution'] ? $statistics['last_execution']->format('d M H:i') : 'Yok' }}
            </p>
        </div>
    </div>

    <!-- Commands Table -->
    <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-neutral-100 dark:border-neutral-800 flex justify-between items-center bg-neutral-50/50 dark:bg-neutral-800/50">
            <h3 class="font-bold text-neutral-800 dark:text-neutral-200">Gelen Komutlar</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-neutral-600 dark:text-neutral-400">
                <thead class="bg-neutral-50 dark:bg-neutral-800/50 text-neutral-500 font-bold uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Tarih</th>
                        <th class="px-6 py-4">UUID</th>
                        <th class="px-6 py-4">Tip (Command)</th>
                        <th class="px-6 py-4">Durum</th>
                        <th class="px-6 py-4">Çalıştırılma</th>
                        <th class="px-6 py-4 text-right">Aksiyon</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    @forelse($commands as $command)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                            <td class="px-6 py-4 text-xs font-mono">{{ $command->requested_at->format('d M Y, H:i') }}</td>
                            <td class="px-6 py-4 text-[10px] font-mono text-neutral-500">{{ \Illuminate\Support\Str::limit($command->command_uuid, 8) }}</td>
                            <td class="px-6 py-4 font-bold text-neutral-800 dark:text-neutral-300">{{ $command->command_type }}</td>
                            <td class="px-6 py-4 font-mono text-[10px] uppercase font-bold
                                @if($command->status === 'pending') text-blue-600
                                @elseif($command->status === 'approved') text-indigo-600
                                @elseif($command->status === 'executed') text-green-600
                                @elseif($command->status === 'rejected') text-yellow-600
                                @elseif($command->status === 'failed') text-red-600
                                @endif
                            ">
                                {{ $command->status }}
                            </td>
                            <td class="px-6 py-4 text-xs">{{ $command->executed_at ? $command->executed_at->format('d M Y, H:i') : '-' }}</td>
                            <td class="px-6 py-4 flex justify-end gap-2">
                                @if($command->isPending())
                                    <form action="{{ route('admin.platform.commands.approve', $command->id) }}" method="POST">
                                        @csrf
                                        <button class="px-3 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded font-bold text-xs">Onayla (Approve)</button>
                                    </form>
                                    <form action="{{ route('admin.platform.commands.reject', $command->id) }}" method="POST">
                                        @csrf
                                        <button class="px-3 py-1 bg-yellow-50 hover:bg-yellow-100 text-yellow-700 rounded font-bold text-xs">Reddet (Reject)</button>
                                    </form>
                                @endif
                                
                                @if($command->status === 'approved')
                                    <form action="{{ route('admin.platform.commands.execute', $command->id) }}" method="POST">
                                        @csrf
                                        <button class="px-3 py-1 bg-green-500 hover:bg-green-600 text-white rounded font-bold text-xs shadow">Çalıştır (Execute)</button>
                                    </form>
                                @endif

                                @if($command->result)
                                    <button onclick="alert('{{ addslashes(json_encode($command->result, JSON_PRETTY_PRINT)) }}')" class="px-3 py-1 bg-neutral-100 hover:bg-neutral-200 text-neutral-700 rounded font-bold text-xs">Sonuç</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-neutral-500 font-bold">
                                HQ tarafından gelen hiçbir komut bulunmuyor.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-neutral-100 dark:border-neutral-800">
            {{ $commands->links() }}
        </div>
    </div>
</div>
@endsection
