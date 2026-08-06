@extends('layouts.admin')
@section('title', 'Satın Alma Siparişleri')
@section('content')
    <div class="space-y-6">
        
        <!-- Header -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm flex justify-between items-center">
            <div>
                <h1 class="text-lg font-bold text-slate-900 dark:text-white">Satın Alma Siparişleri</h1>
                <p class="text-xs text-slate-500 mt-1">Malzeme ve demirbaş alımları için oluşturulan talepleri, onayları ve fatura tutarlarını takip edin.</p>
            </div>
            
            <x-admin.button type="button" onclick="toggleModal('order-modal')" variant="primary" size="sm">
                Yeni Sipariş Oluştur
            </x-admin.button>
        </div>

        <!-- Siparişler Tablosu -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm">
            <x-admin.table.layout>
                <x-slot name="head">
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">Sipariş No</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">Tedarikçi</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">Sipariş Tarihi</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">Tutar</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">Durum</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">İşlem</th>
                </x-slot>
                <x-slot name="body">
                    @forelse($orders as $ord)
                        <tr>
                            <td class="px-4 py-3 text-xs font-bold font-mono text-slate-900 dark:text-white">{{ $ord->order_number }}</td>
                            <td class="px-4 py-3 text-xs font-bold">{{ $ord->supplier->name ?? 'Yok' }}</td>
                            <td class="px-4 py-3 text-xs font-mono">{{ $ord->order_date }}</td>
                            <td class="px-4 py-3 text-xs font-bold font-mono text-slate-900 dark:text-white">₺{{ number_format($ord->total_amount, 2) }}</td>
                            <td class="px-4 py-3 text-xs">
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold {{ $ord->status === 'completed' ? 'bg-green-100 text-green-700' : ($ord->status === 'approved' ? 'bg-blue-100 text-blue-700' : ($ord->status === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700')) }}">
                                    {{ $ord->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs space-x-2">
                                @if($ord->status === 'pending')
                                    <form method="POST" action="{{ route('admin.purchase.approve', $ord->id) }}" class="inline-block">
                                        @csrf
                                        <button type="submit" class="text-blue-600 hover:underline font-bold">Onayla</button>
                                    </form>
                                @elseif($ord->status === 'approved')
                                    <form method="POST" action="{{ route('admin.purchase.complete', $ord->id) }}" class="inline-block">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:underline font-bold">Tamamlandı İşaretle</button>
                                    </form>
                                @else
                                    <span class="text-slate-400">İşlem Kilitli</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-xs text-slate-400">Satın alma kaydı bulunmamaktadır.</td>
                        </tr>
                    @endforelse
                </x-slot>
            </x-admin.table.layout>
        </div>

        <!-- Sipariş Ekleme Modal -->
        <div id="order-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 p-6 max-w-md w-full shadow-md space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Yeni Satın Alma Siparişi Oluştur</h3>
                    <button onclick="toggleModal('order-modal')" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>
                
                <form method="POST" action="{{ route('admin.purchase.store') }}" class="space-y-3 text-xs">
                    @csrf
                    
                    <div class="space-y-1">
                        <label class="font-bold text-slate-600 dark:text-slate-400">Tedarikçi Firma</label>
                        <select name="supplier_id" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                            @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-slate-600 dark:text-slate-400">Sipariş / Fatura Numarası</label>
                        <input type="text" name="order_number" placeholder="ORD-2026-0001" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-slate-600 dark:text-slate-400">Toplam Sipariş Tutarı</label>
                        <input type="number" name="total_amount" required step="0.01" class="w-full p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-slate-600 dark:text-slate-400">Sipariş Tarihi</label>
                        <input type="date" name="order_date" required value="{{ date('Y-m-d') }}" class="w-full p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-slate-600 dark:text-slate-400">Notlar / Satın Alınan Kalemler</label>
                        <textarea name="notes" rows="2" placeholder="Örn: 20 Kutu A4 kağıt, 5 adet ofis sandalyesi." class="w-full p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <x-admin.button type="button" onclick="toggleModal('order-modal')" variant="secondary">Vazgeç</x-admin.button>
                        <x-admin.button type="submit" variant="primary">Sipariş Gir</x-admin.button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        function toggleModal(id) {
            const el = document.getElementById(id);
            el.classList.toggle('hidden');
        }
    </script>
@endsection
