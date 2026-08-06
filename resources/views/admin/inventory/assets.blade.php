@extends('layouts.admin')
@section('title', 'Demirbaş Envanteri')
@section('content')
    <div class="space-y-6">
        
        <!-- Header -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm flex justify-between items-center">
            <div>
                <h1 class="text-lg font-bold text-slate-900 dark:text-white">Demirbaş Varlıkları</h1>
                <p class="text-xs text-slate-500 mt-1">Dershane bünyesindeki tüm elektronik, mobilya ve diğer demirbaş cihazların listesini yönetin.</p>
            </div>
            
            <div class="flex gap-2">
                <x-admin.button type="button" onclick="toggleModal('assign-modal')" variant="secondary" size="sm">Zimmet Ata</x-admin.button>
                <x-admin.button type="button" onclick="toggleModal('asset-modal')" variant="primary" size="sm">Yeni Demirbaş Ekle</x-admin.button>
            </div>
        </div>

        <!-- Demirbaşlar Tablosu -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm">
            <x-admin.table.layout>
                <x-slot name="head">
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">Kod / Demirbaş</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">Kategori</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">Marka / Model</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">Lokasyon</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">Durum</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">İşlem</th>
                </x-slot>
                <x-slot name="body">
                    @forelse($assets as $ast)
                        <tr>
                            <td class="px-4 py-3 text-xs font-bold font-mono text-slate-900 dark:text-white">
                                {{ $ast->asset_code }}
                                <div class="text-[10px] font-sans font-normal text-slate-400 mt-0.5">{{ $ast->name }}</div>
                            </td>
                            <td class="px-4 py-3 text-xs">{{ $ast->category->name ?? 'Yok' }}</td>
                            <td class="px-4 py-3 text-xs">
                                {{ $ast->brand }} {{ $ast->model }}
                                <div class="text-[10px] text-slate-400 mt-0.5">S/N: {{ $ast->serial_number ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 text-xs font-bold">{{ $ast->location->name ?? 'Merkez Depo' }}</td>
                            <td class="px-4 py-3 text-xs">
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold {{ $ast->status === 'active' ? 'bg-green-100 text-green-700' : ($ast->status === 'maintenance' ? 'bg-amber-100 text-amber-700' : ($ast->status === 'broken' ? 'bg-red-100 text-red-700' : 'bg-slate-200 text-slate-700')) }}">
                                    {{ $ast->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs space-x-2">
                                <button onclick="editAsset({{ json_encode($ast) }})" class="text-teal-600 hover:underline font-bold">Düzenle</button>
                                @if($ast->status !== 'retired')
                                    <form method="POST" action="{{ route('admin.assets.retire', $ast->id) }}" class="inline-block" onsubmit="return confirm('Emekliye ayırmak istediğinize emin misiniz?')">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:underline font-bold">Emekli Et</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-xs text-slate-400">Kayıtlı demirbaş bulunmamaktadır.</td>
                        </tr>
                    @endforelse
                </x-slot>
            </x-admin.table.layout>
        </div>

        <!-- Yeni Demirbaş Modal -->
        <div id="asset-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 p-6 max-w-lg w-full shadow-md space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center">
                    <h3 id="modal-title" class="text-sm font-bold text-slate-900 dark:text-white">Yeni Demirbaş Kaydı</h3>
                    <button onclick="toggleModal('asset-modal')" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>
                
                <form id="asset-form" method="POST" action="{{ route('admin.assets.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    @csrf
                    <input type="hidden" id="form-method" name="_method" value="POST">

                    <div class="space-y-1">
                        <label class="font-bold text-slate-600 dark:text-slate-400">Demirbaş Adı</label>
                        <input type="text" name="name" id="ast-name" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-slate-600 dark:text-slate-400">Demirbaş Kodu</label>
                        <input type="text" name="asset_code" id="ast-code" placeholder="AST-1001" class="w-full p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-slate-600 dark:text-slate-400">Kategori</label>
                        <select name="category_id" id="ast-category_id" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-slate-600 dark:text-slate-400">Marka</label>
                        <input type="text" name="brand" id="ast-brand" class="w-full p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-slate-600 dark:text-slate-400">Model</label>
                        <input type="text" name="model" id="ast-model" class="w-full p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-slate-600 dark:text-slate-400">Seri Numarası</label>
                        <input type="text" name="serial_number" id="ast-serial_number" class="w-full p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-slate-600 dark:text-slate-400">Alım Fiyatı</label>
                        <input type="number" name="purchase_price" id="ast-purchase_price" required step="0.01" class="w-full p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-slate-600 dark:text-slate-400">Alım Tarihi</label>
                        <input type="date" name="purchase_date" id="ast-purchase_date" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-slate-600 dark:text-slate-400">Garanti Bitiş Tarihi</label>
                        <input type="date" name="warranty_end_date" id="ast-warranty_end_date" class="w-full p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-slate-600 dark:text-slate-400">Lokasyon</label>
                        <select name="location_id" id="ast-location_id" class="w-full p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                            <option value="">Seçilmedi (Merkez Depo)</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-slate-600 dark:text-slate-400">Durum</label>
                        <select name="status" id="ast-status" class="w-full p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                            <option value="active">Aktif (Kullanımda)</option>
                            <option value="maintenance">Bakımda</option>
                            <option value="broken">Arızalı</option>
                            <option value="retired">Emekli / Atıl</option>
                        </select>
                    </div>

                    <div class="md:col-span-2 space-y-1">
                        <label class="font-bold text-slate-600 dark:text-slate-400">Açıklama</label>
                        <textarea name="description" id="ast-description" rows="2" class="w-full p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl"></textarea>
                    </div>

                    <div class="md:col-span-2 flex justify-end gap-2 pt-4">
                        <x-admin.button type="button" onclick="toggleModal('asset-modal')" variant="secondary">Vazgeç</x-admin.button>
                        <x-admin.button type="submit" variant="primary">Kaydet</x-admin.button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Zimmet Ata Modal -->
        <div id="assign-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 p-6 max-w-md w-full shadow-md space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Demirbaş Zimmet Ata</h3>
                    <button onclick="toggleModal('assign-modal')" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>
                
                <form method="POST" action="{{ route('admin.assets.assign') }}" class="space-y-3 text-xs">
                    @csrf
                    
                    <div class="space-y-1">
                        <label class="font-bold text-slate-600 dark:text-slate-400">Demirbaş Varlık</label>
                        <select name="asset_id" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                            @foreach($assets as $ast)
                                @if($ast->status === 'active')
                                    <option value="{{ $ast->id }}">{{ $ast->name }} ({{ $ast->asset_code }})</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-slate-600 dark:text-slate-400">Zimmetlenecek Personel</label>
                        <select name="employee_id" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-slate-600 dark:text-slate-400">Zimmet Tarihi</label>
                        <input type="date" name="assigned_date" required value="{{ date('Y-m-d') }}" class="w-full p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-slate-600 dark:text-slate-400">Cihaz Durumu</label>
                        <input type="text" name="condition" placeholder="Sıfır, Temiz, Kutulu" class="w-full p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-slate-600 dark:text-slate-400">Zimmet Notu</label>
                        <textarea name="notes" rows="2" class="w-full p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <x-admin.button type="button" onclick="toggleModal('assign-modal')" variant="secondary">Vazgeç</x-admin.button>
                        <x-admin.button type="submit" variant="primary">Zimmetle</x-admin.button>
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

        function editAsset(ast) {
            document.getElementById('modal-title').innerText = 'Demirbaş Bilgilerini Düzenle';
            document.getElementById('asset-form').action = `/admin/assets/${ast.id}`;
            document.getElementById('form-method').value = 'PUT';

            document.getElementById('ast-name').value = ast.name;
            document.getElementById('ast-code').value = ast.asset_code;
            document.getElementById('ast-category_id').value = ast.category_id;
            document.getElementById('ast-brand').value = ast.brand || '';
            document.getElementById('ast-model').value = ast.model || '';
            document.getElementById('ast-serial_number').value = ast.serial_number || '';
            document.getElementById('ast-purchase_price').value = ast.purchase_price;
            document.getElementById('ast-purchase_date').value = ast.purchase_date || '';
            document.getElementById('ast-warranty_end_date').value = ast.warranty_end_date || '';
            document.getElementById('ast-location_id').value = ast.location_id || '';
            document.getElementById('ast-status').value = ast.status;
            document.getElementById('ast-description').value = ast.description || '';

            toggleModal('asset-modal');
        }
    </script>
@endsection
