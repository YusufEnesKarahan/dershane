@extends('layouts.admin')

@section('title', 'Duyuru Düzenle')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">Duyuruyu Düzenle</h1>
            <p class="text-sm text-slate-500">Duyuru başlığını, içeriğini, dosya eklerini ve yayın durumunu güncelleyin.</p>
        </div>
        <a href="{{ route('admin.announcements.index') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-xl text-sm font-semibold hover:bg-slate-50 transition-colors">
            Listeye Dön
        </a>
    </div>

    <form action="{{ route('admin.announcements.update', $announcement->id) }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2">
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-2">Duyuru Başlığı *</label>
                <input type="text" name="title" required value="{{ old('title', $announcement->title) }}" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-2">Kategori *</label>
                <select name="category_id" required class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm">
                    <option value="">Kategori Seçiniz</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $announcement->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-2">Kısa Özet</label>
            <input type="text" name="summary" value="{{ old('summary', $announcement->summary) }}" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm">
        </div>

        <div>
            <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-2">Duyuru Detayı & İçeriği *</label>
            <textarea name="content" rows="8" required class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500">{{ old('content', $announcement->content) }}</textarea>
        </div>

        <!-- Zamanlama & Mod Ayarları -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-2">Yayınlama Zamanı (publish_at)</label>
                <input type="datetime-local" name="publish_at" value="{{ old('publish_at', $announcement->publish_at ? $announcement->publish_at->format('Y-m-d\TH:i') : '') }}" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-2">Bitiş Tarihi (expire_at)</label>
                <input type="datetime-local" name="expire_at" value="{{ old('expire_at', $announcement->expire_at ? $announcement->expire_at->format('Y-m-d\TH:i') : '') }}" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-2">Sabitleme (is_pinned)</label>
                <select name="is_pinned" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm">
                    <option value="0" {{ old('is_pinned', $announcement->is_pinned) ? '' : 'selected' }}>Standart Sıralama</option>
                    <option value="1" {{ old('is_pinned', $announcement->is_pinned) ? 'selected' : '' }}>En Üstte Sabitle</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-2">Popup Modu (is_popup)</label>
                <select name="is_popup" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm">
                    <option value="0" {{ old('is_popup', $announcement->is_popup) ? '' : 'selected' }}>Hayır</option>
                    <option value="1" {{ old('is_popup', $announcement->is_popup) ? 'selected' : '' }}>Evet (Popup Modal)</option>
                </select>
            </div>
        </div>

        @if($announcement->attachments->count() > 0)
            <div class="p-4 bg-slate-50 dark:bg-slate-800/40 rounded-xl border border-slate-200 dark:border-slate-700 space-y-2">
                <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Mevcut Ek Dosyalar</h4>
                <div class="flex flex-wrap gap-3">
                    @foreach($announcement->attachments as $att)
                        <div class="px-3 py-1.5 bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-700 text-xs flex items-center gap-2">
                            <i class="fas fa-paperclip text-slate-400"></i>
                            <span class="font-medium text-slate-800 dark:text-slate-200">{{ $att->file_name }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div>
            <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-2">Yeni Dosya Ekleri Ekle</label>
            <input type="file" name="attachments[]" multiple class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2 text-sm">
        </div>

        <div class="flex items-center justify-between pt-6 border-t border-slate-100 dark:border-slate-800">
            <select name="status" class="bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2 text-sm font-bold">
                <option value="Published" {{ $announcement->status === 'Published' ? 'selected' : '' }}>Yayında</option>
                <option value="Draft" {{ $announcement->status === 'Draft' ? 'selected' : '' }}>Taslak</option>
                <option value="Scheduled" {{ $announcement->status === 'Scheduled' ? 'selected' : '' }}>Zamanlanmış</option>
                <option value="Archived" {{ $announcement->status === 'Archived' ? 'selected' : '' }}>Arşivde</option>
            </select>

            <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm transition-colors shadow-sm">
                <i class="fas fa-save mr-1"></i> Değişiklikleri Kaydet
            </button>
        </div>
    </form>
</div>
@endsection
