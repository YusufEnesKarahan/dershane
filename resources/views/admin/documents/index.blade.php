@extends('layouts.admin')
@section('title', 'Tüm Dokümanlar')
@section('content')
    <div class="space-y-6">
        
        <!-- Header -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm flex justify-between items-center">
            <div>
                <h1 class="text-lg font-bold text-neutral-900 dark:text-white">Dijital Doküman Arşivi</h1>
                <p class="text-xs text-neutral-500 mt-1">Sistemdeki tüm belgeleri listeleyin, arayın ve filtreleyin.</p>
            </div>
            
            <a href="{{ route('admin.documents.create') }}" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-xs font-bold text-white rounded-xl transition shadow-lg shadow-teal-950">
                Yeni Doküman Yükle
            </a>
        </div>

        <!-- Arama ve Filtreler -->
        <form method="GET" action="{{ route('admin.documents.search') }}" class="bg-white dark:bg-neutral-900 p-4 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm grid grid-cols-1 md:grid-cols-4 gap-4 text-xs">
            <div>
                <label class="font-bold text-neutral-600 dark:text-neutral-400 block mb-1">Arama Metni</label>
                <input type="text" name="query" value="{{ request('query') }}" placeholder="Başlık, dosya adı veya açıklama..." class="w-full p-2 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
            </div>

            <div>
                <label class="font-bold text-neutral-600 dark:text-neutral-400 block mb-1">Kategori</label>
                <select name="category_id" class="w-full p-2 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    <option value="">Tüm Kategoriler</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="font-bold text-neutral-600 dark:text-neutral-400 block mb-1">Dosya Türü</label>
                <select name="file_type" class="w-full p-2 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    <option value="">Tümü</option>
                    <option value="pdf" {{ request('file_type') == 'pdf' ? 'selected' : '' }}>PDF</option>
                    <option value="docx" {{ request('file_type') == 'docx' ? 'selected' : '' }}>Word (DOCX)</option>
                    <option value="xlsx" {{ request('file_type') == 'xlsx' ? 'selected' : '' }}>Excel (XLSX)</option>
                    <option value="png" {{ request('file_type') == 'png' ? 'selected' : '' }}>Görüntü (PNG/JPG)</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="w-full p-2 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl transition">Filtrele</button>
                <a href="{{ route('admin.documents.index') }}" class="p-2 bg-neutral-100 dark:bg-neutral-800 font-bold rounded-xl transition text-center">Sıfırla</a>
            </div>
        </form>

        <!-- Doküman Tablosu -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <x-admin.table.layout>
                <x-slot name="head">
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Doküman Başlığı</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Kategori</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Tür / Boyut</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Yükleyen</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Tarih</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">İşlem</th>
                </x-slot>
                <x-slot name="body">
                    @forelse($documents as $doc)
                        <tr>
                            <td class="px-4 py-3 text-xs font-bold text-neutral-900 dark:text-white">
                                <a href="{{ route('admin.documents.show', $doc->id) }}" class="hover:text-teal-600 flex items-center gap-2">
                                    <span class="p-1.5 bg-neutral-100 dark:bg-neutral-800 rounded">📄</span>
                                    {{ $doc->title }}
                                </a>
                                <div class="text-[10px] font-mono text-neutral-400 mt-0.5">{{ $doc->file_name }}</div>
                            </td>
                            <td class="px-4 py-3 text-xs">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold text-white" style="background-color: {{ $doc->category->color ?? '#0d9488' }}">
                                    {{ $doc->category->name ?? 'Genel' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs uppercase font-mono">
                                {{ $doc->file_type }}
                                <div class="text-[10px] font-normal text-neutral-400">{{ round($doc->file_size / 1024, 1) }} KB</div>
                            </td>
                            <td class="px-4 py-3 text-xs">{{ $doc->uploader->name ?? 'Sistem' }}</td>
                            <td class="px-4 py-3 text-xs font-mono">{{ $doc->created_at->format('d.m.Y H:i') }}</td>
                            <td class="px-4 py-3 text-xs space-x-2">
                                <a href="{{ route('admin.documents.show', $doc->id) }}" class="text-teal-600 hover:underline font-bold">Detay</a>
                                <a href="{{ route('admin.documents.download', $doc->id) }}" class="text-blue-600 hover:underline font-bold">İndir</a>
                                <form method="POST" action="{{ route('admin.documents.destroy', $doc->id) }}" class="inline-block" onsubmit="return confirm('Silmek istediğinize emin misiniz?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline font-bold">Sil</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-xs text-neutral-400">Kayıtlı doküman bulunmamaktadır.</td>
                        </tr>
                    @endforelse
                </x-slot>
            </x-admin.table.layout>
        </div>

    </div>
@endsection
