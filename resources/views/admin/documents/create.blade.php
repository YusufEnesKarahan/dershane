@extends('layouts.admin')
@section('title', 'Yeni Doküman Yükle')
@section('content')
    <div class="space-y-6 max-w-3xl mx-auto">
        
        <!-- Header -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm">

        <!-- Form -->
        <x-admin.form.layout method="POST" action="{{ route('admin.documents.store') }}" enctype="multipart/form-data" class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm">
            
            <x-admin.form.field-group label="Doküman Başlığı" id="title" required>
                <input type="text" name="title" required placeholder="Örn: 2026 Eğitim Sözleşmesi Şablonu" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
            </x-admin.form.field-group>

            <x-admin.form.field-group label="Kategori" id="category_id" required>
                <select name="category_id" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                    <option value="">Kategori Seçin</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </x-admin.form.field-group>

            <x-admin.form.field-group label="Dosya Seçin (PDF, DOCX, XLSX, PNG, max 50MB)" id="file" required>
                <input type="file" name="file" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer">
            </x-admin.form.field-group>

            <x-admin.form.field-group label="Açıklama / Notlar" id="description">
                <textarea name="description" rows="3" placeholder="Belge hakkında ek detaylar yazabilirsiniz..." class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors resize-none"></textarea>
            </x-admin.form.field-group>

            <div class="flex justify-end gap-2 pt-4">
                <x-admin.button href="{{ route('admin.documents.index') }}" variant="secondary">
                    Vazgeç
                </x-admin.button>
                <x-admin.button type="submit" variant="primary">
                    Yükle ve Kaydet
                </x-admin.button>
            </div>
        </x-admin.form.layout>

    </div>
@endsection
