@extends('layouts.admin')

@section('title', 'Yeni Haftalık Çalışma Programı')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">Yeni Haftalık Çalışma Programı Hazırla</h1>
            <p class="text-sm text-slate-500">Öğrenciler için konu, kaynak kitap, sayfa aralığı ve hedef süreleri tanımlayın.</p>
        </div>
        <a href="{{ route('admin.homeworks.index') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-xl text-sm font-semibold hover:bg-slate-50 transition-colors">
            İptal / Dön
        </a>
    </div>

    @if(session('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl font-bold text-sm">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('admin.homeworks.store') }}" method="POST" class="bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-2">Program / Ödev Başlığı *</label>
                <input type="text" name="title" required value="{{ old('title') }}" placeholder="Örn: 3. Hafta - Türev & İntegral Soru Çözümü" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                @error('title') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-2">Hafta No</label>
                <input type="number" name="week_number" value="{{ old('week_number', 1) }}" min="1" max="52" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-2">Konu Adı</label>
                <input type="text" name="subject" value="{{ old('subject') }}" placeholder="Örn: Trigonometri III" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-2">Sınıf *</label>
                <select name="classroom_id" required class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm">
                    <option value="">Sınıf Seçiniz</option>
                    @foreach($classrooms as $c)
                        <option value="{{ $c->id }}" {{ old('classroom_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-2">Ders *</label>
                <select name="course_id" required class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm">
                    <option value="">Ders Seçiniz</option>
                    @foreach($courses as $co)
                        <option value="{{ $co->id }}" {{ old('course_id') == $co->id ? 'selected' : '' }}>{{ $co->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-2">Öğretmen *</label>
                <select name="teacher_id" required class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm">
                    <option value="">Öğretmen Seçiniz</option>
                    @foreach($teachers as $t)
                        <option value="{{ $t->id }}" {{ old('teacher_id') == $t->id ? 'selected' : '' }}>{{ $t->user?->name ?? 'Öğretmen #' . $t->id }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-2">Başlangıç Tarihi</label>
                <input type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-2">Son Teslim / Bitiş Tarihi *</label>
                <input type="datetime-local" name="due_date" required value="{{ old('due_date', date('Y-m-d\TH:i', strtotime('+7 days'))) }}" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-2">Öncelik Seviyesi *</label>
                <select name="priority" required class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm">
                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Düşük</option>
                    <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Orta</option>
                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>Yüksek</option>
                    <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Acil</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-2">Tahmini Süre (Dakika) *</label>
                <input type="number" name="estimated_minutes" required value="{{ old('estimated_minutes', 60) }}" min="5" max="600" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-2">Kaynak Kitap</label>
                <input type="text" name="source_book" value="{{ old('source_book') }}" placeholder="Örn: Çap Yayınları AYT Soru Bankası" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-2">Sayfa Aralığı</label>
                <input type="text" name="page_range" value="{{ old('page_range') }}" placeholder="Örn: S. 142 - 158 (Test 1-6)" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-2">Video Anlatım Linki</label>
                <input type="url" name="video_url" value="{{ old('video_url') }}" placeholder="https://youtube.com/..." class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-2">Açıklama & Çalışma Notları</label>
            <textarea name="description" rows="4" placeholder="Çalışma programı detayları, dikkat edilecek hususlar..." class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm">{{ old('description') }}</textarea>
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-4">
                <label class="inline-flex items-center gap-2 cursor-pointer text-sm font-semibold text-slate-700 dark:text-slate-300">
                    <input type="radio" name="status" value="published" checked class="text-blue-600 focus:ring-blue-500">
                    Hemen Yayınla
                </label>
                <label class="inline-flex items-center gap-2 cursor-pointer text-sm font-semibold text-slate-700 dark:text-slate-300">
                    <input type="radio" name="status" value="draft" class="text-amber-600 focus:ring-amber-500">
                    Taslak Olarak Kaydet
                </label>
            </div>

            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-sm transition-colors shadow-sm">
                <i class="fas fa-save mr-1"></i> Programı Kaydet
            </button>
        </div>
    </form>
</div>
@endsection
