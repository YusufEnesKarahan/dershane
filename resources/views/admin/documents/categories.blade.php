@extends('layouts.admin')
@section('title', 'Doküman Kategorileri')
@section('content')
    <x-admin.crud.index-layout title="Doküman Kategorileri" description="Dijital arşivi sınıflandırmak için evrak türleri ve renk tanımlarını yönetin.">
        <x-slot name="actions">
            <x-admin.button type="button" onclick="toggleModal('category-modal')" variant="primary" icon="M12 4v16m8-8H4">
                Yeni Kategori Ekle
            </x-admin.button>
        </x-slot>

        <!-- Kategori Listesi -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm">
            <x-admin.table.layout>
                <x-slot name="head">
                    <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Renk / Kategori Adı</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Slug</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Belge Sayısı</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Durum</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">İşlem</th>
                </x-slot>
                <x-slot name="body">
                    @forelse($categories as $cat)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40">
                            <td class="px-6 py-4 text-sm font-bold text-neutral-900 dark:text-white flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full" style="background-color: {{ $cat->color }}"></span>
                                {{ $cat->name }}
                            </td>
                            <td class="px-6 py-4 text-sm font-mono text-neutral-500">{{ $cat->slug }}</td>
                            <td class="px-6 py-4 text-sm font-bold font-mono">{{ $cat->documents_count }} Belge</td>
                            <td class="px-6 py-4 text-sm">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $cat->is_active ? 'bg-emerald-100 text-emerald-800 border-emerald-200/50 dark:bg-emerald-500/20 dark:text-emerald-400 dark:border-emerald-500/20' : 'bg-neutral-100 text-neutral-800 border-neutral-200/50 dark:bg-neutral-800 dark:text-neutral-300 dark:border-neutral-700' }}">
                                    {{ $cat->is_active ? 'Aktif' : 'Pasif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm space-x-2">
                                <x-admin.button type="button" onclick="editCategory({{ json_encode($cat) }})" variant="secondary" size="sm">Düzenle</x-admin.button>
                                <form method="POST" action="{{ route('admin.document-categories.destroy', $cat->id) }}" class="inline-block" onsubmit="return confirm('Kategoriyi silmek istediğinize emin misiniz?')">
                                    @csrf
                                    @method('DELETE')
                                    <x-admin.button type="submit" variant="danger" size="sm">Sil</x-admin.button>
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
            <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 p-6 max-w-md w-full shadow-sm space-y-4">
                <div class="flex justify-between items-center">
                    <h3 id="modal-title" class="text-lg font-bold text-neutral-900 dark:text-white">Yeni Doküman Kategorisi</h3>
                    <button onclick="toggleModal('category-modal')" class="text-neutral-400 hover:text-neutral-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <form id="category-form" method="POST" action="{{ route('admin.document-categories.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" id="form-method" name="_method" value="POST">
                    
                    <x-admin.form.field-group label="Kategori Adı" id="cat-name" required>
                        <input type="text" name="name" id="cat-name" required placeholder="Örn: Öğrenci Sözleşmeleri" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Renk Kodu" id="cat-color">
                        <input type="color" name="color" id="cat-color" value="#0d9488" class="w-full h-10 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-2 py-1 cursor-pointer">
                    </x-admin.form.field-group>

                    <div class="flex justify-end gap-2 pt-2">
                        <x-admin.button type="button" onclick="toggleModal('category-modal')" variant="secondary">
                            Vazgeç
                        </x-admin.button>
                        <x-admin.button type="submit" variant="primary">
                            Kaydet
                        </x-admin.button>
                    </div>
                </form>
            </div>
        </div>

    </x-admin.crud.index-layout>

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
