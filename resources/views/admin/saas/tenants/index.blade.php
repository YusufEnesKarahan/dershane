@extends('layouts.admin')
@section('title', 'SaaS Tenants')

@section('content')
<div class="space-y-6">
    <div class="bg-white dark:bg-slate-900 p-8 rounded-3xl text-slate-900 dark:text-slate-100 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <h1 class="text-2xl font-black mt-2">Dershane / Tenant Yönetimi</h1>
            <p class="text-xs text-slate-300 mt-1">Platform üzerindeki tüm müşterileri (dershaneleri) merkezi olarak yönetin.</p>
        </div>
        @if($license)
        <div class="flex items-center gap-2">
            <span class="px-3 py-1 text-xs font-bold rounded-full {{ $license->status == 'active' ? 'bg-green-500/20 text-green-300 border border-green-500/30' : ($license->status == 'suspended' ? 'bg-red-500/20 text-red-300 border border-red-500/30' : 'bg-amber-500/20 text-amber-300 border border-amber-500/30') }}">
                Lisans: {{ ucfirst($license->status) }}
            </span>
        </div>
        @endif
    </div>

    <x-card>
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <h3 class="text-lg font-bold">Kayıtlı Dershaneler</h3>
            <form method="GET" action="{{ route('admin.saas.tenants.index') }}" class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Dershane, Email, Telefon..." class="border border-slate-300 rounded-lg p-2 dark:bg-slate-800 dark:border-slate-700 text-sm">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg text-sm">Filtrele</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-500 dark:text-slate-400">
                <thead class="text-xs text-slate-700 uppercase bg-slate-50 dark:bg-slate-800 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3">Dershane (Tenant)</th>
                        <th class="px-4 py-3">İletişim</th>
                        <th class="px-4 py-3">Kayıt Tarihi</th>
                        <th class="px-4 py-3 text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tenants as $tenant)
                        <tr class="border-b dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50">
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-900 dark:text-white">{{ $tenant->name }}</div>
                                <div class="text-xs text-slate-500">{{ $tenant->slug }}</div>
                            </td>
                            <td class="px-4 py-3 text-xs">
                                <div>{{ $tenant->email }}</div>
                                <div>{{ $tenant->phone }}</div>
                            </td>
                            <td class="px-4 py-3">{{ $tenant->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.saas.tenants.show', $tenant->id) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 font-semibold text-xs">Detaylar</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $tenants->links() }}
        </div>
    </x-card>
</div>
@endsection
