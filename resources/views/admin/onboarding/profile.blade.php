@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-onboarding.stepper :currentStep="1" :progress="$progress" />

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h3 class="text-lg font-bold text-slate-900 mb-1">Adım 1: Kurum Bilgileri</h3>
        <p class="text-xs text-slate-500 mb-6">Kurumunuzun resmi adı, iletişim ve adres detaylarını tanımlayın.</p>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.onboarding.saveProfile') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Kurum Resmi Adı</label>
                    <input type="text" name="institution_name" required value="{{ old('institution_name', $settings->institution_name) }}" placeholder="Örn: Final Eğitim Kurumları Kadıköy Şubesi" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Telefon Numarası</label>
                    <input type="text" name="phone" required value="{{ old('phone', $settings->phone) }}" placeholder="0212 555 00 00" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">E-posta Adresi</label>
                    <input type="email" name="email" required value="{{ old('email', $settings->email) }}" placeholder="iletisim@kurum.com" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Şehir / İl</label>
                    <input type="text" name="city" value="{{ old('city', $settings->city) }}" placeholder="İstanbul" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">İlçe</label>
                    <input type="text" name="district" value="{{ old('district', $settings->district) }}" placeholder="Kadıköy" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Web Sitesi (Opsiyonel)</label>
                    <input type="url" name="website" value="{{ old('website', $settings->website) }}" placeholder="https://www.kurum.com" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Açık Adres</label>
                    <textarea name="address" rows="3" required class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('address', $settings->address) }}</textarea>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-slate-100">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl transition-all shadow-sm">
                    Kaydet ve Devam Et →
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
