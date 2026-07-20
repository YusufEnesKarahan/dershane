@extends('layouts.admin')
@section('title', isset($classroom) ? 'Derslik Düzenle' : 'Yeni Derslik Ekle')
@section('content')
    <x-admin.crud.index-layout title="{{ isset($classroom) ? 'Derslik Tanımını Düzenle' : 'Yeni Derslik Ekle' }}" description="Fiziki sınıf kodlarını, kapasite ve renk tanımlarını güncelleyin.">
        <x-slot name="actions">
            <a href="{{ route('admin.classrooms.index') }}" class="px-4 py-2 bg-neutral-100 text-neutral-700 text-xs font-semibold rounded-xl hover:bg-neutral-200 transition">
                Listeye Geri Dön
            </a>
        </x-slot>

        <x-admin.form.layout :action="isset($classroom) ? route('admin.classrooms.update', $classroom->id) : route('admin.classrooms.store')" method="POST">
            @if(isset($classroom))
                @method('PUT')
            @endif

            <div class="bg-white dark:bg-neutral-900 p-8 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-6 max-w-3xl">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-admin.form.field-group label="Derslik Kodu (Benzersiz)" id="code">
                        <input type="text" name="code" required value="{{ $classroom->code ?? '' }}" {{ isset($classroom) ? 'disabled' : '' }} class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 rounded-lg px-3 py-2">
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Derslik Adı" id="name">
                        <input type="text" name="name" required value="{{ $classroom->name ?? '' }}" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 rounded-lg px-3 py-2">
                    </x-admin.form.field-group>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <x-admin.form.field-group label="Bağlı Şube" id="branch_id">
                        <select name="branch_id" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 rounded-lg px-3 py-2">
                            <option value="">Tüm Şubeler</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}" {{ (isset($classroom) && $classroom->branch_id === $b->id) ? 'selected' : '' }}>{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Derslik Tipi" id="classroom_type_id">
                        <select name="classroom_type_id" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 rounded-lg px-3 py-2">
                            <option value="">Standart Derslik</option>
                            @foreach($types as $t)
                                <option value="{{ $t->id }}" {{ (isset($classroom) && $classroom->classroom_type_id === $t->id) ? 'selected' : '' }}>{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Maksimum Kapasite" id="capacity">
                        <input type="number" name="capacity" required value="{{ $classroom->capacity ?? 30 }}" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 rounded-lg px-3 py-2">
                    </x-admin.form.field-group>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-admin.form.field-group label="Renk Kodu (Program Etiketi)" id="color_code">
                        <input type="color" name="color_code" value="{{ $classroom->color_code ?? '#4F46E5' }}" class="w-full h-10 p-1 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 rounded-lg cursor-pointer">
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Aktiflik Durumu" id="is_active">
                        <select name="is_active" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 rounded-lg px-3 py-2">
                            <option value="1" {{ (!isset($classroom) || $classroom->is_active) ? 'selected' : '' }}>Aktif (Kullanılabilir)</option>
                            <option value="0" {{ (isset($classroom) && !$classroom->is_active) ? 'selected' : '' }}>Pasif (Bakımda)</option>
                        </select>
                    </x-admin.form.field-group>
                </div>

                <div class="pt-4 border-t border-neutral-100 flex items-center justify-between">
                    <button type="submit" class="px-4 py-2 bg-primary text-white text-sm font-medium rounded-xl hover:bg-primary-dark transition shadow-sm">
                        Dersliği Kaydet
                    </button>
                </div>
            </div>
        </x-admin.form.layout>
    </x-admin.crud.index-layout>
@endsection
