@extends('layouts.admin')
@section('title', 'Bildirim Merkezi')
@section('content')
<x-admin.crud.index-layout title="Bildirim Merkezi" description="Tüm iletişim kanallarının merkezi görünümü.">
<div class="grid grid-cols-1 md:grid-cols-3 gap-6"><div class="bg-white p-6 rounded-2xl shadow-premium-sm"><p class="text-xs text-neutral-500">Toplam bildirim</p><p class="text-3xl font-bold">{{ $summary['total_notifications'] }}</p></div><div class="bg-white p-6 rounded-2xl shadow-premium-sm"><p class="text-xs text-neutral-500">Okunma oranı</p><p class="text-3xl font-bold text-green-600">%{{ $summary['read_rate'] }}</p></div><div class="bg-white p-6 rounded-2xl shadow-premium-sm"><p class="text-xs text-neutral-500">Teslimat oranı</p><p class="text-3xl font-bold text-primary">%{{ $summary['delivery_rate'] }}</p></div></div>
<div class="mt-6 bg-white p-6 rounded-2xl shadow-premium-sm"><h3 class="font-bold mb-3">Kanal dağılımı</h3><div class="flex gap-3 flex-wrap">@forelse($summary['channel_distribution'] as $channel => $total)<span class="px-3 py-2 rounded-lg bg-neutral-100 text-sm">{{ $channel }}: {{ $total }}</span>@empty<p class="text-sm text-neutral-500">Henüz veri yok.</p>@endforelse</div></div>
</x-admin.crud.index-layout>
@endsection
