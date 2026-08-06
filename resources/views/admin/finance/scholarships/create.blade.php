@extends('layouts.admin')
@section('title', 'Yeni Burs Ekle')
@section('content')
    <x-admin.crud.form-layout title="Öğrenciye Burs Tanımla" description="Bir öğrenciye burs oranı tanımlayın." backRoute="{{ route('admin.scholarships.index') }}">
        <x-admin.form.layout action="{{ route('admin.scholarships.store') }}" method="POST">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Student -->
                <x-admin.form.field-group label="Öğrenci" id="student_id" :error="$errors->first('student_id')" required>
                    <select name="student_id" class="w-full text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors" required>
                        <option value="">Öğrenci Seçin...</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->first_name }} {{ $student->last_name }} ({{ $student->student_number }})
                            </option>
                        @endforeach
                    </select>
                </x-admin.form.field-group>

                <!-- Title -->
                <x-admin.form.field-group label="Burs Başlığı / Sebebi" id="title" :error="$errors->first('title')" required>
                    <input type="text" name="title" value="{{ old('title') }}" placeholder="Örn: Başarı Bursu" class="w-full text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors" required>
                </x-admin.form.field-group>

                <!-- Percentage -->
                <x-admin.form.field-group label="Burs Oranı (%)" id="percentage" :error="$errors->first('percentage')" required>
                    <input type="number" step="0.01" min="0" max="100" name="percentage" value="{{ old('percentage') }}" class="w-full text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors" required>
                </x-admin.form.field-group>
            </div>

            <!-- Active Status -->
            <div class="flex items-center p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700/50 mt-6">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="h-5 w-5 text-primary border-slate-300 rounded focus:ring-primary">
                <div class="ml-3">
                    <label for="is_active" class="block text-sm font-medium text-slate-900 dark:text-white">
                        Aktif Durum
                    </label>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Bu burs şu anda aktif olsun.</p>
                </div>
            </div>

            <div class="flex items-center justify-end pt-6 mt-6 border-t border-slate-100 dark:border-slate-800 space-x-3">
                <x-admin.button href="{{ route('admin.scholarships.index') }}" variant="secondary">
                    İptal
                </x-admin.button>
                <x-admin.button type="submit" variant="primary">
                    Kaydet
                </x-admin.button>
            </div>
        </x-admin.form.layout>
    </x-admin.crud.form-layout>
@endsection
