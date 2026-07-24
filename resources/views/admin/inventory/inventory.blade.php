@extends('layouts.admin')
@section('title', 'Stok & Malzeme Yönetimi')
@section('content')
    <div class="space-y-6">
        
        <!-- Header -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm flex justify-between items-center">
            <div>
                <h1 class="text-lg font-bold text-neutral-900 dark:text-white">Stok & Sarf Malzemeleri</h1>
                <p class="text-xs text-neutral-500 mt-1">Dershane içi kırtasiye, temizlik ve eğitim materyalleri stoklarını ve hareketlerini yönetin.</p>
            </div>
            
            <div class="flex gap-2">
                <button onclick="toggleModal('warehouse-modal')" class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-xs font-bold rounded-xl transition">
                    Depo Tanımla
                </button>
                <button onclick="toggleModal('category-modal')" class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-xs font-bold rounded-xl transition">
                    Stok Kategorisi
                </button>
                <button onclick="toggleModal('transaction-modal')" class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-xs font-bold rounded-xl transition">
                    Stok Hareketi Gir
                </button>
                <button onclick="toggleModal('item-modal')" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-xs font-bold text-white rounded-xl transition shadow-lg shadow-teal-950">
                    Yeni Stok Kartı
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Stok Kartları Tablosu -->
            <div class="lg:col-span-2 bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Stok Durum Listesi</h3>
                
                <x-admin.table.layout>
                    <x-slot name="head">
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">SKU / Ürün</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Kategori</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Depo</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Stok Miktarı</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Durum</th>
                    </x-slot>
                    <x-slot name="body">
                        @forelse($items as $item)
                            <tr>
                                <td class="px-4 py-3 text-xs font-bold font-mono text-neutral-900 dark:text-white">
                                    {{ $item->sku }}
                                    <div class="text-[10px] font-sans font-normal text-neutral-400 mt-0.5">{{ $item->name }}</div>
                                </td>
                                <td class="px-4 py-3 text-xs">{{ $item->category->name ?? 'Yok' }}</td>
                                <td class="px-4 py-3 text-xs">{{ $item->warehouse->name ?? 'Merkez Depo' }}</td>
                                <td class="px-4 py-3 text-xs font-bold font-mono">{{ $item->quantity }} {{ $item->unit }}</td>
                                <td class="px-4 py-3 text-xs">
                                    @if($item->quantity <= $item->minimum_quantity)
                                        <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-red-100 text-red-700">Kritik Stok</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-green-100 text-green-700">Yeterli</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-xs text-neutral-400">Kayıtlı ürün bulunmamaktadır.</td>
                            </tr>
                        @endforelse
                    </x-slot>
                </x-admin.table.layout>
            </div>

            <!-- Son Stok Hareketleri -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Son Stok Hareketleri</h3>
                
                <div class="space-y-3 max-h-[500px] overflow-y-auto pr-1">
                    @forelse($transactions as $tx)
                        <div class="p-3 bg-neutral-50 dark:bg-neutral-800/40 border border-neutral-100 dark:border-neutral-800 rounded-xl space-y-1 text-xs">
                            <div class="flex justify-between items-center">
                                <span class="font-bold text-neutral-800 dark:text-neutral-200">{{ $tx->item->name ?? 'Ürün' }}</span>
                                <span class="px-1.5 py-0.5 text-[9px] font-bold rounded uppercase {{ $tx->type === 'purchase' ? 'bg-green-100 text-green-700' : ($tx->type === 'usage' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700') }}">
                                    {{ $tx->type }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center text-[10px] text-neutral-400 font-mono">
                                <span>Miktar: {{ $tx->quantity > 0 ? '+' : '' }}{{ $tx->quantity }}</span>
                                <span>{{ $tx->created_at->toDateString() }}</span>
                            </div>
                            <p class="text-[10px] text-neutral-500 italic">{{ $tx->description }}</p>
                        </div>
                    @empty
                        <div class="text-center text-xs text-neutral-400 py-6">Stok hareketi bulunmamaktadır.</div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Stok Kartı Modal -->
        <div id="item-modal" class="fixed inset-0 z-50 hidden bg-neutral-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 p-6 max-w-md w-full shadow-premium space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Yeni Stok Tanım Kartı</h3>
                    <button onclick="toggleModal('item-modal')" class="text-neutral-400 hover:text-neutral-600">&times;</button>
                </div>
                
                <form method="POST" action="{{ route('admin.inventory.store') }}" class="space-y-3 text-xs">
                    @csrf
                    
                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Ürün Adı</label>
                        <input type="text" name="name" required placeholder="A4 Kağıt, Tahta Kalemi vb." class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">SKU / Barkod Kodu</label>
                        <input type="text" name="sku" required placeholder="SKU-89237" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Kategori</label>
                        <select name="category_id" required class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Depo</label>
                        <select name="warehouse_id" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                            <option value="">Merkez Depo</option>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div class="space-y-1 col-span-2">
                            <label class="font-bold text-neutral-600 dark:text-neutral-400">Başlangıç Stok Miktarı</label>
                            <input type="number" name="quantity" value="0" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-neutral-600 dark:text-neutral-400">Birim</label>
                            <input type="text" name="unit" value="Kutu" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Kritik Stok Sınırı</label>
                        <input type="number" name="minimum_quantity" value="5" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Açıklama</label>
                        <textarea name="description" rows="2" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" onclick="toggleModal('item-modal')" class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 font-bold rounded-xl transition">Vazgeç</button>
                        <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl transition">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Stok Hareketi Modal -->
        <div id="transaction-modal" class="fixed inset-0 z-50 hidden bg-neutral-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 p-6 max-w-md w-full shadow-premium space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Yeni Stok Hareketi Gir</h3>
                    <button onclick="toggleModal('transaction-modal')" class="text-neutral-400 hover:text-neutral-600">&times;</button>
                </div>
                
                <form method="POST" action="{{ route('admin.inventory.transaction') }}" class="space-y-3 text-xs">
                    @csrf
                    
                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Ürün</label>
                        <select name="item_id" required class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                            @foreach($items as $item)
                                <option value="{{ $item->id }}">{{ $item->name }} (SKU: {{ $item->sku }} - Kalan: {{ $item->quantity }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Hareket Tipi</label>
                        <select name="type" required class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                            <option value="purchase">Giriş (Satın Alma vb.)</option>
                            <option value="usage">Çıkış (Kullanım / Sarf vb.)</option>
                            <option value="adjustment">Düzeltme (Stok Güncelleme)</option>
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Miktar</label>
                        <input type="number" name="quantity" required value="1" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Açıklama / Referans</label>
                        <textarea name="description" rows="2" placeholder="Neden veya fatura referansı yazın." class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" onclick="toggleModal('transaction-modal')" class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 font-bold rounded-xl transition">Vazgeç</button>
                        <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl transition">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Stok Kategorisi Modal -->
        <div id="category-modal" class="fixed inset-0 z-50 hidden bg-neutral-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 p-6 max-w-md w-full shadow-premium space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Yeni Stok Kategorisi Tanımla</h3>
                    <button onclick="toggleModal('category-modal')" class="text-neutral-400 hover:text-neutral-600">&times;</button>
                </div>
                
                <form method="POST" action="{{ route('admin.inventory.store-category') }}" class="space-y-3 text-xs">
                    @csrf
                    
                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Kategori Adı</label>
                        <input type="text" name="name" required class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Kategori Kodu</label>
                        <input type="text" name="code" required placeholder="SRF, KRT, EGT vb." class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Açıklama</label>
                        <textarea name="description" rows="2" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" onclick="toggleModal('category-modal')" class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 font-bold rounded-xl transition">Vazgeç</button>
                        <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl transition">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Depo Modal -->
        <div id="warehouse-modal" class="fixed inset-0 z-50 hidden bg-neutral-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 p-6 max-w-md w-full shadow-premium space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Yeni Depo Tanımla</h3>
                    <button onclick="toggleModal('warehouse-modal')" class="text-neutral-400 hover:text-neutral-600">&times;</button>
                </div>
                
                <form method="POST" action="{{ route('admin.inventory.store-warehouse') }}" class="space-y-3 text-xs">
                    @csrf
                    
                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Depo Adı</label>
                        <input type="text" name="name" required class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Açıklama</label>
                        <textarea name="description" rows="2" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" onclick="toggleModal('warehouse-modal')" class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 font-bold rounded-xl transition">Vazgeç</button>
                        <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl transition">Kaydet</button>
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
