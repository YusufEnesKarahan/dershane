@extends('layouts.admin')
@section('title', 'Rol Klonla')
@section('content')
    <x-admin.crud.index-layout title="Rol Klonla" description="{{ $role->name }} rolündeki tüm yetkileri kopyalayarak yeni bir rol oluşturun.">
        <div class="max-w-2xl bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <x-admin.form.layout :action="route('admin.roles.clone', $role)" method="POST">
                <x-admin.form.field-group label="Yeni Rol Adı" id="name" :error="$errors->first('name')">
                    <input type="text" name="name" id="name" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                </x-admin.form.field-group>

                <x-admin.form.field-group label="Yeni Açıklama" id="description" :error="$errors->first('description')">
                    <textarea name="description" id="description" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200 h-24"></textarea>
                </x-admin.form.field-group>

                <x-admin.form.field-group label="Renk" id="color" :error="$errors->first('color')">
                    <input type="color" name="color" id="color" value="{{ $role->color ?? '#6366f1' }}" class="w-16 h-8 bg-neutral-50 border border-neutral-200 dark:border-neutral-700 rounded cursor-pointer">
                </x-admin.form.field-group>

                <div class="flex items-center gap-3">
                    <button type="submit" class="px-4 py-2 bg-primary text-white text-sm font-medium rounded-xl hover:bg-primary-dark transition shadow-sm">Klonla</button>
                    <a href="{{ route('admin.roles.index') }}" class="px-4 py-2 text-sm text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 transition">Vazgeç</a>
                </div>
            </x-admin.form.layout>
        </div>
    </x-admin.crud.index-layout>
@endsection
