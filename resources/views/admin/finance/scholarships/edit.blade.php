@extends('layouts.admin')
@section('title', 'Burs Güncelle')
@section('content')
    <x-admin.crud.form-layout title="Burs Düzenle" description="Öğrenci bursunu güncelliyorsunuz." backRoute="{{ route('admin.scholarships.index') }}">
        <x-admin.form.layout action="{{ route('admin.scholarships.update', $scholarship->id) }}" method="POST">
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Student -->
                <x-admin.form.field-group label="Öğrenci" id="student_id" :error="$errors->first('student_id')" required>
                    <select name="student_id" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors" required>
                        <option value="">Öğrenci Seçin...</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id', $scholarship->student_id) == $student->id ? 'selected' : '' }}>
                                {{ $student->first_name }} {{ $student->last_name }} ({{ $student->student_number }})
                            </option>
                        @endforeach
                    </select>
                </x-admin.form.field-group>

                <!-- Title -->
                <x-admin.form.field-group label="Burs Başlığı / Sebebi" id="title" :error="$errors->first('title')" required>
                    <input type="text" name="title" value="{{ old('title', $scholarship->title) }}" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors" required>
                </x-admin.form.field-group>

                <!-- Percentage -->
                <x-admin.form.field-group label="Burs Oranı (%)" id="percentage" :error="$errors->first('percentage')" required>
                    <input type="number" step="0.01" min="0" max="100" name="percentage" value="{{ old('percentage', $scholarship->percentage) }}" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors" required>
                </x-admin.form.field-group>
            </div>

            <!-- Active Status -->
            <div class="flex items-center p-4 bg-neutral-50 dark:bg-neutral-800/50 rounded-xl border border-neutral-200 dark:border-neutral-700/50 mt-6">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $scholarship->is_active) ? 'checked' : '' }} class="h-5 w-5 text-primary border-neutral-300 rounded focus:ring-primary">
                <div class="ml-3">
                    <label for="is_active" class="block text-sm font-medium text-neutral-900 dark:text-white">
                        Aktif Durum
                    </label>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Bu burs şu anda aktif olsun.</p>
                </div>
            </div>

            <div class="flex items-center justify-end pt-6 mt-6 border-t border-neutral-100 dark:border-neutral-800 space-x-3">
                <x-admin.button href="{{ route('admin.scholarships.index') }}" variant="secondary">
                    İptal
                </x-admin.button>
                <x-admin.button type="submit" variant="primary">
                    Güncelle
                </x-admin.button>
            </div>
        </x-admin.form.layout>
    </x-admin.crud.form-layout>
@endsection
