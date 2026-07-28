@extends('layouts.admin')
@section('title', 'HQ API Settings')

@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-blue-900 to-slate-900 p-8 rounded-3xl text-white shadow-premium flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <h1 class="text-2xl font-black mt-2">HQ API Key Management</h1>
            <p class="text-xs text-slate-300 mt-1">Dershane ERP sisteminin HQ Panel bağlantısı için API Token yönetimi.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-6">
        <h2 class="text-lg font-bold text-neutral-900 dark:text-white border-b border-neutral-100 dark:border-neutral-800 pb-2">HQ API Token</h2>

        @if($token)
            <div class="space-y-4">
                <div>
                    <p class="text-xs text-neutral-500 uppercase tracking-wider font-bold">Token Değeri (Bearer)</p>
                    <div class="flex items-center gap-2 mt-1">
                        <input type="text" readonly value="{{ $token->token }}" class="w-full font-mono bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-neutral-800 rounded-xl px-4 py-2 text-sm text-neutral-800 dark:text-neutral-300">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                    <div>
                        <p class="text-xs text-neutral-500 uppercase tracking-wider font-bold">Oluşturulma Tarihi</p>
                        <p class="text-sm font-semibold text-neutral-800 dark:text-neutral-300 mt-1">{{ $token->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-neutral-500 uppercase tracking-wider font-bold">Son Kullanım</p>
                        <p class="text-sm font-semibold text-neutral-800 dark:text-neutral-300 mt-1">{{ $token->last_used_at ? $token->last_used_at->format('d M Y, H:i') : 'Kullanılmadı' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-neutral-500 uppercase tracking-wider font-bold">Durum</p>
                        <span class="inline-block px-2.5 py-0.5 mt-1 text-[10px] font-bold uppercase rounded-full {{ $token->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $token->is_active ? 'Aktif' : 'Pasif' }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-4">
                    <form action="{{ route('admin.platform.api.regenerate') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs transition shadow-sm">
                            Yeniden Oluştur
                        </button>
                    </form>

                    <form action="{{ route('admin.platform.api.revoke') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl text-xs transition shadow-sm">
                            İptal Et (Revoke)
                        </button>
                    </form>
                </div>
            </div>
        @else
            <div class="text-center py-6">
                <p class="text-sm text-neutral-500">Aktif bir HQ API Token bulunmuyor.</p>
                <form action="{{ route('admin.platform.api.regenerate') }}" method="POST" class="mt-4">
                    @csrf
                    <button type="submit" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs transition shadow-sm">
                        Token Oluştur
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
