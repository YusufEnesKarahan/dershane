@extends('layouts.admin')
@section('title', 'Özellik Yönetimi (Feature Flags)')

@section('content')
<div class="space-y-6">
    <div class="bg-white dark:bg-slate-900 p-8 rounded-3xl text-slate-900 dark:text-slate-100 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <h1 class="text-2xl font-black mt-2">Özellik Yönetimi (Feature Flags)</h1>
            <p class="text-xs text-slate-300 mt-1">SaaS modüllerini ve platform özelliklerini açıp kapatın.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-100 text-green-800 rounded-xl font-medium text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm">
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50 dark:bg-slate-800/50">
                    <tr>
                        <th class="px-4 py-3 rounded-tl-lg">Özellik (Name)</th>
                        <th class="px-4 py-3">Durum</th>
                        <th class="px-4 py-3">Metadata (Plan)</th>
                        <th class="px-4 py-3 rounded-tr-lg text-right">İşlem</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($flags as $flag)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                        <td class="px-4 py-4 font-medium">{{ $flag->name }}</td>
                        <td class="px-4 py-4">
                            @if($flag->enabled)
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-bold">Aktif</span>
                            @else
                                <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-bold">Pasif</span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <span class="text-xs text-slate-500 font-mono">
                                {{ $flag->metadata ? json_encode($flag->metadata) : '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-right">
                            <form action="{{ route('admin.platform.features.toggle', $flag) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 text-xs font-bold rounded {{ $flag->enabled ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'bg-green-50 text-green-600 hover:bg-green-100' }}">
                                    {{ $flag->enabled ? 'Kapat' : 'Aç' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-slate-500">Kayıtlı özellik (feature flag) bulunamadı.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
