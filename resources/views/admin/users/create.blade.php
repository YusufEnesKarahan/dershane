@extends('layouts.admin')
@section('title', 'Kullanıcı Ekle')
@section('content')
    <x-admin.crud.index-layout title="Yeni Kullanıcı Ekle">
        <div class="max-w-2xl bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <x-admin.form.layout :action="route('admin.users.store')" method="POST">
                <x-admin.form.field-group label="Tam Adı" id="name" :error="$errors->first('name')">
                    <input type="text" name="name" id="name" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                </x-admin.form.field-group>

                <x-admin.form.field-group label="E-Posta" id="email" :error="$errors->first('email')">
                    <input type="email" name="email" id="email" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                </x-admin.form.field-group>

                <x-admin.form.field-group label="Şifre" id="password" :error="$errors->first('password')">
                    <input type="password" name="password" id="password" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                </x-admin.form.field-group>

                <x-admin.form.field-group label="Telefon" id="phone" :error="$errors->first('phone')">
                    <input type="text" name="phone" id="phone" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                </x-admin.form.field-group>

                <x-admin.form.field-group label="Roller" id="roles" :error="$errors->first('roles')">
                    <select name="roles[]" id="roles" multiple required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200 h-32">
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </x-admin.form.field-group>

                <x-admin.form.field-group label="Şube" id="branch_id" :error="$errors->first('branch_id')">
                    <select name="branch_id" id="branch_id" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                        <option value="">Tüm Şubeler</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </x-admin.form.field-group>

                <x-admin.form.field-group label="Durum" id="status" :error="$errors->first('status')">
                    <select name="status" id="status" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                        <option value="ACTIVE">Aktif</option>
                        <option value="PASSIVE">Pasif</option>
                        <option value="SUSPENDED">Askıda</option>
                    </select>
                </x-admin.form.field-group>

                <div class="flex items-center gap-3">
                    <button type="submit" class="px-4 py-2 bg-primary text-white text-sm font-medium rounded-xl hover:bg-primary-dark transition shadow-sm">Kaydet</button>
                    <a href="{{ route('admin.users.index') }}" class="px-4 py-2 text-sm text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 transition">Vazgeç</a>
                </div>
            </x-admin.form.layout>
        </div>
    </x-admin.crud.index-layout>
@endsection
