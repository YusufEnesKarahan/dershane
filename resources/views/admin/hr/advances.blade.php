@extends('layouts.admin')
@section('title', 'Avans Talepleri')
@section('content')
    <div class="space-y-6">
        
        <!-- Header -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm flex justify-between items-center">
            <div>
                <h1 class="text-lg font-bold text-neutral-900 dark:text-white">Personel Avans Talepleri</h1>
                <p class="text-xs text-neutral-500 mt-1">Personellerin maaş öncesi avans taleplerini, onay süreçlerini ve geçmiş avans ödemelerini yönetin.</p>
            </div>
            
            <button onclick="toggleModal('advance-modal')" class="px-4 py-2 bg-violet-600 hover:bg-violet-700 text-xs font-bold text-white rounded-xl transition shadow-lg shadow-violet-950">
                Avans Talebi Oluştur
            </button>
        </div>

        <!-- Avanslar Tablosu -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <x-admin.table.layout>
                <x-slot name="head">
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Talep Tarihi</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Personel</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Gerekçe / Neden</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Tutar</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Durum</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">İşlem</th>
                </x-slot>
                <x-slot name="body">
                    @forelse($advances as $adv)
                        <tr>
                            <td class="px-4 py-3 text-xs font-bold font-mono text-neutral-900 dark:text-white">{{ $adv->created_at->toDateString() }}</td>
                            <td class="px-4 py-3 text-xs">
                                <span class="font-bold text-neutral-800 dark:text-neutral-200">{{ $adv->employee->first_name }} {{ $adv->employee->last_name }}</span>
                                <div class="text-[10px] text-neutral-400 mt-0.5">{{ $adv->employee->department->name ?? 'Yok' }}</div>
                            </td>
                            <td class="px-4 py-3 text-xs">{{ $adv->reason }}</td>
                            <td class="px-4 py-3 text-xs font-bold font-mono text-neutral-900 dark:text-white">₺{{ number_format($adv->amount, 2) }}</td>
                            <td class="px-4 py-3 text-xs">
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold {{ $adv->status === 'Approved' ? 'bg-green-100 text-green-700' : ($adv->status === 'Rejected' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                                    {{ $adv->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs space-x-2">
                                @if($adv->status === 'Pending')
                                    <form method="POST" action="{{ route('admin.advances.approve', $adv->id) }}" class="inline-block">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:underline font-bold">Onayla</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.advances.reject', $adv->id) }}" class="inline-block">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:underline font-bold">Reddet</button>
                                    </form>
                                @else
                                    <span class="text-neutral-400 font-bold">Tamamlandı</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-xs text-neutral-400">Avans talebi bulunmamaktadır.</td>
                        </tr>
                    @endforelse
                </x-slot>
            </x-admin.table.layout>
        </div>

        <!-- Avans Ekleme Modal -->
        <div id="advance-modal" class="fixed inset-0 z-50 hidden bg-neutral-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 p-6 max-w-md w-full shadow-premium space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Yeni Avans Talebi Oluştur</h3>
                    <button onclick="toggleModal('advance-modal')" class="text-neutral-400 hover:text-neutral-600">&times;</button>
                </div>
                
                <form method="POST" action="{{ route('admin.advances.store') }}" class="space-y-3 text-xs">
                    @csrf
                    
                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Personel</label>
                        <select name="employee_id" required class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Avans Tutarı</label>
                        <input type="number" name="amount" required step="0.01" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Gerekçe / Talep Nedeni</label>
                        <textarea name="reason" rows="2" placeholder="Sağlık harcaması, Acil nakit ihtiyacı vb." class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" onclick="toggleModal('advance-modal')" class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 font-bold rounded-xl transition">Vazgeç</button>
                        <button type="submit" class="px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white font-bold rounded-xl transition">Gönder</button>
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
