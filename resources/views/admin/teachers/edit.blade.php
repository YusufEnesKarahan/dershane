@extends('layouts.admin')
@section('title', 'Öğretmen Düzenle')
@section('content')
    <x-admin.crud.index-layout title="Öğretmen Profilini Düzenle" description="Öğretmenin ünvan, biyografi ve tecrübe bilgilerini güncelleyin.">
    <div class="bg-white dark:bg-neutral-900 p-6 sm:p-8 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm max-w-xl mx-auto flex flex-col h-full">
        <h3 class="text-base font-bold text-neutral-900 dark:text-white mb-6 flex items-center gap-2">
            <svg class="w-5 h-5 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
            Profil Bilgileri
        </h3>

        <x-admin.form.layout :action="route('admin.teachers.update', $teacher->id)" method="PUT">
            
            <x-admin.form.field-group label="Ünvan / Pozisyon" id="title">
                <input type="text" name="title" required value="{{ $teacher->title }}" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 focus:ring-violet-500 focus:border-violet-500 dark:text-white transition-colors">
            </x-admin.form.field-group>

            <x-admin.form.field-group label="Uzmanlık Alanları" id="specialties">
                <input type="text" name="specialties" required value="{{ $teacher->specialties }}" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 focus:ring-violet-500 focus:border-violet-500 dark:text-white transition-colors">
            </x-admin.form.field-group>

            <x-admin.form.field-group label="Biyografi / Detay" id="bio">
                <textarea name="bio" rows="4" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 focus:ring-violet-500 focus:border-violet-500 dark:text-white transition-colors">{{ $teacher->bio }}</textarea>
            </x-admin.form.field-group>

            <x-admin.form.field-group label="Mezuniyet" id="education">
                <input type="text" name="education" value="{{ $teacher->education }}" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 focus:ring-violet-500 focus:border-violet-500 dark:text-white transition-colors">
            </x-admin.form.field-group>

            <x-admin.form.field-group label="Deneyim Yılı" id="experience_years">
                <input type="number" name="experience_years" value="{{ $teacher->experience_years }}" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 focus:ring-violet-500 focus:border-violet-500 dark:text-white transition-colors">
            </x-admin.form.field-group>

            <x-admin.form.field-group label="Durum" id="status">
                <select name="status" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 focus:ring-violet-500 focus:border-violet-500 dark:text-white transition-colors">
                    <option value="Active" {{ $teacher->status === 'Active' ? 'selected' : '' }}>Aktif</option>
                    <option value="Inactive" {{ $teacher->status === 'Inactive' ? 'selected' : '' }}>Pasif</option>
                </select>
            </x-admin.form.field-group>

            <div class="pt-6 mt-6 flex justify-end gap-3 border-t border-neutral-100 dark:border-neutral-800">
                <a href="{{ route('admin.teachers.index') }}" class="px-5 py-2.5 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-neutral-700 dark:text-neutral-300 text-sm font-bold rounded-xl transition-colors">İptal</a>
                <button type="submit" class="px-5 py-2.5 bg-violet-600 hover:bg-violet-500 text-white text-sm font-bold rounded-xl transition-colors shadow-lg shadow-violet-900/20 border border-violet-500/50 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    Değişiklikleri Kaydet
                </button>
            </div>

        </x-admin.form.layout>
    </div>
    </x-admin.crud.index-layout>
@endsection
