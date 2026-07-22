@extends('layouts.admin')
@section('title', 'İletişim Analitiği')
@section('content')
    <x-admin.crud.index-layout title="İletişim & Bildirim Analitiği" description="SMS ve E-Posta gönderim başarı oranlarını, toplam bildirim ve duyuru loglarını inceleyin.">
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Toplam Bildirim</h4>
                <div class="text-2xl font-bold">{{ $summary['total_notifications'] }} Mesaj</div>
            </div>
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Yayınlanan Duyuru</h4>
                <div class="text-2xl font-bold text-primary">{{ $summary['total_announcements'] }} Kayıt</div>
            </div>
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Duyuru Okunma Sayısı</h4>
                <div class="text-2xl font-bold text-green-600">{{ $summary['total_reads'] }} Kişi</div>
            </div>
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">İletim Başarı Oranı</h4>
                <div class="text-2xl font-bold text-amber-600">%{{ $summary['delivery_rate'] }}</div>
            </div>
        </div>

        <!-- İletim Logları Tablosu -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm">
            <h3 class="text-sm font-bold text-neutral-900 dark:text-white mb-4">Bildirim İletim Logları</h3>
            <x-admin.table.layout>
                <x-slot name="head">
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Alıcı</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Kanal</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Sağlayıcı</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Gönderim Zamanı</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Durum</th>
                </x-slot>
                <x-slot name="body">
                    @forelse($recentLogs as $log)
                        <tr>
                            <td class="px-4 py-3 text-xs font-bold text-neutral-900">{{ $log->recipient }}</td>
                            <td class="px-4 py-3 text-xs font-semibold text-neutral-700">{{ $log->channel }}</td>
                            <td class="px-4 py-3 text-xs font-mono text-neutral-500">{{ $log->provider }}</td>
                            <td class="px-4 py-3 text-xs text-neutral-500 font-mono">{{ $log->created_at->format('d.m.Y H:i') }}</td>
                            <td class="px-4 py-3 text-xs">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full {{ $log->status === 'Sent' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $log->status === 'Sent' ? 'Gönderildi' : 'Başarısız' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-xs text-neutral-400">Henüz iletim kaydı bulunmamaktadır.</td>
                        </tr>
                    @endforelse
                </x-slot>
            </x-admin.table.layout>
        </div>

    </x-admin.crud.index-layout>
@endsection
