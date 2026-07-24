@extends('layouts.admin')
@section('title', 'Cihaz Bakım Kayıtları')
@section('content')
    <div class="space-y-6">
        
        <!-- Header -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm flex justify-between items-center">
            <div>
                <h1 class="text-lg font-bold text-neutral-900 dark:text-white">Demirbaş Bakım & Onarım Raporları</h1>
                <p class="text-xs text-neutral-500 mt-1">Arızalanan veya periyodik bakıma giden cihazların maliyetlerini ve durumlarını yönetin.</p>
            </div>
            
            <button onclick="toggleModal('maintenance-modal')" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-xs font-bold text-white rounded-xl transition shadow-lg shadow-teal-950">
                Yeni Bakım Raporu Ekle
            </button>
        </div>

        <!-- Bakım Kayıtları Tablosu -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <x-admin.table.layout>
                <x-slot name="head">
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Bakım Tarihi</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Demirbaş</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Sorumlu Personel</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Maliyet</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Durum</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">İşlem</th>
                </x-slot>
                <x-slot name="body">
                    @forelse($records as $rec)
                        <tr>
                            <td class="px-4 py-3 text-xs font-bold font-mono text-neutral-900 dark:text-white">{{ $rec->maintenance_date }}</td>
                            <td class="px-4 py-3 text-xs font-bold">
                                {{ $rec->asset->name ?? 'Silinmiş' }}
                                <div class="text-[10px] font-mono font-normal text-neutral-400 mt-0.5">{{ $rec->asset->asset_code ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 text-xs">{{ $rec->employee->first_name ?? '-' }} {{ $rec->employee->last_name ?? '-' }}</td>
                            <td class="px-4 py-3 text-xs font-bold font-mono text-neutral-900 dark:text-white">₺{{ number_format($rec->cost, 2) }}</td>
                            <td class="px-4 py-3 text-xs">
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold {{ $rec->status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $rec->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs">
                                @if($rec->status === 'pending')
                                    <form method="POST" action="{{ route('admin.maintenance.complete', $rec->id) }}" class="inline-block">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:underline font-bold">Tamamlandı Olarak İşaretle</button>
                                    </form>
                                @else
                                    <span class="text-neutral-400">Tamamlandı</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-xs text-neutral-400">Bakım kaydı bulunmamaktadır.</td>
                        </tr>
                    @endforelse
                </x-slot>
            </x-admin.table.layout>
        </div>

        <!-- Bakım Ekleme Modal -->
        <div id="maintenance-modal" class="fixed inset-0 z-50 hidden bg-neutral-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 p-6 max-w-md w-full shadow-premium space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Yeni Bakım Kaydı Ekle</h3>
                    <button onclick="toggleModal('maintenance-modal')" class="text-neutral-400 hover:text-neutral-600">&times;</button>
                </div>
                
                <form method="POST" action="{{ route('admin.maintenance.store') }}" class="space-y-3 text-xs">
                    @csrf
                    
                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Bakıma Alınacak Demirbaş</label>
                        <select name="asset_id" required class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                            @foreach($assets as $ast)
                                <option value="{{ $ast->id }}">{{ $ast->name }} ({{ $ast->asset_code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Sorumlu Personel</label>
                        <select name="employee_id" required class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Bakım Tarihi</label>
                        <input type="date" name="maintenance_date" required value="{{ date('Y-m-d') }}" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Bakım Maliyeti</label>
                        <input type="number" name="cost" required step="0.01" value="0.00" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Yapılan İşlem / Açıklama</label>
                        <textarea name="description" rows="2" placeholder="Örn: RAM yükseltmesi ve genel temizlik yapıldı." class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" onclick="toggleModal('maintenance-modal')" class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 font-bold rounded-xl transition">Vazgeç</button>
                        <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl transition">Bakıma Al</button>
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
