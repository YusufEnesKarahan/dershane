@extends('layouts.admin')
@section('title', 'Veli Bildirimleri')
@section('content')
    <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm space-y-6">
        <div>
            <h1 class="text-lg font-bold text-neutral-900 dark:text-white">Gelen Veli Bildirimleri</h1>
            <p class="text-xs text-neutral-500 mt-1">Yönetim tarafından doğrudan sizin için gönderilen kişiselleştirilmiş bildirimler.</p>
        </div>

        <div class="space-y-4">
            @forelse($notifications as $notif)
                <div class="p-4 bg-neutral-50 rounded-xl border border-neutral-100 space-y-1">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-bold text-neutral-800">{{ $notif->title }}</h3>
                        <span class="text-[10px] text-neutral-400 font-mono">{{ $notif->created_at->format('d.m.Y H:i') }}</span>
                    </div>
                    <p class="text-[11px] text-neutral-600 leading-relaxed">{{ $notif->content }}</p>
                </div>
            @empty
                <div class="text-center text-xs text-neutral-400 py-6">Kayıtlı bildirim bulunmamaktadır.</div>
            @endforelse
        </div>
    </div>
@endsection
