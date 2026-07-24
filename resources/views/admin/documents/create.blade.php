@extends('layouts.admin')
@section('title', 'Yeni Doküman Yükle')
@section('content')
    <div class="space-y-6 max-w-3xl mx-auto">
        
        <!-- Header -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <h1 class="text-lg font-bold text-neutral-900 dark:text-white">Yeni Doküman / Belge Yükle</h1>
            <p class="text-xs text-neutral-500 mt-1">Dijital arşive yeni bir dosya veya evrak eklemek için aşağıdaki formu doldurun.</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('admin.documents.store') }}" enctype="multipart/form-data" class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4 text-xs">
            @csrf
            
            <div class="space-y-1">
                <label class="font-bold text-neutral-600 dark:text-neutral-400">Doküman Başlığı</label>
                <input type="text" name="title" required placeholder="Örn: 2026 Eğitim Sözleşmesi Şablonu" class="w-full p-3 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
            </div>

            <div class="space-y-1">
                <label class="font-bold text-neutral-600 dark:text-neutral-400">Kategori</label>
                <select name="category_id" required class="w-full p-3 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    <option value="">Kategori Seçin</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1">
                <label class="font-bold text-neutral-600 dark:text-neutral-400">Dosya Seçin (PDF, DOCX, XLSX, PNG, max 50MB)</label>
                <input type="file" name="file" class="w-full p-3 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
            </div>

            <div class="space-y-1">
                <label class="font-bold text-neutral-600 dark:text-neutral-400">Açıklama / Notlar</label>
                <textarea name="description" rows="3" placeholder="Belge hakkında ek detaylar yazabilirsiniz..." class="w-full p-3 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-4">
                <a href="{{ route('admin.documents.index') }}" class="px-5 py-2.5 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 font-bold rounded-xl transition">Vazgeç</a>
                <button type="submit" class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl transition shadow-lg shadow-teal-950">Yükle ve Kaydet</button>
            </div>
        </form>

    </div>
@endsection
