@extends('layouts.admin')
@section('title', 'Yeni Öğrenci Kaydı')
@section('content')
    <x-admin.crud.index-layout title="Yeni Öğrenci Oluştur" description="Yeni bir öğrencinin özlük bilgilerini, veli iletişimini ve akademik detaylarını sisteme kaydedin.">
        <x-slot name="actions">
            <x-admin.button href="{{ route('admin.students.index') }}" variant="secondary" icon="M10 19l-7-7m0 0l7-7m-7 7h18">
                Listeye Geri Dön
            </x-admin.button>
        </x-slot>

        @if(session('error'))
            <div class="mb-6 p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-700 rounded-r-lg shadow-sm">
                <div class="flex items-center gap-2 font-bold">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-700 rounded-r-lg shadow-sm">
                <div class="font-bold mb-1">Lütfen aşağıdaki hataları düzeltiniz:</div>
                <ul class="list-disc list-inside text-xs space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm" x-data="{ createUser: {{ old('create_user_account') ? 'true' : 'false' }}, createParent: {{ old('create_parent_account') ? 'true' : 'false' }} }">
            <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-6 border-b border-slate-100 dark:border-slate-800 pb-4">
                <i class="fas fa-user-graduate text-blue-500"></i>
                Öğrenci Bilgileri Formu
            </h3>
            
            <x-admin.form.layout :action="route('admin.students.store')" method="POST">
                
                <h4 class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-4 mt-6">Kişisel Bilgiler</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <x-admin.form.field-group label="Öğrenci No" id="student_number" required>
                        <input type="text" name="student_number" id="student_number" required value="{{ old('student_number', 'OGR-' . rand(1000, 9999)) }}" class="w-full bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-slate-900 dark:text-white font-mono">
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="TC Kimlik No (11 Hane)" id="identity_number">
                        <input type="text" name="identity_number" id="identity_number" maxlength="11" value="{{ old('identity_number') }}" placeholder="10000000000" class="w-full bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-slate-900 dark:text-white font-mono">
                    </x-admin.form.field-group>
                    
                    <x-admin.form.field-group label="Cinsiyet" id="gender" required>
                        <select name="gender" id="gender" required class="w-full bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-slate-900 dark:text-white">
                            <option value="">Cinsiyet Seçiniz</option>
                            <option value="Kadın" {{ old('gender') == 'Kadın' || old('gender') == 'Female' ? 'selected' : '' }}>Kadın</option>
                            <option value="Erkek" {{ old('gender') == 'Erkek' || old('gender') == 'Male' ? 'selected' : '' }}>Erkek</option>
                        </select>
                    </x-admin.form.field-group>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <x-admin.form.field-group label="Adı" id="first_name" required>
                        <input type="text" name="first_name" id="first_name" required value="{{ old('first_name') }}" class="w-full bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-slate-900 dark:text-white">
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Soyadı" id="last_name" required>
                        <input type="text" name="last_name" id="last_name" required value="{{ old('last_name') }}" class="w-full bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-slate-900 dark:text-white">
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Doğum Tarihi" id="birth_date">
                        <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date') }}" class="w-full bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-slate-900 dark:text-white">
                    </x-admin.form.field-group>
                </div>

                <!-- Sisteme Giriş Hesabı Toggle -->
                <div class="my-6">
                    <div class="flex items-center justify-between p-4 bg-blue-50/50 dark:bg-blue-950/30 rounded-xl border border-blue-100 dark:border-blue-900/50 mb-4">
                        <div>
                            <span class="text-sm font-bold text-blue-900 dark:text-blue-200">Sisteme Giriş Hesabı Oluştur</span>
                            <p class="text-xs text-blue-700/80 dark:text-blue-300/80">Açılırsa öğrenci e-posta ve şifresi ile Öğrenci Portalına giriş yapabilir.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="create_user_account" value="1" x-model="createUser" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <div x-show="createUser" x-transition class="grid grid-cols-1 md:grid-cols-3 gap-6 p-4 bg-blue-50/30 dark:bg-slate-800/40 rounded-xl border border-blue-100 dark:border-slate-700">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">E-Posta Adresi <span class="text-rose-500">*</span></label>
                            <input type="email" name="user_email" value="{{ old('user_email') }}" class="w-full bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="ogrenci@dershane.com">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Şifre <span class="text-rose-500">*</span></label>
                            <input type="password" name="user_password" class="w-full bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="••••••••">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Şifre Tekrar <span class="text-rose-500">*</span></label>
                            <input type="password" name="user_password_confirmation" class="w-full bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="••••••••">
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-100 dark:border-slate-800 my-6"></div>
                <h4 class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-4">Akademik Bilgiler</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <x-admin.form.field-group label="Sınıf Ataması" id="classroom_id">
                        <select name="classroom_id" id="classroom_id" class="w-full bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-slate-900 dark:text-white">
                            <option value="">Sonra Atanacak</option>
                            @foreach($classrooms as $c)
                                <option value="{{ $c->id }}" {{ old('classroom_id') == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->code }})</option>
                            @endforeach
                        </select>
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Durum" id="status" required>
                        <select name="status" id="status" required class="w-full bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-slate-900 dark:text-white">
                            <option value="Active" {{ old('status', 'Active') == 'Active' ? 'selected' : '' }}>Aktif</option>
                            <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Pasif</option>
                        </select>
                    </x-admin.form.field-group>
                </div>

                <div class="border-t border-slate-100 dark:border-slate-800 my-6"></div>
                <h4 class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-4 flex items-center gap-2">
                    <i class="fas fa-users text-slate-400"></i>
                    Veli & İletişim Bilgileri
                </h4>

                <!-- Veli Hesabı da Oluştur Toggle -->
                <div class="my-4">
                    <div class="flex items-center justify-between p-4 bg-emerald-50/50 dark:bg-emerald-950/30 rounded-xl border border-emerald-100 dark:border-emerald-900/50 mb-4">
                        <div>
                            <span class="text-sm font-bold text-emerald-900 dark:text-emerald-200">Veli Hesabı da Oluştur</span>
                            <p class="text-xs text-emerald-700/80 dark:text-emerald-300/80">Açılırsa veli için Veli Portalı giriş hesabı otomatik oluşturulur.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="create_parent_account" value="1" x-model="createParent" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div>

                    <div x-show="createParent" x-transition class="grid grid-cols-1 md:grid-cols-3 gap-6 p-4 bg-emerald-50/30 dark:bg-slate-800/40 rounded-xl border border-emerald-100 dark:border-slate-700 mb-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Veli E-Posta <span class="text-rose-500">*</span></label>
                            <input type="email" name="guardian_email" value="{{ old('guardian_email') }}" class="w-full bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="veli@dershane.com">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Veli Şifresi <span class="text-rose-500">*</span></label>
                            <input type="password" name="guardian_password" class="w-full bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="••••••••">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Veli Şifre Tekrar <span class="text-rose-500">*</span></label>
                            <input type="password" name="guardian_password_confirmation" class="w-full bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="••••••••">
                        </div>
                    </div>
                </div>
                
                <div class="p-5 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700/50 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Veli Ad Soyad</label>
                            <input type="text" name="guardian_name" value="{{ old('guardian_name') }}" placeholder="Ahmet Yılmaz" class="w-full bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Yakınlık (Örn: Baba)</label>
                            <input type="text" name="guardian_relation" value="{{ old('guardian_relation', 'Veli') }}" class="w-full bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Veli Telefon (TR)</label>
                            <input type="text" name="guardian_phone" id="guardian_phone" value="{{ old('guardian_phone') }}" placeholder="+90 (5XX) XXX XX XX" oninput="formatTrPhone(this)" class="w-full bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Öğrenci Telefon (TR)</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="+90 (5XX) XXX XX XX" oninput="formatTrPhone(this)" class="w-full bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm font-mono">
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Adres</label>
                        <textarea name="address_text" rows="2" placeholder="Açık adres bilgisi..." class="w-full bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">{{ old('address_text') }}</textarea>
                    </div>
                </div>

                <div class="pt-6 mt-6 flex justify-end border-t border-slate-100 dark:border-slate-800">
                    <x-admin.button type="submit" variant="primary" icon="M5 13l4 4L19 7">
                        Öğrenciyi Sisteme Kaydet
                    </x-admin.button>
                </div>
            </x-admin.form.layout>
        </div>
    </x-admin.crud.index-layout>

    <script>
    function formatTrPhone(input) {
        let value = input.value.replace(/\D/g, '');
        if (value.startsWith('90')) value = value.substring(2);
        if (value.startsWith('0')) value = value.substring(1);
        if (value.length > 10) value = value.substring(0, 10);

        let formatted = '';
        if (value.length > 0) {
            formatted = '+90 (' + value.substring(0, 3);
        }
        if (value.length >= 3) {
            formatted += ') ' + value.substring(3, 6);
        }
        if (value.length >= 6) {
            formatted += ' ' + value.substring(6, 8);
        }
        if (value.length >= 8) {
            formatted += ' ' + value.substring(8, 10);
        }
        input.value = formatted;
    }
    </script>
@endsection
