@extends('layouts.admin')
@section('title', isset($classroom) ? 'Derslik Düzenle' : 'Yeni Derslik Ekle')
@section('content')
    <x-admin.crud.form-layout 
        title="{{ isset($classroom) ? 'Derslik Tanımını Düzenle' : 'Yeni Derslik Ekle' }}" 
        description="Fiziki sınıf kodlarını, kapasite ve renk tanımlarını güncelleyin."
        backRoute="{{ route('admin.classrooms.index') }}"
    >
        <x-admin.form.layout :action="isset($classroom) ? route('admin.classrooms.update', $classroom->id) : route('admin.classrooms.store')" method="POST">
            @if(isset($classroom))
                @method('PUT')
            @endif

            <div class="space-y-6 max-w-3xl">
                <div class="flex items-center gap-2 mb-6 border-b border-neutral-100 dark:border-neutral-800 pb-4">
                    <div class="p-2 bg-primary/10 rounded-lg">
                        <svg class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-neutral-900 dark:text-white">Derslik Bilgileri</h3>
                        <p class="text-xs text-neutral-500">Dersliğin temel tanımlarını belirleyin.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-admin.form.field-group label="Derslik Kodu (Benzersiz)" id="code" required>
                        <input type="text" name="code" id="code" required value="{{ $classroom->code ?? '' }}" {{ isset($classroom) ? 'disabled' : '' }} class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-neutral-900 dark:text-white transition-colors font-mono {{ isset($classroom) ? 'opacity-50 cursor-not-allowed bg-neutral-50 dark:bg-neutral-800' : '' }}">
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Derslik Adı" id="name" required>
                        <input type="text" name="name" id="name" required value="{{ $classroom->name ?? '' }}" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-neutral-900 dark:text-white transition-colors">
                    </x-admin.form.field-group>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <x-admin.form.field-group label="Bağlı Şube" id="branch_id">
                        <select name="branch_id" id="branch_id" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-neutral-900 dark:text-white transition-colors">
                            <option value="">Tüm Şubeler</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}" {{ (isset($classroom) && $classroom->branch_id === $b->id) ? 'selected' : '' }}>{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Derslik Tipi" id="classroom_type_id">
                        <select name="classroom_type_id" id="classroom_type_id" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-neutral-900 dark:text-white transition-colors">
                            <option value="">Standart Derslik</option>
                            @foreach($types as $t)
                                <option value="{{ $t->id }}" {{ (isset($classroom) && $classroom->classroom_type_id === $t->id) ? 'selected' : '' }}>{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Maksimum Kapasite" id="capacity" required>
                        <input type="number" name="capacity" id="capacity" required value="{{ $classroom->capacity ?? 30 }}" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-neutral-900 dark:text-white transition-colors">
                    </x-admin.form.field-group>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-admin.form.field-group label="Renk Kodu (Program Etiketi)" id="color_code">
                        <div class="flex items-center gap-3">
                            <input type="color" name="color_code" id="color_code" value="{{ $classroom->color_code ?? '#4F46E5' }}" class="w-12 h-10 p-1 bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm cursor-pointer transition-colors">
                            <span class="text-xs text-neutral-500 font-mono" id="color_code_display">{{ $classroom->color_code ?? '#4F46E5' }}</span>
                        </div>
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Aktiflik Durumu" id="is_active">
                        <select name="is_active" id="is_active" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-neutral-900 dark:text-white transition-colors">
                            <option value="1" {{ (!isset($classroom) || $classroom->is_active) ? 'selected' : '' }}>Aktif (Kullanılabilir)</option>
                            <option value="0" {{ (isset($classroom) && !$classroom->is_active) ? 'selected' : '' }}>Pasif (Bakımda)</option>
                        </select>
                    </x-admin.form.field-group>
                </div>

                <div class="pt-6 mt-6 border-t border-neutral-100 dark:border-neutral-800 flex items-center justify-end">
                    <x-admin.button type="submit" variant="primary" icon="M5 13l4 4L19 7">
                        {{ isset($classroom) ? 'Dersliği Güncelle' : 'Dersliği Kaydet' }}
                    </x-admin.button>
                </div>
            </div>
        </x-admin.form.layout>
    </x-admin.crud.form-layout>

    <script>
        document.getElementById('color_code')?.addEventListener('input', function(e) {
            document.getElementById('color_code_display').innerText = e.target.value.toUpperCase();
        });
    </script>
@endsection
