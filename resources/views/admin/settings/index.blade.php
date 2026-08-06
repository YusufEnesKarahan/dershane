@extends('layouts.admin')
@section('title', 'Sistem Ayarları')
@section('content')
    <div class="space-y-6" x-data="{ 
        activeTab: 'brand',
        // Theme live variables
        primaryColor: '#4f46e5',
        secondaryColor: '#06b6d4',
        accentColor: '#f59e0b',
        backgroundColor: '#f8fafc',
        borderRadius: '12px',
        spacing: '16px',
        typography: 'Instrument Sans, sans-serif',
        
        testSmtp() {
            fetch('{{ route('admin.settings.test-mail') }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => alert(data.message))
            .catch(() => alert('Bağlantı hatası.'));
        },
        testStorage() {
            fetch('{{ route('admin.settings.test-storage') }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => alert(data.message))
            .catch(() => alert('Doğrulama hatası.'));
        }
    }">
        <x-admin.crud.index-layout title="Sistem & White Label Yönetimi" description="Kurumsal kimlik, beyaz etiket (white label) ve SMTP/Sistem ayarlarını dinamik olarak yönetin.">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                
                <!-- Sol Panel: Tab Butonları -->
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm space-y-1">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 px-2">Kategoriler</h3>
                    @foreach($groups as $group)
                        <button type="button" @click="activeTab = '{{ $group->slug }}'" :class="activeTab === '{{ $group->slug }}' ? 'bg-primary text-white' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/40'" class="w-full flex items-center gap-2 p-2.5 rounded-xl text-xs font-semibold transition text-left">
                            <span>⚙️</span> {{ $group->name }}
                        </button>
                    @endforeach
                    <button type="button" @click="activeTab = 'backup'" :class="activeTab === 'backup' ? 'bg-primary text-white' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/40'" class="w-full flex items-center gap-2 p-2.5 rounded-xl text-xs font-semibold transition text-left">
                        <span>💾</span> Yedekleme / Aktar
                    </button>
                </div>

                <!-- Orta Panel: Ayar Alanları -->
                <div class="lg:col-span-2 space-y-6 bg-white dark:bg-slate-900 p-8 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm">
                    <x-admin.form.layout :action="route('admin.settings.update')" method="POST">
                        @foreach($groups as $group)
                            <div x-show="activeTab === '{{ $group->slug }}'" class="space-y-6">
                                <div class="border-b border-slate-100 dark:border-slate-800 pb-3 mb-6">
                                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ $group->name }}</h3>
                                    <p class="text-xs text-slate-500 mt-1">{{ $group->description }}</p>
                                </div>

                                @foreach($group->settings as $setting)
                                    <x-admin.form.field-group label="{{ str_replace('_', ' ', str_replace($group->slug.'.', '', $setting->key)) }}" id="{{ $setting->key }}">
                                        @if($setting->type === 'boolean')
                                            <select name="settings[{{ $setting->key }}]" class="w-full text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-slate-800 dark:text-slate-200">
                                                <option value="1" {{ $setting->value === '1' ? 'selected' : '' }}>Aktif (Evet)</option>
                                                <option value="0" {{ $setting->value === '0' || !$setting->value ? 'selected' : '' }}>Pasif (Hayır)</option>
                                            </select>
                                        @elseif($setting->type === 'file')
                                            <x-admin.media-picker name="settings[{{ $setting->key }}]" value="{{ $setting->value }}" />
                                        @elseif($setting->type === 'textarea')
                                            <textarea name="settings[{{ $setting->key }}]" class="w-full text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-slate-800 dark:text-slate-200 h-24">{{ $setting->value }}</textarea>
                                        @else
                                            <!-- Theme custom color live properties tracking variables -->
                                            @if($setting->key === 'theme.primary_color')
                                                <input type="color" name="settings[{{ $setting->key }}]" x-model="primaryColor" class="w-full h-10 border border-slate-200 dark:border-slate-700 rounded-lg cursor-pointer">
                                            @elseif($setting->key === 'theme.secondary_color')
                                                <input type="color" name="settings[{{ $setting->key }}]" x-model="secondaryColor" class="w-full h-10 border border-slate-200 dark:border-slate-700 rounded-lg cursor-pointer">
                                            @elseif($setting->key === 'theme.accent_color')
                                                <input type="color" name="settings[{{ $setting->key }}]" x-model="accentColor" class="w-full h-10 border border-slate-200 dark:border-slate-700 rounded-lg cursor-pointer">
                                            @elseif($setting->key === 'theme.background_color')
                                                <input type="color" name="settings[{{ $setting->key }}]" x-model="backgroundColor" class="w-full h-10 border border-slate-200 dark:border-slate-700 rounded-lg cursor-pointer">
                                            @else
                                                <input type="text" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}" class="w-full text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-slate-800 dark:text-slate-200">
                                            @endif
                                        @endif
                                    </x-admin.form.field-group>
                                @endforeach

                                @if($group->slug === 'mail')
                                    <x-admin.button type="button" variant="secondary" @click="testSmtp()" class="mt-4">
                                        SMTP Bağlantısını Test Et
                                    </x-admin.button>
                                @endif

                                @if($group->slug === 'storage')
                                    <x-admin.button type="button" variant="secondary" @click="testStorage()" class="mt-4">
                                        Disk Ayarlarını Doğrula
                                    </x-admin.button>
                                @endif

                                <div class="pt-6 border-t border-slate-100 dark:border-slate-800">
                                    <x-admin.button type="submit" variant="primary">
                                        Ayarları Kaydet
                                    </x-admin.button>
                                </div>
                            </div>
                        @endforeach
                    </x-admin.form.layout>

                    <!-- Backup / restore Panel -->
                    <div x-show="activeTab === 'backup'" class="space-y-6">
                        <div class="border-b border-slate-100 dark:border-slate-800 pb-3 mb-6">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white">Yedekleme & Dışa Aktar</h3>
                            <p class="text-xs text-slate-500 mt-1">Sistem ayarlarınızı JSON formatında yedekleyin veya geri yükleyin.</p>
                        </div>

                        <div class="p-4 bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-800 rounded-xl space-y-4">
                            <h4 class="text-xs font-bold text-slate-900 dark:text-white">Dışa Aktar</h4>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.settings.export') }}" class="px-4 py-2 bg-primary text-white text-xs font-semibold rounded-xl hover:bg-primary-dark transition shadow-sm">
                                    Standart JSON İndir
                                </a>
                                <a href="{{ route('admin.settings.export', ['encrypt' => 1]) }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-950 text-white text-xs font-semibold rounded-xl transition">
                                    Encrypted JSON İndir
                                </a>
                            </div>
                        </div>

                        <div class="p-4 bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-800 rounded-xl space-y-4">
                            <h4 class="text-xs font-bold text-slate-900 dark:text-white">Geri Yükle / İçe Aktar</h4>
                            <form method="POST" action="{{ route('admin.settings.import') }}" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                <input type="file" name="backup_file" required class="block w-full text-xs text-slate-500">
                                <label class="flex items-center gap-2 text-xs text-slate-700 dark:text-slate-300">
                                    <input type="checkbox" name="is_encrypted" value="1" class="rounded border-slate-300">
                                    <span>Yüklenen dosya şifrelenmiş (Encrypted JSON) yedek dosyasıdır</span>
                                </label>
                                <button type="submit" class="px-4 py-2 bg-green-500 text-white text-xs font-semibold rounded-xl hover:bg-green-600 transition shadow-sm">
                                    Yedeği Yükle
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Sağ Panel: Live CSS Preview Panel Drawer -->
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm space-y-6">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Canlı Tema Önizleme</h3>
                    
                    <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 space-y-4" :style="`background-color: ${backgroundColor}; border-radius: ${borderRadius}; padding: ${spacing};`">
                        <!-- Navbar preview -->
                        <div class="p-3 text-white text-xs font-bold rounded-lg flex items-center justify-between shadow-sm" :style="`background-color: ${primaryColor};`">
                            <span>Dershane Logo</span>
                            <span>Menü</span>
                        </div>
                        
                        <!-- Card preview -->
                        <div class="p-4 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 space-y-2">
                            <h4 class="text-xs font-bold" :style="`font-family: ${typography};`">Örnek Widget Kartı</h4>
                            <p class="text-[10px] text-slate-400">Tema renginiz ve yazı tipiniz bu kartta anında simüle edilir.</p>
                            <button type="button" class="px-3 py-1.5 text-white text-[10px] font-semibold rounded-xl transition" :style="`background-color: ${secondaryColor};`">
                                İkincil Buton
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </x-admin.crud.index-layout>
    </div>
@endsection
