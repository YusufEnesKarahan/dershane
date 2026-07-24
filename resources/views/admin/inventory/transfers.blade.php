@extends('layouts.admin')
@section('title', 'Lokasyon Transferleri')
@section('content')
    <div class="space-y-6">
        
        <!-- Header -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm flex justify-between items-center">
            <div>
                <h1 class="text-lg font-bold text-neutral-900 dark:text-white">Şube & Depo Transferleri</h1>
                <p class="text-xs text-neutral-500 mt-1">Demirbaşların fiziksel olarak şubeler veya depolar arasında yer değiştirmesini takip edin.</p>
            </div>
            
            <button onclick="toggleModal('transfer-modal')" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-xs font-bold text-white rounded-xl transition shadow-lg shadow-teal-950">
                Yeni Transfer Gerçekleştir
            </button>
        </div>

        <!-- Transferler Tablosu -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <x-admin.table.layout>
                <x-slot name="head">
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Transfer Tarihi</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Demirbaş</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Çıkış Lokasyonu</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Varış Lokasyonu</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Açıklama / Not</th>
                </x-slot>
                <x-slot name="body">
                    @forelse($transfers as $tr)
                        <tr>
                            <td class="px-4 py-3 text-xs font-bold font-mono text-neutral-900 dark:text-white">{{ $tr->transfer_date }}</td>
                            <td class="px-4 py-3 text-xs font-bold">
                                {{ $tr->asset->name ?? 'Silinmiş' }}
                                <div class="text-[10px] font-mono font-normal text-neutral-400 mt-0.5">{{ $tr->asset->asset_code ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 text-xs font-bold text-neutral-600 dark:text-neutral-400">{{ $tr->fromLocation->name ?? 'Merkez Depo' }}</td>
                            <td class="px-4 py-3 text-xs font-bold text-teal-600 dark:text-teal-400">{{ $tr->toLocation->name ?? 'Merkez Depo' }}</td>
                            <td class="px-4 py-3 text-xs text-neutral-500 italic max-w-xs">{{ $tr->notes }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-xs text-neutral-400">Lokasyon transfer kaydı bulunmamaktadır.</td>
                        </tr>
                    @endforelse
                </x-slot>
            </x-admin.table.layout>
        </div>

        <!-- Transfer Ekleme Modal -->
        <div id="transfer-modal" class="fixed inset-0 z-50 hidden bg-neutral-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 p-6 max-w-md w-full shadow-premium space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Yeni Transfer Gerçekleştir</h3>
                    <button onclick="toggleModal('transfer-modal')" class="text-neutral-400 hover:text-neutral-600">&times;</button>
                </div>
                
                <form method="POST" action="{{ route('admin.transfers.store') }}" class="space-y-3 text-xs">
                    @csrf
                    
                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Transfer Edilecek Demirbaş</label>
                        <select name="asset_id" required class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                            @foreach($assets as $ast)
                                @if($ast->status === 'active')
                                    <option value="{{ $ast->id }}">{{ $ast->name }} ({{ $ast->asset_code }} - Şu an: {{ $ast->location->name ?? 'Merkez Depo' }})</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Mevcut Lokasyon (Opsiyonel)</label>
                        <select name="from_location_id" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                            <option value="">Merkez Depo / Seçilmedi</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Hedef Lokasyon / Depo</label>
                        <select name="to_location_id" required class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Transfer Tarihi</label>
                        <input type="date" name="transfer_date" required value="{{ date('Y-m-d') }}" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Not / Gerekçe</label>
                        <textarea name="notes" rows="2" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" onclick="toggleModal('transfer-modal')" class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 font-bold rounded-xl transition">Vazgeç</button>
                        <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl transition">Transfer Et</button>
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
