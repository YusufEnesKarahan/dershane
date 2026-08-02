@extends('layouts.admin')
@section('title', 'Yeni İndirim Ekle')
@section('content')
    <x-admin.crud.form-layout title="Yeni İndirim Kampanyası Ekle" description="Sisteme yeni bir indirim türü tanımlayın." backRoute="{{ route('admin.discounts.index') }}">
        <x-admin.form.layout action="{{ route('admin.discounts.store') }}" method="POST">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <x-admin.form.field-group label="İndirim Adı" id="name" :error="$errors->first('name')" required>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-neutral-900 dark:text-white transition-colors" required>
                </x-admin.form.field-group>

                <!-- Code -->
                <x-admin.form.field-group label="Kampanya Kodu" id="code" :error="$errors->first('code')" required help="Öğrenci kaydında kullanılacak benzersiz kod.">
                    <input type="text" name="code" id="code" value="{{ old('code') }}" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm font-mono text-neutral-900 dark:text-white transition-colors uppercase" required>
                </x-admin.form.field-group>

                <!-- Type -->
                <x-admin.form.field-group label="İndirim Türü" id="type" :error="$errors->first('type')" required>
                    <select name="type" id="type" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-neutral-900 dark:text-white transition-colors" required>
                        <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Yüzdelik (%)</option>
                        <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Sabit Tutar (TL)</option>
                    </select>
                </x-admin.form.field-group>

                <!-- Value -->
                <x-admin.form.field-group label="İndirim Değeri" id="value" :error="$errors->first('value')" required>
                    <input type="number" step="0.01" name="value" id="value" value="{{ old('value') }}" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-neutral-900 dark:text-white transition-colors" required>
                </x-admin.form.field-group>
            </div>

            <!-- Active Status -->
            <div class="flex items-center p-4 bg-neutral-50 dark:bg-neutral-800/50 rounded-xl border border-neutral-200 dark:border-neutral-700/50">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="h-5 w-5 text-primary border-neutral-300 rounded focus:ring-primary">
                <div class="ml-3">
                    <label for="is_active" class="block text-sm font-medium text-neutral-900 dark:text-white">
                        Aktif Durum
                    </label>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Bu indirim kampanyası şu anda yeni kayıtlarda kullanılabilir durumda olsun.</p>
                </div>
            </div>

            <div class="flex items-center justify-end pt-6 mt-6 border-t border-neutral-100 dark:border-neutral-800 space-x-3">
                <x-admin.button href="{{ route('admin.discounts.index') }}" variant="secondary">
                    İptal
                </x-admin.button>
                <x-admin.button type="submit" variant="primary" icon="M5 13l4 4L19 7">
                    İndirimi Kaydet
                </x-admin.button>
            </div>
            
        </x-admin.form.layout>
    </x-admin.crud.form-layout>
@endsection
