@extends('layouts.admin')
@section('title', 'Doküman Kategorileri')
@section('content')
    <div class="space-y-6">
        
        <!-- Header -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm flex justify-between items-center">
            <div>
                <h1 class="text-lg font-bold text-neutral-900 dark:text-white">Doküman Kategorileri</h1>
                <p class="text-xs text-neutral-500 mt-1">Dijital arşivi sınıflandırmak için evrak türleri ve renk tanımlarını yönetin.</p>
            </div>
            
            <button onclick="toggleModal('category-modal')" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-xs font-bold text-white rounded-xl transition shadow-lg shadow-teal-950">
                Yeni Kategori Ekle
            </button>
        </div>

        <!-- Kategori Listesi -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <x-admin.table.layout>
                <x-slot name="head">
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Renk / Kategori Adı</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Slug</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Belge Sayısı</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Durum</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">İşlem</th>
                </x-slot>
                <x-slot name="body">
                    @forelse($categories as $cat)
                        <tr>
                            <td class="px-4 py-3 text-xs font-bold text-neutral-900 dark:text-white flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full" style="background-color: {{ $cat->color }}"></span>
                                {{ $cat->name }}
                            </td>
                            <td class="px-4 py-3 text-xs font-mono text-neutral-400">{{ $cat->slug }}</td>
                            <td class="px-4 py-3 text-xs font-bold font-mono">{{ $cat->documents_count }} Belge</td>
                            <td class="px-4 py-3 text-xs">
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold {{ $cat->is_active ? 'bg-green-100 text-green-700' : 'bg-neutral-200 text-neutral-700' }}">
                                    {{ $cat->is_active ? 'Aktif' : 'Pasif' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs space-x-2">
                                <button onclick="editCategory({{ json_encode($cat) }})" class="text-teal-600 hover:underline font-bold">Düzenle</button>
                                <form method="POST" action="{{ route('admin.document-categories.destroy', $cat->id) }}" class="inline-block" onsubmit="return confirm('Kategoriyi silmek istediğinize emin misiniz?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline font-bold">Sil</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-xs text-neutral-400">Kategori bulunmamaktadır.</td>
                        </tr>
                    @endforelse
                </x-slot>
            </x-admin.table.layout>
        </div>

        <!-- Kategori Modal -->
        <div id="category-modal" class="fixed inset-0 z-50 hidden bg-neutral-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 p-6 max-w-md w-full shadow-premium space-y-4">
                <div class="flex justify-between items-center">
                    <h3 id="modal-title" class="text-sm font-bold text-neutral-900 dark:text-white">Yeni Doküman Kategorisi</h3>
                    <button onclick="toggleModal('category-modal')" class="text-neutral-400 hover:text-neutral-600">&times;</button>
                </div>
                
                <form id="category-form" method="POST" action="{{ route('admin.document-categories.store') }}" class="space-y-3 text-xs">
                    @csrf
                    <input type="hidden" id="form-method" name="_method" value="POST">
                    
                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Kategori Adı</label>
                        <input type="text" name="name" id="cat-name" required placeholder="Örn: Öğrenci Sözleşmeleri" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Renk Kodu</label>
                        <input type="color" name="color" id="cat-color" value="#0d9488" class="w-full h-10 p-1 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" onclick="toggleModal('category-modal')" class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 font-bold rounded-xl transition">Vazgeç</button>
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

        function editCategory(cat) {
            document.getElementById('modal-title').innerText = 'Kategoriyi Düzenle';
            document.getElementById('category-form').action = `/admin/document-categories/${cat.id}`;
            document.getElementById('form-method').value = 'PUT';

            document.getElementById('cat-name').value = cat.name;
            document.getElementById('cat-color').value = cat.color;

            toggleModal('category-modal');
        }
    </script>
@endsection
