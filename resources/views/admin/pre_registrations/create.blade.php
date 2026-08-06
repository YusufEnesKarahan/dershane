@extends('layouts.admin')

@section('title', 'Yeni Ön Kayıt')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">Yeni Ön Kayıt Ekle</h1>
            <p class="text-sm text-slate-500">Aday öğrenci görüşme bilgilerini kaydedin.</p>
        </div>
        <a href="{{ route('admin.pre-registrations.index') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-xl text-sm font-semibold hover:bg-slate-50">
            Listeye Dön
        </a>
    </div>

    <form action="{{ route('admin.pre-registrations.store') }}" method="POST" class="bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-2">Öğrenci Ad Soyad *</label>
                <input type="text" name="student_name" required value="{{ old('student_name') }}" placeholder="Örn: Burak Özdemir" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm">
                @error('student_name') <span class="text-xs text-rose-500 block mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-2">Telefon *</label>
                <input type="text" name="phone" required value="{{ old('phone') }}" placeholder="05XX XXX XX XX" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm">
                @error('phone') <span class="text-xs text-rose-500 block mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-2">E-Posta (Opsiyonel)</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="ornek@mail.com" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-2">Hedef Sınıf / Seviye</label>
                <input type="text" name="classroom_name" value="{{ old('classroom_name') }}" placeholder="Örn: 12. Sınıf / YKS" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-2">İlgilendiği Program</label>
                <input type="text" name="interested_program" value="{{ old('interested_program') }}" placeholder="Örn: Sayısal VIP Kurs" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-2">Başvuru Kaynağı *</label>
                <select name="source" required class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm">
                    <option value="Instagram">Instagram</option>
                    <option value="Google">Google</option>
                    <option value="Referans">Referans</option>
                    <option value="Web">Web</option>
                    <option value="Telefon">Telefon</option>
                    <option value="Diğer" selected>Diğer</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-2">Durum *</label>
                <select name="status" required class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm">
                    <option value="Yeni" selected>Yeni</option>
                    <option value="Arandı">Arandı</option>
                    <option value="Randevu">Randevu</option>
                    <option value="Kayıt Oldu">Kayıt Oldu</option>
                    <option value="İptal">İptal</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-2">İlgilenen Personel / Temsilci</label>
                <select name="assigned_to" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm">
                    <option value="">Seçiniz</option>
                    @foreach($staffUsers as $staff)
                        <option value="{{ $staff->id }}" {{ old('assigned_to', auth()->id()) == $staff->id ? 'selected' : '' }}>{{ $staff->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-2">Hatırlatma / Takip Tarihi</label>
                <input type="datetime-local" name="reminder_at" value="{{ old('reminder_at') }}" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-2">Görüşme Notları</label>
            <textarea name="notes" rows="4" placeholder="Görüşme detayları, veli talepleri..." class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm">{{ old('notes') }}</textarea>
        </div>

        <div class="flex justify-end pt-4 border-t border-slate-100 dark:border-slate-800">
            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-sm transition-colors shadow-sm">
                Ön Kaydı Kaydet
            </button>
        </div>
    </form>
</div>
@endsection
