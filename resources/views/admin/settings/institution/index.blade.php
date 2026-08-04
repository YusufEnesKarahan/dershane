@extends('layouts.admin')

@section('title', 'Kurum Ayarları')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ activeTab: '{{ request()->get('tab', 'general') }}' }">
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-widest bg-indigo-50 text-indigo-700 rounded-full border border-indigo-200">Merkezi Yapılandırma</span>
            <h1 class="text-2xl font-bold text-slate-900 mt-1">Kurum Sistem Ayarları</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kurum kimliği, marka görselleri, bölgesel tercihler ve bildirim ayarlarını yönetin.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-800 text-sm flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-2xl text-rose-800 text-sm space-y-1">
            <span class="font-bold block mb-1">Lütfen aşağıdaki hataları düzeltin:</span>
            <ul class="list-disc list-inside space-y-0.5 text-xs">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Tab Buttons Navigation -->
    <div class="bg-slate-100 p-1.5 rounded-2xl mb-8 flex flex-wrap gap-1 border border-slate-200/80">
        <button @click="activeTab = 'general'" :class="{ 'bg-white text-slate-900 shadow-sm font-bold': activeTab === 'general', 'text-slate-600 hover:text-slate-900 font-medium': activeTab !== 'general' }" class="px-5 py-2.5 rounded-xl text-xs transition-all">
            🏛️ Genel Bilgiler
        </button>
        <button @click="activeTab = 'branding'" :class="{ 'bg-white text-slate-900 shadow-sm font-bold': activeTab === 'branding', 'text-slate-600 hover:text-slate-900 font-medium': activeTab !== 'branding' }" class="px-5 py-2.5 rounded-xl text-xs transition-all">
            🎨 Marka & Görsel
        </button>
        <button @click="activeTab = 'regional'" :class="{ 'bg-white text-slate-900 shadow-sm font-bold': activeTab === 'regional', 'text-slate-600 hover:text-slate-900 font-medium': activeTab !== 'regional' }" class="px-5 py-2.5 rounded-xl text-xs transition-all">
            🌍 Bölge & Dil
        </button>
        <button @click="activeTab = 'notifications'" :class="{ 'bg-white text-slate-900 shadow-sm font-bold': activeTab === 'notifications', 'text-slate-600 hover:text-slate-900 font-medium': activeTab !== 'notifications' }" class="px-5 py-2.5 rounded-xl text-xs transition-all">
            🔔 Bildirim Tercihleri
        </button>
    </div>

    <!-- TAB 1: Genel Bilgiler -->
    <div x-show="activeTab === 'general'" x-cloak class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <h2 class="text-lg font-bold text-slate-900 mb-1">Genel Kurum Bilgileri & İletişim</h2>
        <p class="text-xs text-slate-500 mb-6">Kurumunuzun resmi adı, vergi numarası ve iletişim adreslerini güncelleyin.</p>

        <form action="{{ route('admin.settings.institution.updateGeneral') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Kurum Resmi Adı</label>
                    <input type="text" name="institution_name" required value="{{ old('institution_name', $settings->institution_name) }}" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Kurum Açıklaması / Tanıtım</label>
                    <textarea name="description" rows="3" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Kurumunuz hakkında kısa açıklama">{{ old('description', $settings->description) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Telefon Numarası</label>
                    <input type="text" name="phone" required value="{{ old('phone', $settings->phone) }}" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">E-posta Adresi</label>
                    <input type="email" name="email" required value="{{ old('email', $settings->email) }}" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Şehir / İl</label>
                    <input type="text" name="city" value="{{ old('city', $settings->city) }}" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">İlçe</label>
                    <input type="text" name="district" value="{{ old('district', $settings->district) }}" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Web Sitesi</label>
                    <input type="url" name="website" value="{{ old('website', $settings->website) }}" placeholder="https://www.kurum.com" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Açık Adres</label>
                    <textarea name="address" rows="3" required class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('address', $settings->address) }}</textarea>
                </div>
            </div>

            <!-- Fatura Bilgileri Alt Grubu -->
            <div class="pt-6 border-t border-slate-100">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4">Vergi & Fatura Bilgileri</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Vergi Numarası / T.C.</label>
                        <input type="text" name="tax_number" value="{{ old('tax_number', $settings->tax_number) }}" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Fatura Ünvanı</label>
                        <input type="text" name="invoice_title" value="{{ old('invoice_title', $settings->invoice_information['title'] ?? '') }}" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Vergi Dairesi</label>
                        <input type="text" name="invoice_tax_office" value="{{ old('invoice_tax_office', $settings->invoice_information['tax_office'] ?? '') }}" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-slate-100">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl transition-all shadow-sm">
                    Genel Bilgileri Kaydet
                </button>
            </div>
        </form>
    </div>

    <!-- TAB 2: Marka Ayarları -->
    <div x-show="activeTab === 'branding'" x-cloak class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <h2 class="text-lg font-bold text-slate-900 mb-1">Marka & Görsel Özelleştirme</h2>
        <p class="text-xs text-slate-500 mb-6">Kurum logonuz, tarayıcı ikonu (favicon) ve portal tema renklerini belirleyin.</p>

        <form action="{{ route('admin.settings.institution.updateBranding') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Logo Upload -->
                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200">
                    <label class="block text-sm font-bold text-slate-800 mb-2">Kurum Logosu</label>
                    <p class="text-xs text-slate-500 mb-4">PNG, JPG, SVG veya WEBP formatında maksimum 2MB dosya yükleyebilirsiniz.</p>
                    
                    @if($settings->logo)
                        <div class="mb-4 p-3 bg-white rounded-xl border border-slate-200 inline-block">
                            <img src="{{ asset('storage/' . $settings->logo) }}" alt="Kurum Logosu" class="h-16 object-contain">
                        </div>
                    @endif

                    <input type="file" name="logo" accept="image/jpeg,image/png,image/svg+xml,image/webp" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>

                <!-- Favicon Upload -->
                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200">
                    <label class="block text-sm font-bold text-slate-800 mb-2">Tarayıcı İkonu (Favicon)</label>
                    <p class="text-xs text-slate-500 mb-4">PNG, ICO veya SVG formatında maksimum 2MB dosya yükleyebilirsiniz.</p>
                    
                    @if($settings->favicon)
                        <div class="mb-4 p-3 bg-white rounded-xl border border-slate-200 inline-block">
                            <img src="{{ asset('storage/' . $settings->favicon) }}" alt="Favicon" class="w-8 h-8 object-contain">
                        </div>
                    @endif

                    <input type="file" name="favicon" accept="image/x-icon,image/png,image/svg+xml,image/vnd.microsoft.icon" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>

                <!-- Colors -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Ana Tema Rengi (Primary Color)</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="primary_color" value="{{ old('primary_color', $settings->primary_color ?? '#4f46e5') }}" class="w-12 h-10 rounded-lg border-0 cursor-pointer">
                        <input type="text" value="{{ old('primary_color', $settings->primary_color ?? '#4f46e5') }}" class="rounded-xl border-slate-200 text-sm font-mono focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Yardımcı Tema Rengi (Secondary Color)</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="secondary_color" value="{{ old('secondary_color', $settings->secondary_color ?? '#0f172a') }}" class="w-12 h-10 rounded-lg border-0 cursor-pointer">
                        <input type="text" value="{{ old('secondary_color', $settings->secondary_color ?? '#0f172a') }}" class="rounded-xl border-slate-200 text-sm font-mono focus:ring-indigo-500">
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-slate-100">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl transition-all shadow-sm">
                    Görsel Ayarları Kaydet
                </button>
            </div>
        </form>
    </div>

    <!-- TAB 3: Bölge Ayarları -->
    <div x-show="activeTab === 'regional'" x-cloak class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <h2 class="text-lg font-bold text-slate-900 mb-1">Bölge & Dil Tercihleri</h2>
        <p class="text-xs text-slate-500 mb-6">Sistem zaman dilimi ve varsayılan panel dilinizi konfigüre edin.</p>

        <form action="{{ route('admin.settings.institution.updateRegional') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Sistem Dili</label>
                    <select name="language" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500">
                        <option value="tr" {{ old('language', $settings->language) === 'tr' ? 'selected' : '' }}>Türkçe (TR)</option>
                        <option value="en" {{ old('language', $settings->language) === 'en' ? 'selected' : '' }}>English (US)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Saat Dilimi (Timezone)</label>
                    <select name="timezone" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500">
                        <option value="Europe/Istanbul" {{ old('timezone', $settings->timezone) === 'Europe/Istanbul' ? 'selected' : '' }}>Europe / Istanbul (GMT+3)</option>
                        <option value="Europe/London" {{ old('timezone', $settings->timezone) === 'Europe/London' ? 'selected' : '' }}>Europe / London (GMT+0)</option>
                        <option value="UTC" {{ old('timezone', $settings->timezone) === 'UTC' ? 'selected' : '' }}>UTC (Coordinated Universal Time)</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-slate-100">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl transition-all shadow-sm">
                    Bölgesel Ayarları Kaydet
                </button>
            </div>
        </form>
    </div>

    <!-- TAB 4: Bildirim Ayarları -->
    <div x-show="activeTab === 'notifications'" x-cloak class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <h2 class="text-lg font-bold text-slate-900 mb-1">Bildirim Tercihleri</h2>
        <p class="text-xs text-slate-500 mb-6">E-posta, sistem içi ve veli bilgilendirme servislerini aktifleştirin veya kapatın.</p>

        <form action="{{ route('admin.settings.institution.updateNotifications') }}" method="POST" class="space-y-6">
            @csrf

            @php
                $prefs = $settings->notification_preferences ?? ['email_notifications' => true, 'system_notifications' => true, 'parent_notifications' => true];
            @endphp

            <div class="space-y-4">
                <label class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-200/80 cursor-pointer">
                    <div>
                        <span class="font-bold text-slate-800 text-sm block">E-posta Bildirimleri</span>
                        <span class="text-xs text-slate-500">Kritik duyurular ve raporlar kurum e-postasına gönderilsin.</span>
                    </div>
                    <input type="checkbox" name="email_notifications" value="1" {{ !empty($prefs['email_notifications']) ? 'checked' : '' }} class="w-5 h-5 rounded text-indigo-600 focus:ring-indigo-500">
                </label>

                <label class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-200/80 cursor-pointer">
                    <div>
                        <span class="font-bold text-slate-800 text-sm block">Sistem İçi Bildirimler</span>
                        <span class="text-xs text-slate-500">Yönetim panelinde anlık sistem bildirim merkezi aktif olsun.</span>
                    </div>
                    <input type="checkbox" name="system_notifications" value="1" {{ !empty($prefs['system_notifications']) ? 'checked' : '' }} class="w-5 h-5 rounded text-indigo-600 focus:ring-indigo-500">
                </label>

                <label class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-200/80 cursor-pointer">
                    <div>
                        <span class="font-bold text-slate-800 text-sm block">Veli Portalı Bildirimleri</span>
                        <span class="text-xs text-slate-500">Devamsızlık ve sınav sonuçları veli portalına otomatik iletilsin.</span>
                    </div>
                    <input type="checkbox" name="parent_notifications" value="1" {{ !empty($prefs['parent_notifications']) ? 'checked' : '' }} class="w-5 h-5 rounded text-indigo-600 focus:ring-indigo-500">
                </label>
            </div>

            <div class="flex justify-end pt-4 border-t border-slate-100">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl transition-all shadow-sm">
                    Bildirim Tercihlerini Kaydet
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
