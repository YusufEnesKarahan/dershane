@extends('layouts.admin')
@section('title', 'Profil Ayarları')
@section('content')
    <x-admin.crud.index-layout title="Profil Ayarları">
        <div x-data="{ tab: 'profile' }" class="space-y-6">
            <!-- Tabs Menu -->
            <div class="flex border-b border-neutral-200 dark:border-neutral-800">
                <button @click="tab = 'profile'" :class="tab === 'profile' ? 'border-primary text-primary' : 'border-transparent text-neutral-500 hover:text-neutral-900 dark:hover:text-white'" class="px-4 py-2 border-b-2 text-sm font-medium transition-colors">Kişisel Bilgiler</button>
                <button @click="tab = 'security'" :class="tab === 'security' ? 'border-primary text-primary' : 'border-transparent text-neutral-500 hover:text-neutral-900 dark:hover:text-white'" class="px-4 py-2 border-b-2 text-sm font-medium transition-colors ml-4">Güvenlik</button>
                <button @click="tab = 'preferences'" :class="tab === 'preferences' ? 'border-primary text-primary' : 'border-transparent text-neutral-500 hover:text-neutral-900 dark:hover:text-white'" class="px-4 py-2 border-b-2 text-sm font-medium transition-colors ml-4">Tercihler</button>
                <button @click="tab = 'avatar'" :class="tab === 'avatar' ? 'border-primary text-primary' : 'border-transparent text-neutral-500 hover:text-neutral-900 dark:hover:text-white'" class="px-4 py-2 border-b-2 text-sm font-medium transition-colors ml-4">Profil Resmi</button>
            </div>

            <!-- Tab Contents -->
            <div class="max-w-2xl bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
                <!-- Personal Info -->
                <div x-show="tab === 'profile'" x-transition>
                    <x-admin.form.layout :action="route('admin.profile.update')" method="POST">
                        <x-admin.form.field-group label="Tam Adı" id="name" :error="$errors->first('name')">
                            <input type="text" name="name" id="name" value="{{ $user->name }}" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                        </x-admin.form.field-group>

                        <x-admin.form.field-group label="E-Posta" id="email" :error="$errors->first('email')">
                            <input type="email" name="email" id="email" value="{{ $user->email }}" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                        </x-admin.form.field-group>

                        <x-admin.form.field-group label="Telefon" id="phone" :error="$errors->first('phone')">
                            <input type="text" name="phone" id="phone" value="{{ $user->phone }}" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                        </x-admin.form.field-group>

                        <button type="submit" class="px-4 py-2 bg-primary text-white text-sm font-medium rounded-xl hover:bg-primary-dark transition shadow-sm">Değişiklikleri Kaydet</button>
                    </x-admin.form.layout>
                </div>

                <!-- Security / Password Reset -->
                <div x-show="tab === 'security'" x-transition>
                    <x-admin.form.layout :action="route('admin.profile.password')" method="POST">
                        <x-admin.form.field-group label="Mevcut Şifre" id="current_password" :error="$errors->first('current_password')">
                            <input type="password" name="current_password" id="current_password" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                        </x-admin.form.field-group>

                        <x-admin.form.field-group label="Yeni Şifre" id="password" :error="$errors->first('password')">
                            <input type="password" name="password" id="password" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                        </x-admin.form.field-group>

                        <x-admin.form.field-group label="Yeni Şifre (Onayla)" id="password_confirmation" :error="$errors->first('password_confirmation')">
                            <input type="password" name="password_confirmation" id="password_confirmation" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                        </x-admin.form.field-group>

                        <button type="submit" class="px-4 py-2 bg-primary text-white text-sm font-medium rounded-xl hover:bg-primary-dark transition shadow-sm">Şifreyi Güncelle</button>
                    </x-admin.form.layout>
                </div>

                <!-- Preferences -->
                <div x-show="tab === 'preferences'" x-transition>
                    <x-admin.form.layout :action="route('admin.preferences.update')" method="POST">
                        <x-admin.form.field-group label="Tema Tercihi" id="theme" :error="$errors->first('theme')">
                            <select name="theme" id="theme" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                                <option value="light" {{ $user->preferredTheme() === 'light' ? 'selected' : '' }}>Açık Tema (Light)</option>
                                <option value="dark" {{ $user->preferredTheme() === 'dark' ? 'selected' : '' }}>Koyu Tema (Dark)</option>
                                <option value="auto" {{ $user->preferredTheme() === 'auto' ? 'selected' : '' }}>Sistem Teması (Auto)</option>
                            </select>
                        </x-admin.form.field-group>

                        <x-admin.form.field-group label="Dil" id="language" :error="$errors->first('language')">
                            <select name="language" id="language" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                                <option value="tr" {{ $user->preferredLanguage() === 'tr' ? 'selected' : '' }}>Türkçe</option>
                                <option value="en" {{ $user->preferredLanguage() === 'en' ? 'selected' : '' }}>English</option>
                            </select>
                        </x-admin.form.field-group>

                        <x-admin.form.field-group label="Zaman Dilimi" id="timezone" :error="$errors->first('timezone')">
                            <input type="text" name="timezone" id="timezone" value="{{ $user->preferredTimezone() }}" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                        </x-admin.form.field-group>

                        <button type="submit" class="px-4 py-2 bg-primary text-white text-sm font-medium rounded-xl hover:bg-primary-dark transition shadow-sm">Tercihleri Kaydet</button>
                    </x-admin.form.layout>
                </div>

                <!-- Avatar Upload -->
                <div x-show="tab === 'avatar'" x-transition>
                    <x-admin.form.layout :action="route('admin.profile.avatar')" method="POST" enctype="multipart/form-data">
                        <div class="flex items-center gap-6 mb-6">
                            <img src="{{ $user->getAvatarUrl() }}" class="w-24 h-24 rounded-full border border-neutral-200 dark:border-neutral-700">
                            <div>
                                <h4 class="text-sm font-medium text-neutral-900 dark:text-white">Profil Resmi</h4>
                                <p class="text-xs text-neutral-500 mt-1">PNG, JPG formatlarında maksimum 2MB dosya yükleyebilirsiniz.</p>
                            </div>
                        </div>

                        <x-admin.form.field-group label="Profil Resmi Seç" id="avatar" :error="$errors->first('avatar')">
                            <input type="file" name="avatar" id="avatar" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                        </x-admin.form.field-group>

                        <button type="submit" class="px-4 py-2 bg-primary text-white text-sm font-medium rounded-xl hover:bg-primary-dark transition shadow-sm">Resmi Yükle</button>
                    </x-admin.form.layout>
                </div>
            </div>
        </div>
    </x-admin.crud.index-layout>
@endsection
