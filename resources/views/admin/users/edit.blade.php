@extends('layouts.admin')
@section('title', 'Kullanıcı Düzenle')
@section('content')
    <x-admin.crud.index-layout title="Kullanıcı Düzenle" description="{{ $user->name }} kullanıcısını düzenliyorsunuz.">
        <div class="max-w-2xl bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <x-admin.form.layout :action="route('admin.users.update', $user)" method="PUT">
                <x-admin.form.field-group label="Tam Adı" id="name" :error="$errors->first('name')">
                    <input type="text" name="name" id="name" value="{{ $user->name }}" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                </x-admin.form.field-group>

                <x-admin.form.field-group label="E-Posta" id="email" :error="$errors->first('email')">
                    <input type="email" name="email" id="email" value="{{ $user->email }}" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                </x-admin.form.field-group>

                <x-admin.form.field-group label="Şifre (Değiştirmek istemiyorsanız boş bırakın)" id="password" :error="$errors->first('password')">
                    <input type="password" name="password" id="password" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                </x-admin.form.field-group>

                <x-admin.form.field-group label="Telefon" id="phone" :error="$errors->first('phone')">
                    <input type="text" name="phone" id="phone" value="{{ $user->phone }}" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                </x-admin.form.field-group>

                <x-admin.form.field-group label="Roller" id="roles" :error="$errors->first('roles')">
                    <select name="roles[]" id="roles" multiple required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200 h-32">
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ $user->roles->contains($role->id) ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </x-admin.form.field-group>

                <x-admin.form.field-group label="Şube" id="branch_id" :error="$errors->first('branch_id')">
                    <select name="branch_id" id="branch_id" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                        <option value="">Tüm Şubeler</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ $user->branch_id == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </x-admin.form.field-group>

                <x-admin.form.field-group label="Durum" id="status" :error="$errors->first('status')">
                    <select name="status" id="status" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                        <option value="ACTIVE" {{ $user->status->value === 'ACTIVE' ? 'selected' : '' }}>Aktif</option>
                        <option value="PASSIVE" {{ $user->status->value === 'PASSIVE' ? 'selected' : '' }}>Pasif</option>
                        <option value="SUSPENDED" {{ $user->status->value === 'SUSPENDED' ? 'selected' : '' }}>Askıda</option>
                    </select>
                </x-admin.form.field-group>

                <div class="flex items-center gap-3">
                    <button type="submit" class="px-4 py-2 bg-primary text-white text-sm font-medium rounded-xl hover:bg-primary-dark transition shadow-sm">Güncelle</button>
                    <a href="{{ route('admin.users.index') }}" class="px-4 py-2 text-sm text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 transition">Vazgeç</a>
                </div>
            </x-admin.form.layout>
        </div>
    </x-admin.crud.index-layout>
@endsection
