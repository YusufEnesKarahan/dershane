@extends('layouts.admin')
@section('title', 'Öğrenci Profilini Düzenle')
@section('content')
    <x-admin.crud.index-layout title="Profil Bilgilerini Güncelle" description="{{ $student->full_name }} isimli öğrencinin özlük ve iletişim bilgilerini düzenleyin.">
        <x-slot name="actions">
            <x-admin.button href="{{ route('admin.students.show', $student->id) }}" variant="secondary" icon="M10 19l-7-7m0 0l7-7m-7 7h18">
                Profile Geri Dön
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

        <div class="bg-white dark:bg-neutral-900 p-6 sm:p-8 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm">
            <h3 class="text-base font-bold text-neutral-900 dark:text-white flex items-center gap-2 mb-6 border-b border-neutral-100 dark:border-neutral-800 pb-4">
                <i class="fas fa-user-edit text-indigo-500"></i>
                Öğrenci Bilgileri Formu
            </h3>
            
            <x-admin.form.layout :action="route('admin.students.update', $student->id)" method="POST">
                @method('PUT')
                <h4 class="text-sm font-bold text-neutral-700 dark:text-neutral-300 mb-4 mt-6">Kişisel Bilgiler</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <x-admin.form.field-group label="Öğrenci No" id="student_number" required>
                        <input type="text" name="student_number" id="student_number" required value="{{ old('student_number', $student->student_number) }}" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-neutral-900 dark:text-white font-mono">
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="TC Kimlik No (11 Hane)" id="identity_number">
                        <input type="text" name="identity_number" id="identity_number" maxlength="11" value="{{ old('identity_number', $student->identity_number) }}" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-neutral-900 dark:text-white font-mono">
                    </x-admin.form.field-group>
                    
                    <x-admin.form.field-group label="Cinsiyet" id="gender" required>
                        <select name="gender" id="gender" required class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-neutral-900 dark:text-white">
                            <option value="">Cinsiyet Seçiniz</option>
                            <option value="Kadın" {{ old('gender', $student->gender) == 'Kadın' || old('gender', $student->gender) == 'Female' ? 'selected' : '' }}>Kadın</option>
                            <option value="Erkek" {{ old('gender', $student->gender) == 'Erkek' || old('gender', $student->gender) == 'Male' ? 'selected' : '' }}>Erkek</option>
                        </select>
                    </x-admin.form.field-group>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <x-admin.form.field-group label="Adı" id="first_name" required>
                        <input type="text" name="first_name" id="first_name" required value="{{ old('first_name', $student->first_name) }}" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-neutral-900 dark:text-white">
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Soyadı" id="last_name" required>
                        <input type="text" name="last_name" id="last_name" required value="{{ old('last_name', $student->last_name) }}" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-neutral-900 dark:text-white">
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Doğum Tarihi" id="birth_date">
                        <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date', $student->birth_date ? \Carbon\Carbon::parse($student->birth_date)->format('Y-m-d') : '') }}" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-neutral-900 dark:text-white">
                    </x-admin.form.field-group>
                </div>

                <div class="border-t border-neutral-100 dark:border-neutral-800 my-6"></div>
                <h4 class="text-sm font-bold text-neutral-700 dark:text-neutral-300 mb-4">Akademik Bilgiler</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <x-admin.form.field-group label="Sınıf Ataması" id="classroom_id">
                        <select name="classroom_id" id="classroom_id" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-neutral-900 dark:text-white">
                            <option value="">Sınıf Atanmadı</option>
                            @foreach($classrooms as $c)
                                <option value="{{ $c->id }}" {{ old('classroom_id', $student->classroom_id) == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->code }})</option>
                            @endforeach
                        </select>
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Durum" id="status" required>
                        <select name="status" id="status" required class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-neutral-900 dark:text-white">
                            <option value="Active" {{ old('status', $student->status) == 'Active' ? 'selected' : '' }}>Aktif</option>
                            <option value="Inactive" {{ old('status', $student->status) == 'Inactive' ? 'selected' : '' }}>Pasif</option>
                            <option value="Graduated" {{ old('status', $student->status) == 'Graduated' ? 'selected' : '' }}>Mezun</option>
                            <option value="Suspended" {{ old('status', $student->status) == 'Suspended' ? 'selected' : '' }}>Ayrıldı / Donduruldu</option>
                        </select>
                    </x-admin.form.field-group>
                </div>

                <div class="border-t border-neutral-100 dark:border-neutral-800 my-6"></div>
                <h4 class="text-sm font-bold text-neutral-700 dark:text-neutral-300 mb-4 flex items-center gap-2">
                    <i class="fas fa-users text-neutral-400"></i>
                    Veli & İletişim Bilgileri
                </h4>
                
                <div class="p-5 bg-neutral-50 dark:bg-neutral-800/50 rounded-xl border border-neutral-200 dark:border-neutral-700/50 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">Veli Ad Soyad</label>
                            <input type="text" name="guardian_name" value="{{ old('guardian_name', $student->primaryGuardian?->guardian_name) }}" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">Yakınlık (Örn: Baba)</label>
                            <input type="text" name="guardian_relation" value="{{ old('guardian_relation', $student->primaryGuardian?->relation) }}" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">Veli Telefon (TR)</label>
                            <input type="text" name="guardian_phone" id="guardian_phone" value="{{ old('guardian_phone', $student->primaryGuardian?->phone) }}" placeholder="+90 (5XX) XXX XX XX" oninput="formatTrPhone(this)" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">Öğrenci Telefon (TR)</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $student->contact?->phone) }}" placeholder="+90 (5XX) XXX XX XX" oninput="formatTrPhone(this)" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm font-mono">
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">Adres</label>
                        <textarea name="address_text" rows="2" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">{{ old('address_text', $student->address?->address_text) }}</textarea>
                    </div>
                </div>

                <div class="pt-6 mt-6 flex justify-end border-t border-neutral-100 dark:border-neutral-800">
                    <x-admin.button type="submit" variant="primary" icon="M5 13l4 4L19 7">
                        Profil Bilgilerini Kaydet
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
