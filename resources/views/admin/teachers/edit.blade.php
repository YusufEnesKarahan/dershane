@extends('layouts.admin')
@section('title', 'Öğretmen Düzenle')
@section('content')
    <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm max-w-xl mx-auto space-y-6">
        <div>
            <h1 class="text-lg font-bold text-neutral-900 dark:text-white">Öğretmen Profilini Düzenle</h1>
            <p class="text-xs text-neutral-500 mt-1">Öğretmenin ünvan, biyografi ve tecrübe bilgilerini güncelleyin.</p>
        </div>

        <x-admin.form.layout :action="route('admin.teachers.update', $teacher->id)" method="PUT">
            
            <x-admin.form.field-group label="Ünvan / Pozisyon" id="title">
                <input type="text" name="title" required value="{{ $teacher->title }}" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
            </x-admin.form.field-group>

            <x-admin.form.field-group label="Uzmanlık Alanları" id="specialties">
                <input type="text" name="specialties" required value="{{ $teacher->specialties }}" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
            </x-admin.form.field-group>

            <x-admin.form.field-group label="Biyografi / Detay" id="bio">
                <textarea name="bio" rows="4" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">{{ $teacher->bio }}</textarea>
            </x-admin.form.field-group>

            <x-admin.form.field-group label="Mezuniyet" id="education">
                <input type="text" name="education" value="{{ $teacher->education }}" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
            </x-admin.form.field-group>

            <x-admin.form.field-group label="Deneyim Yılı" id="experience_years">
                <input type="number" name="experience_years" value="{{ $teacher->experience_years }}" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
            </x-admin.form.field-group>

            <x-admin.form.field-group label="Durum" id="status">
                <select name="status" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                    <option value="Active" {{ $teacher->status === 'Active' ? 'selected' : '' }}>Aktif</option>
                    <option value="Inactive" {{ $teacher->status === 'Inactive' ? 'selected' : '' }}>Pasif</option>
                </select>
            </x-admin.form.field-group>

            <div class="pt-4 flex items-center justify-between gap-4">
                <a href="{{ route('admin.teachers.index') }}" class="px-4 py-2 bg-neutral-100 text-neutral-700 text-xs font-semibold rounded-xl hover:bg-neutral-200 transition">İptal</a>
                <button type="submit" class="px-4 py-2 bg-primary text-white text-xs font-semibold rounded-xl hover:bg-primary-dark transition shadow-sm">
                    Değişiklikleri Kaydet
                </button>
            </div>

        </x-admin.form.layout>
    </div>
@endsection
