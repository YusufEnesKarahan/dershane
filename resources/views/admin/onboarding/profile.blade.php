@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-onboarding.stepper :currentStep="1" :progress="$progress" />

    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
        <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-1">Adım 1: Kurum Bilgileri</h3>
        <p class="text-xs text-slate-500 dark:text-slate-400 mb-6">Kurumunuzun resmi adı, iletişim ve adres detaylarını tanımlayın.</p>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-xl text-emerald-800 dark:text-emerald-300 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.onboarding.saveProfile') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Kurum Resmi Adı</label>
                    <input type="text" name="institution_name" required value="{{ old('institution_name', $settings->institution_name) }}" placeholder="Örn: Final Eğitim Kurumları Kadıköy Şubesi" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Telefon Numarası</label>
                    <input type="text" name="phone" required value="{{ old('phone', $settings->phone) }}" placeholder="0212 555 00 00" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">E-posta Adresi</label>
                    <input type="email" name="email" required value="{{ old('email', $settings->email) }}" placeholder="iletisim@kurum.com" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Şehir / İl</label>
                    <input type="text" name="city" value="{{ old('city', $settings->city) }}" placeholder="İstanbul" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">İlçe</label>
                    <input type="text" name="district" value="{{ old('district', $settings->district) }}" placeholder="Kadıköy" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Web Sitesi (Opsiyonel)</label>
                    <input type="url" name="website" value="{{ old('website', $settings->website) }}" placeholder="https://www.kurum.com" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Açık Adres</label>
                    <textarea name="address" rows="3" required class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-sm focus:ring-blue-500 focus:border-blue-500">{{ old('address', $settings->address) }}</textarea>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-slate-100 dark:border-slate-800">
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition-all shadow-sm">
                    Kaydet ve Devam Et →
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
