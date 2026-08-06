@extends('layouts.admin')

@section('title', 'Yeni Duyuru Oluştur')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-black text-neutral-900 dark:text-white">Yeni Duyuru Oluştur</h1>
            <p class="text-sm text-neutral-500">Portallarda ve panoda yayınlanacak duyuru içeriklerini tanımlayın.</p>
        </div>
        <a href="{{ route('admin.announcements.index') }}" class="px-4 py-2 bg-white border border-neutral-200 text-neutral-700 rounded-xl text-sm font-semibold hover:bg-neutral-50 transition-colors">
            Listeye Dön
        </a>
    </div>

    <form action="{{ route('admin.announcements.store') }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-neutral-900 p-6 sm:p-8 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2">
                <label class="block text-xs font-bold uppercase text-neutral-600 dark:text-neutral-300 mb-2">Duyuru Başlığı *</label>
                <input type="text" name="title" required value="{{ old('title') }}" placeholder="Örn: 2026 YKS Deneme Sınavı Takvimi Açıklandı" class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500">
                @error('title') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-neutral-600 dark:text-neutral-300 mb-2">Kategori *</label>
                <select name="category_id" required class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500">
                    <option value="">Kategori Seçiniz</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase text-neutral-600 dark:text-neutral-300 mb-2">Kısa Özet</label>
            <input type="text" name="summary" value="{{ old('summary') }}" placeholder="Duyurunun portallarda ve widgetlarda gösterilecek kısa özeti..." class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-xl px-4 py-2.5 text-sm">
        </div>

        <div>
            <label class="block text-xs font-bold uppercase text-neutral-600 dark:text-neutral-300 mb-2">Duyuru Detayı & İçeriği *</label>
            <textarea name="content" rows="8" required placeholder="Duyuru metni, detaylar ve yönlendirmeler..." class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500">{{ old('content') }}</textarea>
            @error('content') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- Hedef Kitle & Şube Ayarları -->
        <div class="p-4 bg-neutral-50 dark:bg-neutral-800/40 rounded-xl border border-neutral-200 dark:border-neutral-700 space-y-4">
            <h4 class="text-xs font-bold text-neutral-700 dark:text-neutral-300 uppercase tracking-wider">Hedef Kitle & Şube Kapsamı</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase text-neutral-600 dark:text-neutral-300 mb-2">Hedef Rol</label>
                    <select name="target_role" class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-xl px-4 py-2.5 text-sm">
                        <option value="all" {{ old('target_role') == 'all' ? 'selected' : '' }}>Tüm Kullanıcılar (Herkes)</option>
                        <option value="Student" {{ old('target_role') == 'Student' ? 'selected' : '' }}>Sadece Öğrenciler</option>
                        <option value="Parent" {{ old('target_role') == 'Parent' ? 'selected' : '' }}>Sadece Veliler</option>
                        <option value="Teacher" {{ old('target_role') == 'Teacher' ? 'selected' : '' }}>Sadece Öğretmenler</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-neutral-600 dark:text-neutral-300 mb-2">Şube Yayın Türü</label>
                    <select name="is_all_branches" class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-xl px-4 py-2.5 text-sm">
                        <option value="1" {{ old('is_all_branches', 1) == 1 ? 'selected' : '' }}>Tüm Şubelerde Yayınla</option>
                        <option value="0" {{ old('is_all_branches') == 0 ? 'selected' : '' }}>Seçili Şubelerde Yayınla</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-neutral-600 dark:text-neutral-300 mb-2">Hedef Şubeler (Çoklu Seçim)</label>
                    <select name="branch_ids[]" multiple class="w-full h-24 bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-xl px-4 py-2 text-xs">
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Zamanlama & Mod Ayarları -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <label class="block text-xs font-bold uppercase text-neutral-600 dark:text-neutral-300 mb-2">Yayınlama Zamanı (publish_at)</label>
                <input type="datetime-local" name="publish_at" value="{{ old('publish_at') }}" class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-xl px-4 py-2.5 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-neutral-600 dark:text-neutral-300 mb-2">Bitiş / Gizlenme Tarihi (expire_at)</label>
                <input type="datetime-local" name="expire_at" value="{{ old('expire_at') }}" class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-xl px-4 py-2.5 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-neutral-600 dark:text-neutral-300 mb-2">Sabitleme (is_pinned)</label>
                <select name="is_pinned" class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-xl px-4 py-2.5 text-sm">
                    <option value="0" {{ old('is_pinned') == 0 ? 'selected' : '' }}>Standart Sıralama</option>
                    <option value="1" {{ old('is_pinned') == 1 ? 'selected' : '' }}>En Üstte Sabitle</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-neutral-600 dark:text-neutral-300 mb-2">Popup Modu (is_popup)</label>
                <select name="is_popup" class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-xl px-4 py-2.5 text-sm">
                    <option value="0" {{ old('is_popup') == 0 ? 'selected' : '' }}>Hayır (Liste Görünümü)</option>
                    <option value="1" {{ old('is_popup') == 1 ? 'selected' : '' }}>Evet (Girişte 1 Kez Açılan Modal)</option>
                </select>
            </div>
        </div>

        <!-- Dosya Ekleri & Bildirim Seçeneği -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold uppercase text-neutral-600 dark:text-neutral-300 mb-2">Dosya Ekleri (PDF, Word, Excel, Görsel)</label>
                <input type="file" name="attachments[]" multiple class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-xl px-4 py-2 text-sm">
                <span class="text-[11px] text-neutral-500 mt-1 block">Maksimum 10MB boyutunda PDF, Word, Excel veya Görsel dosyaları seçebilirsiniz.</span>
            </div>

            <div class="flex items-center gap-3 pt-6">
                <label class="inline-flex items-center gap-2 cursor-pointer text-sm font-bold text-neutral-800 dark:text-neutral-200">
                    <input type="checkbox" name="send_notification" value="1" checked class="w-4 h-4 text-emerald-600 rounded focus:ring-emerald-500">
                    Yayınlandığında Hedef Kullanıcılara Anlık Veritabanı Bildirimi Gönder
                </label>
            </div>
        </div>

        <div class="flex items-center justify-between pt-6 border-t border-neutral-100 dark:border-neutral-800">
            <div class="flex items-center gap-4">
                <label class="inline-flex items-center gap-2 cursor-pointer text-sm font-semibold text-neutral-700 dark:text-neutral-300">
                    <input type="radio" name="status" value="Published" checked class="text-emerald-600 focus:ring-emerald-500">
                    Yayınla
                </label>
                <label class="inline-flex items-center gap-2 cursor-pointer text-sm font-semibold text-neutral-700 dark:text-neutral-300">
                    <input type="radio" name="status" value="Draft" class="text-amber-600 focus:ring-amber-500">
                    Taslak Kaydet
                </label>
            </div>

            <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm transition-colors shadow-sm">
                <i class="fas fa-save mr-1"></i> Duyuruyu Kaydet
            </button>
        </div>
    </form>
</div>
@endsection
