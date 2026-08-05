@extends('layouts.admin')

@section('title', 'Haftalık Çalışma Programı Düzenle')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-black text-neutral-900 dark:text-white">Çalışma Programını Düzenle</h1>
            <p class="text-sm text-neutral-500">Program içeriklerini, kaynak kitapları ve öncelik ayarlarını güncelleyin.</p>
        </div>
        <a href="{{ route('admin.homeworks.index') }}" class="px-4 py-2 bg-white border border-neutral-200 text-neutral-700 rounded-xl text-sm font-semibold hover:bg-neutral-50 transition-colors">
            Listeye Dön
        </a>
    </div>

    @if(session('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl font-bold text-sm">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('admin.homeworks.update', $homework->id) }}" method="POST" class="bg-white dark:bg-neutral-900 p-6 sm:p-8 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-xs font-bold uppercase text-neutral-600 dark:text-neutral-300 mb-2">Program / Ödev Başlığı *</label>
                <input type="text" name="title" required value="{{ old('title', $homework->title) }}" class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-neutral-600 dark:text-neutral-300 mb-2">Hafta No</label>
                <input type="number" name="week_number" value="{{ old('week_number', $homework->week_number ?? 1) }}" min="1" max="52" class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-neutral-600 dark:text-neutral-300 mb-2">Konu Adı</label>
                <input type="text" name="subject" value="{{ old('subject', $homework->subject) }}" class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <label class="block text-xs font-bold uppercase text-neutral-600 dark:text-neutral-300 mb-2">Başlangıç Tarihi</label>
                <input type="date" name="start_date" value="{{ old('start_date', $homework->start_date ? $homework->start_date->format('Y-m-d') : date('Y-m-d')) }}" class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-xl px-4 py-2.5 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-neutral-600 dark:text-neutral-300 mb-2">Bitiş Tarihi *</label>
                <input type="datetime-local" name="due_date" required value="{{ old('due_date', $homework->due_date ? $homework->due_date->format('Y-m-d\TH:i') : '') }}" class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-xl px-4 py-2.5 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-neutral-600 dark:text-neutral-300 mb-2">Öncelik Seviyesi *</label>
                <select name="priority" required class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-xl px-4 py-2.5 text-sm">
                    <option value="low" {{ old('priority', $homework->priority) == 'low' ? 'selected' : '' }}>Düşük</option>
                    <option value="medium" {{ old('priority', $homework->priority) == 'medium' ? 'selected' : '' }}>Orta</option>
                    <option value="high" {{ old('priority', $homework->priority) == 'high' ? 'selected' : '' }}>Yüksek</option>
                    <option value="urgent" {{ old('priority', $homework->priority) == 'urgent' ? 'selected' : '' }}>Acil</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-neutral-600 dark:text-neutral-300 mb-2">Tahmini Süre (Dk) *</label>
                <input type="number" name="estimated_minutes" required value="{{ old('estimated_minutes', $homework->estimated_minutes ?? 45) }}" min="5" max="600" class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-xl px-4 py-2.5 text-sm">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-xs font-bold uppercase text-neutral-600 dark:text-neutral-300 mb-2">Kaynak Kitap</label>
                <input type="text" name="source_book" value="{{ old('source_book', $homework->source_book) }}" class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-xl px-4 py-2.5 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-neutral-600 dark:text-neutral-300 mb-2">Sayfa Aralığı</label>
                <input type="text" name="page_range" value="{{ old('page_range', $homework->page_range) }}" class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-xl px-4 py-2.5 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-neutral-600 dark:text-neutral-300 mb-2">Video Linki</label>
                <input type="url" name="video_url" value="{{ old('video_url', $homework->video_url) }}" class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-xl px-4 py-2.5 text-sm">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase text-neutral-600 dark:text-neutral-300 mb-2">Açıklama</label>
            <textarea name="description" rows="4" class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-xl px-4 py-2.5 text-sm">{{ old('description', $homework->description) }}</textarea>
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-neutral-100 dark:border-neutral-800">
            <select name="status" class="bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-xl px-4 py-2 text-sm font-bold">
                <option value="draft" {{ $homework->status === 'draft' ? 'selected' : '' }}>Taslak</option>
                <option value="published" {{ $homework->status === 'published' ? 'selected' : '' }}>Yayında</option>
                <option value="completed" {{ $homework->status === 'completed' ? 'selected' : '' }}>Tamamlandı</option>
            </select>

            <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-sm transition-colors shadow-sm">
                <i class="fas fa-save mr-1"></i> Değişiklikleri Kaydet
            </button>
        </div>
    </form>
</div>
@endsection
