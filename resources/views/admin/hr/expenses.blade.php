@extends('layouts.admin')
@section('title', 'Masraf Kayıtları')
@section('content')
    <div class="space-y-6">
        
        <!-- Header -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm flex justify-between items-center">
            <div>
                <h1 class="text-lg font-bold text-neutral-900 dark:text-white">Personel Masraf Kayıtları</h1>
                <p class="text-xs text-neutral-500 mt-1">Personellerin iş süreçlerinde yaptığı harcamaları, fiş/makbuz yüklemelerini ve masraf onaylarını yönetin.</p>
            </div>
            
            <button onclick="toggleModal('expense-modal')" class="px-4 py-2 bg-violet-600 hover:bg-violet-700 text-xs font-bold text-white rounded-xl transition shadow-lg shadow-violet-950">
                Masraf Beyanı Ekle
            </button>
        </div>

        <!-- Masraflar Tablosu -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <x-admin.table.layout>
                <x-slot name="head">
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Açıklama / Başlık</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Personel</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Kategori</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Tutar</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Durum</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">İşlem</th>
                </x-slot>
                <x-slot name="body">
                    @forelse($expenses as $exp)
                        <tr>
                            <td class="px-4 py-3 text-xs font-bold text-neutral-900 dark:text-white">
                                {{ $exp->title }}
                                @if($exp->receipt)
                                    <div class="text-[10px] font-normal text-neutral-400 mt-0.5 font-mono">Belge: {{ $exp->receipt }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs">
                                <span class="font-bold text-neutral-800 dark:text-neutral-200">{{ $exp->employee->first_name }} {{ $exp->employee->last_name }}</span>
                                <div class="text-[10px] text-neutral-400 mt-0.5">{{ $exp->employee->department->name ?? 'Yok' }}</div>
                            </td>
                            <td class="px-4 py-3 text-xs">{{ $exp->category }}</td>
                            <td class="px-4 py-3 text-xs font-bold font-mono text-neutral-900 dark:text-white">₺{{ number_format($exp->amount, 2) }}</td>
                            <td class="px-4 py-3 text-xs">
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold {{ $exp->status === 'Approved' ? 'bg-green-100 text-green-700' : ($exp->status === 'Rejected' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                                    {{ $exp->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs space-x-2">
                                @if($exp->status === 'Pending')
                                    <form method="POST" action="{{ route('admin.expenses.approve', $exp->id) }}" class="inline-block">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:underline font-bold">Onayla</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.expenses.reject', $exp->id) }}" class="inline-block">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:underline font-bold">Reddet</button>
                                    </form>
                                @else
                                    <span class="text-neutral-400">İşlem Tamamlandı</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-xs text-neutral-400">Masraf beyanı bulunmamaktadır.</td>
                        </tr>
                    @endforelse
                </x-slot>
            </x-admin.table.layout>
        </div>

        <!-- Masraf Ekleme Modal -->
        <div id="expense-modal" class="fixed inset-0 z-50 hidden bg-neutral-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 p-6 max-w-md w-full shadow-premium space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Yeni Masraf Beyanı Ekle</h3>
                    <button onclick="toggleModal('expense-modal')" class="text-neutral-400 hover:text-neutral-600">&times;</button>
                </div>
                
                <form method="POST" action="{{ route('admin.expenses.store') }}" class="space-y-3 text-xs">
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
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Masraf Açıklaması / Başlık</label>
                        <input type="text" name="title" required placeholder="Şehir dışı ulaşım, Kırtasiye alımı vb." class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Tutar</label>
                        <input type="number" name="amount" required step="0.01" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Kategori</label>
                        <select name="category" required class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                            <option value="Ulaşım / Yol">Ulaşım / Yol</option>
                            <option value="Yemek / Temsil">Yemek / Temsil</option>
                            <option value="Ofis Malzemesi">Ofis Malzemesi</option>
                            <option value="Eğitim Materyali">Eğitim Materyali</option>
                            <option value="Diğer">Diğer</option>
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Fiş / Makbuz No</label>
                        <input type="text" name="receipt" placeholder="F-1298379" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" onclick="toggleModal('expense-modal')" class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 font-bold rounded-xl transition">Vazgeç</button>
                        <button type="submit" class="px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white font-bold rounded-xl transition">Beyan Et</button>
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
