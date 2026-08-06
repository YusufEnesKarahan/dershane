@extends('layouts.admin')
@section('title', 'Tüm Dokümanlar')
@section('content')
    <x-admin.crud.index-layout title="Dijital Doküman Arşivi" description="Sistemdeki tüm belgeleri listeleyin, arayın ve filtreleyin.">
        <x-slot name="actions">
            <x-admin.button href="{{ route('admin.documents.create') }}" variant="primary" icon="M12 4v16m8-8H4">
                Yeni Doküman Yükle
            </x-admin.button>
        </x-slot>

        <!-- Arama ve Filtreler -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm mb-6">
            <form method="GET" action="{{ route('admin.documents.search') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <x-admin.form.field-group label="Arama Metni" id="query">
                    <input type="text" name="query" value="{{ request('query') }}" placeholder="Başlık, dosya adı veya açıklama..." class="w-full text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                </x-admin.form.field-group>

                <x-admin.form.field-group label="Kategori" id="category_id">
                    <select name="category_id" id="category_id" class="w-full text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                        <option value="">Tüm Kategoriler</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </x-admin.form.field-group>

                <x-admin.form.field-group label="Dosya Türü" id="file_type">
                    <select name="file_type" id="file_type" class="w-full text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                        <option value="">Tümü</option>
                        <option value="pdf" {{ request('file_type') == 'pdf' ? 'selected' : '' }}>PDF</option>
                        <option value="docx" {{ request('file_type') == 'docx' ? 'selected' : '' }}>Word (DOCX)</option>
                        <option value="xlsx" {{ request('file_type') == 'xlsx' ? 'selected' : '' }}>Excel (XLSX)</option>
                        <option value="png" {{ request('file_type') == 'png' ? 'selected' : '' }}>Görüntü (PNG/JPG)</option>
                    </select>
                </x-admin.form.field-group>

                <div class="flex items-end gap-2">
                    <x-admin.button type="submit" variant="primary" class="w-full justify-center">Filtrele</x-admin.button>
                    <x-admin.button href="{{ route('admin.documents.index') }}" variant="secondary" class="justify-center">Sıfırla</x-admin.button>
                </div>
            </form>
        </div>

        <!-- Doküman Tablosu -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm">
            <x-admin.table.layout>
                <x-slot name="head">
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Doküman Başlığı</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Kategori</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Tür / Boyut</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Yükleyen</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Tarih</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">İşlem</th>
                </x-slot>
                <x-slot name="body">
                    @forelse($documents as $doc)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                            <td class="px-6 py-4 text-sm font-bold text-slate-900 dark:text-white">
                                <a href="{{ route('admin.documents.show', $doc->id) }}" class="hover:text-primary transition flex items-center gap-2">
                                    <span class="p-1.5 bg-slate-100 dark:bg-slate-800 rounded">📄</span>
                                    {{ $doc->title }}
                                </a>
                                <div class="text-[11px] font-mono text-slate-500 mt-0.5">{{ $doc->file_name }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold text-white shadow-sm" style="background-color: {{ $doc->category->color ?? '#0d9488' }}">
                                    {{ $doc->category->name ?? 'Genel' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm uppercase font-mono text-slate-700 dark:text-slate-300">
                                {{ $doc->file_type }}
                                <div class="text-[11px] font-normal text-slate-500 mt-0.5">{{ round($doc->file_size / 1024, 1) }} KB</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">{{ $doc->uploader->name ?? 'Sistem' }}</td>
                            <td class="px-6 py-4 text-sm font-mono text-slate-600 dark:text-slate-400">{{ $doc->created_at->format('d.m.Y H:i') }}</td>
                            <td class="px-6 py-4 text-sm space-x-2">
                                <x-admin.button href="{{ route('admin.documents.show', $doc->id) }}" variant="secondary" size="sm">Detay</x-admin.button>
                                <x-admin.button href="{{ route('admin.documents.download', $doc->id) }}" variant="success" size="sm">İndir</x-admin.button>
                                <form method="POST" action="{{ route('admin.documents.destroy', $doc->id) }}" class="inline-block" onsubmit="return confirm('Silmek istediğinize emin misiniz?')">
                                    @csrf
                                    @method('DELETE')
                                    <x-admin.button type="submit" variant="danger" size="sm">Sil</x-admin.button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-xs text-slate-400">Kayıtlı doküman bulunmamaktadır.</td>
                        </tr>
                    @endforelse
                </x-slot>
            </x-admin.table.layout>
        </div>

    </x-admin.crud.index-layout>
@endsection
