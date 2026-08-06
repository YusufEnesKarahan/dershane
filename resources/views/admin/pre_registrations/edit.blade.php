@extends('layouts.admin')

@section('title', 'Ön Kayıt Düzenle')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-black text-neutral-900 dark:text-white">Ön Kaydı Düzenle</h1>
            <p class="text-sm text-neutral-500">{{ $preRegistration->student_name }} isimli adayın bilgilerini güncelleyin.</p>
        </div>
        <a href="{{ route('admin.pre-registrations.index') }}" class="px-4 py-2 bg-white border border-neutral-200 text-neutral-700 rounded-xl text-sm font-semibold hover:bg-neutral-50">
            Listeye Dön
        </a>
    </div>

    <form action="{{ route('admin.pre-registrations.update', $preRegistration->id) }}" method="POST" class="bg-white dark:bg-neutral-900 p-6 sm:p-8 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-xs font-bold uppercase text-neutral-600 dark:text-neutral-300 mb-2">Öğrenci Ad Soyad *</label>
                <input type="text" name="student_name" required value="{{ old('student_name', $preRegistration->student_name) }}" class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-xl px-4 py-2.5 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-neutral-600 dark:text-neutral-300 mb-2">Telefon *</label>
                <input type="text" name="phone" required value="{{ old('phone', $preRegistration->phone) }}" class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-xl px-4 py-2.5 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-neutral-600 dark:text-neutral-300 mb-2">E-Posta</label>
                <input type="email" name="email" value="{{ old('email', $preRegistration->email) }}" class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-xl px-4 py-2.5 text-sm">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <label class="block text-xs font-bold uppercase text-neutral-600 dark:text-neutral-300 mb-2">Hedef Sınıf / Seviye</label>
                <input type="text" name="classroom_name" value="{{ old('classroom_name', $preRegistration->classroom_name) }}" class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-xl px-4 py-2.5 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-neutral-600 dark:text-neutral-300 mb-2">İlgilendiği Program</label>
                <input type="text" name="interested_program" value="{{ old('interested_program', $preRegistration->interested_program) }}" class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-xl px-4 py-2.5 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-neutral-600 dark:text-neutral-300 mb-2">Başvuru Kaynağı *</label>
                <select name="source" required class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-xl px-4 py-2.5 text-sm">
                    @foreach(['Instagram', 'Google', 'Referans', 'Web', 'Telefon', 'Diğer'] as $src)
                        <option value="{{ $src }}" {{ old('source', $preRegistration->source) === $src ? 'selected' : '' }}>{{ $src }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-neutral-600 dark:text-neutral-300 mb-2">Durum *</label>
                <select name="status" required class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-xl px-4 py-2.5 text-sm">
                    @foreach(['Yeni', 'Arandı', 'Randevu', 'Kayıt Oldu', 'İptal'] as $st)
                        <option value="{{ $st }}" {{ old('status', $preRegistration->status) === $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase text-neutral-600 dark:text-neutral-300 mb-2">Görüşme Notları</label>
            <textarea name="notes" rows="4" class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-xl px-4 py-2.5 text-sm">{{ old('notes', $preRegistration->notes) }}</textarea>
        </div>

        <div class="flex justify-between items-center pt-4 border-t border-neutral-100 dark:border-neutral-800">
            @if($preRegistration->status !== 'Kayıt Oldu')
                <a href="{{ route('admin.pre-registrations.convert', $preRegistration->id) }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm transition-colors flex items-center gap-2">
                    <i class="fas fa-check-circle"></i> Tek Tuşla Kesin Kayıda Dönüştür
                </a>
            @else
                <div></div>
            @endif

            <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-sm transition-colors shadow-sm">
                Değişiklikleri Kaydet
            </button>
        </div>
    </form>
</div>
@endsection
