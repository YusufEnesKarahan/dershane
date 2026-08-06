@extends('layouts.admin')

@section('title', 'Kesin Kayıda Dönüştür')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white flex items-center gap-3">
                <i class="fas fa-magic text-emerald-600"></i> Tek Tuşla Kesin Kayıda Dönüştür
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                <strong>{{ $preRegistration->student_name }}</strong> isimli ön kaydı Öğrenci, Veli ve Fatura kayıtları ile tek işlemde kesin kayıda aktarın.
            </p>
        </div>
        <a href="{{ route('admin.pre-registrations.index') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-xl text-sm font-semibold hover:bg-slate-50">
            İptal / Listeye Dön
        </a>
    </div>

    <form action="{{ route('admin.pre-registrations.convert.store', $preRegistration->id) }}" method="POST" class="bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm space-y-6">
        @csrf

        <!-- Aday Bilgileri Özeti -->
        <div class="p-4 bg-blue-50 dark:bg-blue-950/30 rounded-xl border border-blue-200 dark:border-blue-800 flex items-center justify-between">
            <div>
                <div class="text-xs font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400">Aday Kayıt Özeti</div>
                <div class="text-lg font-black text-blue-950 dark:text-blue-200 mt-0.5">{{ $preRegistration->student_name }}</div>
                <div class="text-xs text-blue-700 dark:text-blue-300">Telefon: {{ $preRegistration->phone }} | Program: {{ $preRegistration->interested_program ?: 'Belirtilmedi' }}</div>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300">
                {{ $preRegistration->source }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-2">Öğrenci Numarası (Otomatik veya Manuel)</label>
                <input type="text" name="student_number" value="{{ old('student_number', 'STU-' . rand(10000, 99999)) }}" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm font-mono font-bold">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-2">TC Kimlik No (Opsiyonel)</label>
                <input type="text" name="tc_no" value="{{ old('tc_no') }}" placeholder="11 Haneli TC No" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-2">Sınıf / Şube Ataması</label>
                <select name="classroom_id" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm">
                    <option value="">Sınıf Seçiniz</option>
                    @foreach($classrooms as $cls)
                        <option value="{{ $cls->id }}" {{ old('classroom_id') == $cls->id ? 'selected' : '' }}>{{ $cls->name }} (Kapasite: {{ $cls->capacity }})</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Veli Hesabı Oluşturma -->
        <div class="p-6 bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-4">
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-user-shield text-emerald-600"></i> Veli Hesabı Oluştur
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-2">Veli Ad Soyad</label>
                    <input type="text" name="guardian_name" value="{{ old('guardian_name') }}" placeholder="Örn: Mehmet Özdemir" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-2">Veli Telefon</label>
                    <input type="text" name="guardian_phone" value="{{ old('guardian_phone', $preRegistration->phone) }}" placeholder="05XX XXX XX XX" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-2">Veli E-Posta</label>
                    <input type="email" name="guardian_email" value="{{ old('guardian_email') }}" placeholder="veli@mail.com" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm">
                </div>
            </div>
        </div>

        <!-- İlk Fatura Oluşturma -->
        <div class="p-6 bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-4">
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-file-invoice-dollar text-emerald-600"></i> İlk Fatura & Öğrenim Ücreti
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-2">Kayıt / Toplam Öğrenim Ücreti (TL)</label>
                    <input type="number" step="0.01" name="tuition_amount" value="{{ old('tuition_amount', '15000') }}" min="0" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm font-bold">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-2">Fatura Son Ödeme Vadesi</label>
                    <input type="date" name="due_date" value="{{ old('due_date', now()->addDays(7)->format('Y-m-d')) }}" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm">
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-4 border-t border-slate-100 dark:border-slate-800">
            <button type="submit" class="px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-black rounded-xl text-sm transition-colors shadow-lg flex items-center gap-2">
                <i class="fas fa-check-circle"></i> Kesin Kaydı Başlat (Atomik İşlem)
            </button>
        </div>
    </form>
</div>
@endsection
