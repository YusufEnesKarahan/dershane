@extends('layouts.admin')
@section('title', 'HQ Update Management')

@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-slate-900 to-indigo-900 p-8 rounded-3xl text-white shadow-premium flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <h1 class="text-2xl font-black mt-2">HQ Update Foundation</h1>
            <p class="text-xs text-slate-300 mt-1">Manage system version tracking and securely fetch update metadata from HQ.</p>
        </div>
        <div>
            <form action="{{ route('admin.platform.updates.check') }}" method="POST">
                @csrf
                <button type="submit" class="bg-white text-indigo-900 px-6 py-2 rounded-xl font-bold hover:bg-neutral-100 transition-colors">
                    Check Updates
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-200 text-green-800 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-200 text-red-800 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-neutral-900 p-4 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <span class="text-[10px] font-bold text-neutral-500 uppercase tracking-wider">Current Version</span>
            <p class="text-lg font-black mt-1 text-indigo-600">
                v{{ $currentVersion }}
            </p>
        </div>
        <div class="bg-white dark:bg-neutral-900 p-4 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <span class="text-[10px] font-bold text-neutral-500 uppercase tracking-wider">Latest Available</span>
            <p class="text-lg font-black text-neutral-900 dark:text-white mt-1">
                {{ $latestUpdate ? 'v' . $latestUpdate->version : 'None' }}
            </p>
        </div>
        <div class="bg-white dark:bg-neutral-900 p-4 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <span class="text-[10px] font-bold text-neutral-500 uppercase tracking-wider">Update Channel</span>
            <p class="text-lg font-black text-neutral-900 dark:text-white mt-1">
                {{ strtoupper(config('hq.updates.channel', 'stable')) }}
            </p>
        </div>
        <div class="bg-white dark:bg-neutral-900 p-4 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <span class="text-[10px] font-bold text-neutral-500 uppercase tracking-wider">Update System</span>
            <p class="text-lg font-black mt-1 {{ config('hq.updates.enabled') ? 'text-green-600' : 'text-red-600' }}">
                {{ config('hq.updates.enabled') ? 'ENABLED' : 'DISABLED' }}
            </p>
        </div>
    </div>

    <!-- Updates Table -->
    <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-neutral-100 dark:border-neutral-800 flex justify-between items-center bg-neutral-50/50 dark:bg-neutral-800/50">
            <h3 class="font-bold text-neutral-800 dark:text-neutral-200">Update History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-neutral-600 dark:text-neutral-400">
                <thead class="bg-neutral-50 dark:bg-neutral-800/50 text-neutral-500 font-bold uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Version</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    @forelse($updates as $update)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-neutral-800 dark:text-neutral-300">
                                v{{ $update->version }}
                                <span class="text-[10px] text-neutral-400 block">{{ $update->channel }}</span>
                            </td>
                            <td class="px-6 py-4 font-mono text-[10px] uppercase font-bold
                                @if($update->status === 'installed') text-green-600 
                                @elseif($update->status === 'available') text-blue-600
                                @elseif($update->status === 'failed') text-red-600
                                @else text-yellow-600 @endif">
                                {{ $update->status }}
                            </td>
                            <td class="px-6 py-4 text-xs font-mono">
                                {{ $update->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($update->status !== 'installed')
                                <form action="{{ route('admin.platform.updates.mark-installed', $update) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-green-100 hover:bg-green-200 text-green-700 rounded font-bold text-xs">Mark Installed</button>
                                </form>
                                @endif
                                <button onclick="alert('{{ addslashes(json_encode($update->metadata, JSON_PRETTY_PRINT)) }}')" class="px-3 py-1 bg-neutral-100 hover:bg-neutral-200 text-neutral-700 rounded font-bold text-xs">Metadata</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-neutral-500 font-bold">
                                Hiçbir güncelleme kaydı bulunamadı.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-neutral-100 dark:border-neutral-800">
            {{ $updates->links() }}
        </div>
    </div>
</div>
@endsection
