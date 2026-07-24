@extends('layouts.admin')
@section('title', 'İstatistikler')
@section('content')
    <x-admin.crud.index-layout title="İçerik İstatistik Raporları" description="Yazılarınızın okunma sayılarını, tıklama ve yorum etkileşimlerini analiz edin.">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Toplam Okunma</h4>
                <div class="text-2xl font-bold">14,250</div>
                <div class="text-[10px] text-green-500 mt-1">▲ %12.4 (Geçen haftaya göre)</div>
            </div>
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Tekil Ziyaretçiler</h4>
                <div class="text-2xl font-bold">8,920</div>
                <div class="text-[10px] text-green-500 mt-1">▲ %8.2 (Geçen haftaya göre)</div>
            </div>
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Ortalama Hemen Çıkma (Bounce)</h4>
                <div class="text-2xl font-bold">%34.5</div>
                <div class="text-[10px] text-red-500 mt-1">▼ -%2.1 (Düşüş iyiye işarettir)</div>
            </div>
        </div>

        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <h3 class="text-sm font-bold text-neutral-900 dark:text-white mb-4">En Popüler Makaleler</h3>
            <x-admin.table.layout>
                <x-slot name="head">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">Makale</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">Okunma Sayısı</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">Yorumlar</th>
                </x-slot>
                <x-slot name="body">
                    @forelse($popular as $blog)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40">
                            <td class="px-6 py-4 text-sm font-semibold text-neutral-900 dark:text-white">{{ $blog->title }}</td>
                            <td class="px-6 py-4 text-sm text-neutral-500">{{ $blog->views_count }}</td>
                            <td class="px-6 py-4 text-sm text-neutral-500">{{ $blog->comments_count }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-sm text-neutral-400">Yeterli etkileşim verisi henüz birikmedi.</td>
                        </tr>
                    @endforelse
                </x-slot>
            </x-admin.table.layout>
        </div>
    </x-admin.crud.index-layout>
@endsection
