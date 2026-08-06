@extends('layouts.admin')
@section('title', 'Tedarikçiler')
@section('content')
    <div class="space-y-6">
        
        <!-- Header -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm flex justify-between items-center">
            <div>
                <h1 class="text-lg font-bold text-slate-900 dark:text-white">Tedarikçi Kayıtları</h1>
                <p class="text-xs text-slate-500 mt-1">Dershane alımları için tedarikçi firmaları, iletişim bilgilerini ve vergi numaralarını yönetin.</p>
            </div>
            
            <x-admin.button type="button" onclick="toggleModal('supplier-modal')" variant="primary" size="sm">
                Yeni Tedarikçi Ekle
            </x-admin.button>
        </div>

        <!-- Tedarikçiler Tablosu -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm">
            <x-admin.table.layout>
                <x-slot name="head">
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">Tedarikçi Adı</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">Telefon / E-Posta</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">Adres</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">Vergi Numarası</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">Sipariş Sayısı</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">İşlem</th>
                </x-slot>
                <x-slot name="body">
                    @forelse($suppliers as $sup)
                        <tr>
                            <td class="px-4 py-3 text-xs font-bold text-slate-900 dark:text-white">{{ $sup->name }}</td>
                            <td class="px-4 py-3 text-xs">
                                <div>{{ $sup->phone ?? '-' }}</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">{{ $sup->email ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 text-xs max-w-xs truncate">{{ $sup->address ?? '-' }}</td>
                            <td class="px-4 py-3 text-xs font-mono">{{ $sup->tax_number ?? '-' }}</td>
                            <td class="px-4 py-3 text-xs font-bold font-mono text-center">{{ $sup->purchase_orders_count }} Sipariş</td>
                            <td class="px-4 py-3 text-xs space-x-2">
                                <button onclick="editSupplier({{ json_encode($sup) }})" class="text-teal-600 hover:underline font-bold">Düzenle</button>
                                <form method="POST" action="{{ route('admin.suppliers.destroy', $sup->id) }}" class="inline-block" onsubmit="return confirm('Tedarikçiyi silmek istediğinize emin misiniz?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline font-bold">Sil</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-xs text-slate-400">Kayıtlı tedarikçi bulunmamaktadır.</td>
                        </tr>
                    @endforelse
                </x-slot>
            </x-admin.table.layout>
        </div>

        <!-- Tedarikçi Ekleme Modal -->
        <div id="supplier-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 p-6 max-w-md w-full shadow-md space-y-4">
                <div class="flex justify-between items-center">
                    <h3 id="modal-title" class="text-sm font-bold text-slate-900 dark:text-white">Yeni Tedarikçi Ekle</h3>
                    <button onclick="toggleModal('supplier-modal')" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>
                
                <form id="supplier-form" method="POST" action="{{ route('admin.suppliers.store') }}" class="space-y-3 text-xs">
                    @csrf
                    <input type="hidden" id="form-method" name="_method" value="POST">
                    
                    <div class="space-y-1">
                        <label class="font-bold text-slate-600 dark:text-slate-400">Firma Adı</label>
                        <input type="text" name="name" id="sup-name" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-slate-600 dark:text-slate-400">Telefon</label>
                        <input type="text" name="phone" id="sup-phone" class="w-full p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-slate-600 dark:text-slate-400">E-Posta</label>
                        <input type="email" name="email" id="sup-email" class="w-full p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-slate-600 dark:text-slate-400">Vergi Numarası / Daire</label>
                        <input type="text" name="tax_number" id="sup-tax_number" class="w-full p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-slate-600 dark:text-slate-400">Adres</label>
                        <textarea name="address" id="sup-address" rows="2" class="w-full p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <x-admin.button type="button" onclick="toggleModal('supplier-modal')" variant="secondary">Vazgeç</x-admin.button>
                        <x-admin.button type="submit" variant="primary">Kaydet</x-admin.button>
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

        function editSupplier(sup) {
            document.getElementById('modal-title').innerText = 'Tedarikçi Düzenle';
            document.getElementById('supplier-form').action = `/admin/suppliers/${sup.id}`;
            document.getElementById('form-method').value = 'PUT';

            document.getElementById('sup-name').value = sup.name;
            document.getElementById('sup-phone').value = sup.phone || '';
            document.getElementById('sup-email').value = sup.email || '';
            document.getElementById('sup-tax_number').value = sup.tax_number || '';
            document.getElementById('sup-address').value = sup.address || '';

            toggleModal('supplier-modal');
        }
    </script>
@endsection
