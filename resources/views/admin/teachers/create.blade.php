@extends('layouts.admin')

@section('title', 'Yeni Öğretmen Ekle')

@section('content')
<div class="p-6 h-full flex flex-col">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white flex items-center">
                <a href="{{ route('admin.teachers.index') }}" class="mr-3 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                Yeni Öğretmen Ekle
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 ml-9">Sisteme yeni bir öğretmen kaydedin.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4">
            <x-alert type="success" dismissible="true">
                {{ session('success') }}
            </x-alert>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4">
            <x-alert type="danger" dismissible="true">
                {{ session('error') }}
            </x-alert>
        </div>
    @endif

    <!-- Form -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 flex-1 overflow-hidden">
        <form action="{{ route('admin.teachers.store') }}" method="POST" class="h-full flex flex-col">
            @csrf
            
            <div class="flex-1 overflow-y-auto p-6 space-y-8">
                <!-- Kimlik Bilgileri -->
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-6 border border-slate-200 dark:border-slate-700">
                    <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Kimlik ve İletişim Bilgileri
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Ad <span class="text-rose-500">*</span></label>
                            <input type="text" name="first_name" id="first_name" required value="{{ old('first_name') }}"
                                class="form-input w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                            @error('first_name') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="last_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Soyad <span class="text-rose-500">*</span></label>
                            <input type="text" name="last_name" id="last_name" required value="{{ old('last_name') }}"
                                class="form-input w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                            @error('last_name') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">E-Posta Adresi <span class="text-rose-500">*</span></label>
                            <input type="email" name="email" id="email" required value="{{ old('email') }}"
                                class="form-input w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                                placeholder="ornek@mail.com">
                            <p class="mt-1 text-xs text-slate-500">Kullanıcı sisteme bu e-posta adresiyle giriş yapacaktır.</p>
                            @error('email') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Telefon Numarası</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                                class="form-input w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                                placeholder="05XX XXX XX XX">
                            @error('phone') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="birth_date" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Doğum Tarihi</label>
                            <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date') }}"
                                class="form-input w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                            @error('birth_date') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="gender" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Cinsiyet</label>
                            <select name="gender" id="gender" class="form-select w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Seçiniz...</option>
                                <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Erkek</option>
                                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Kadın</option>
                                <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Belirtmek İstemiyor</option>
                            </select>
                            @error('gender') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Profesyonel Bilgiler -->
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-6 border border-slate-200 dark:border-slate-700">
                    <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Profesyonel Bilgiler
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="title" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Branş / Unvan <span class="text-rose-500">*</span></label>
                            <input type="text" name="title" id="title" required value="{{ old('title') }}"
                                class="form-input w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                                placeholder="Matematik Öğretmeni">
                            @error('title') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="specialties" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Uzmanlık Alanları</label>
                            <input type="text" name="specialties" id="specialties" value="{{ old('specialties') }}"
                                class="form-input w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                                placeholder="LGS Matematik, YKS Geometri">
                            @error('specialties') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="education" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Mezuniyet Bilgisi</label>
                            <input type="text" name="education" id="education" value="{{ old('education') }}"
                                class="form-input w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                                placeholder="Boğaziçi Üniversitesi Matematik Öğretmenliği">
                            @error('education') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="experience_years" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Deneyim (Yıl)</label>
                            <input type="number" name="experience_years" id="experience_years" min="0" max="50" value="{{ old('experience_years') }}"
                                class="form-input w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                            @error('experience_years') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Sistem Ayarları -->
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-6 border border-slate-200 dark:border-slate-700">
                    <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Sistem Ayarları
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="status" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Durum</label>
                            <select name="status" id="status" class="form-select w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                                <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Aktif</option>
                                <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Pasif</option>
                            </select>
                            @error('status') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Hesap Şifresi</label>
                            <input type="text" name="password" id="password" value="{{ old('password') }}"
                                class="form-input w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                                placeholder="Sistem otomatik oluşturur (Boş bırakılabilir)">
                            <p class="mt-1 text-xs text-slate-500">Boş bırakılırsa sistem rastgele güvenli bir şifre belirler.</p>
                            @error('password') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-700 flex items-center justify-end space-x-3">
                <a href="{{ route('admin.teachers.index') }}" class="btn-secondary px-6">İptal</a>
                <button type="submit" class="btn-primary px-8">Kaydet</button>
            </div>
        </form>
    </div>
</div>
@endsection
