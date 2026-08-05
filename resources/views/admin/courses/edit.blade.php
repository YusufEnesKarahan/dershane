@extends('layouts.admin')
@section('title', isset($course) ? 'Ders Düzenle' : 'Yeni Ders Ekle')
@section('content')
    <div class="space-y-6">
        <x-admin.crud.index-layout title="{{ isset($course) ? 'Ders Detaylarını Düzenle' : 'Yeni Ders Tanımla' }}" description="Ders temel tanımlamalarını, ana ve yardımcı öğretmen atamalarını ve fiyatlandırmasını belirleyin.">
            <x-slot name="actions">
                <x-admin.button href="{{ route('admin.courses.index') }}" variant="secondary" icon="M10 19l-7-7m0 0l7-7m-7 7h18">
                    Listeye Geri Dön
                </x-admin.button>
            </x-slot>

            @if(session('error'))
                <div class="mb-6 p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-700 rounded-r-lg shadow-sm">
                    <div class="flex items-center gap-2 font-bold text-sm">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            <x-admin.form.layout :action="isset($course) ? route('admin.courses.update', $course->id) : route('admin.courses.store')" method="POST">
                @if(isset($course))
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                    <!-- Sol Panel: Form Elemanları -->
                    <div class="lg:col-span-3 space-y-6">
                        <div class="bg-white dark:bg-neutral-900 p-6 sm:p-8 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-6">
                            
                            <h3 class="text-base font-bold text-neutral-900 dark:text-white flex items-center gap-2 mb-6 border-b border-neutral-100 dark:border-neutral-800 pb-4">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                Temel Ders Bilgileri
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <x-admin.form.field-group label="Ders Kodu (Benzersiz)" id="code" required>
                                    <input type="text" name="code" id="code" required value="{{ old('code', $course->code ?? '') }}" {{ isset($course) ? 'readonly' : '' }} class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-neutral-900 dark:text-white transition-colors {{ isset($course) ? 'opacity-70 cursor-not-allowed bg-neutral-50 dark:bg-neutral-800' : '' }} font-mono">
                                    @if($errors->has('code'))
                                        <div class="text-xs text-rose-500 mt-1">{{ $errors->first('code') }}</div>
                                    @endif
                                </x-admin.form.field-group>

                                <x-admin.form.field-group label="Ders Adı" id="name" required>
                                    <input type="text" name="name" id="name" required value="{{ old('name', $course->name ?? '') }}" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-neutral-900 dark:text-white transition-colors" placeholder="Örn: Matematik - AYT">
                                </x-admin.form.field-group>

                                <x-admin.form.field-group label="Ders Seviyesi" id="course_level_id">
                                    <select name="course_level_id" id="course_level_id" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-neutral-900 dark:text-white transition-colors">
                                        <option value="">Genel Seviye</option>
                                        @foreach($levels as $l)
                                            <option value="{{ $l->id }}" {{ (old('course_level_id', $course->course_level_id ?? null) == $l->id) ? 'selected' : '' }}>{{ $l->name }}</option>
                                        @endforeach
                                    </select>
                                </x-admin.form.field-group>
                            </div>

                            <x-admin.form.field-group label="Açıklama" id="description">
                                <textarea name="description" id="description" placeholder="Ders içeriği ve müfredat özeti..." class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-neutral-900 dark:text-white transition-colors h-24">{{ old('description', $course->description ?? '') }}</textarea>
                            </x-admin.form.field-group>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                <x-admin.form.field-group label="Süre / Sezon" id="duration">
                                    <input type="text" name="duration" id="duration" placeholder="Örn: 9 Ay" value="{{ old('duration', $course->duration ?? '') }}" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-neutral-900 dark:text-white transition-colors">
                                </x-admin.form.field-group>

                                <x-admin.form.field-group label="Kontenjan (Kişi)" id="capacity">
                                    <input type="number" name="capacity" id="capacity" value="{{ old('capacity', $course->capacity ?? 24) }}" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-neutral-900 dark:text-white transition-colors">
                                </x-admin.form.field-group>

                                <x-admin.form.field-group label="Fiyat (₺)" id="price" required>
                                    <input type="number" step="0.01" name="price" id="price" required value="{{ old('price', $course?->currentPricing?->price ?? 0) }}" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-neutral-900 dark:text-white transition-colors">
                                </x-admin.form.field-group>

                                <x-admin.form.field-group label="Aktiflik Durumu" id="is_active">
                                    <select name="is_active" id="is_active" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-neutral-900 dark:text-white transition-colors">
                                        <option value="1" {{ (!isset($course) || $course->is_active) ? 'selected' : '' }}>Aktif</option>
                                        <option value="0" {{ (isset($course) && !$course->is_active) ? 'selected' : '' }}>Pasif</option>
                                    </select>
                                </x-admin.form.field-group>
                            </div>

                            <!-- Öğretmen Atama Kartı -->
                            <div class="mt-8 pt-6 border-t border-neutral-100 dark:border-neutral-800 space-y-4">
                                <h4 class="text-sm font-bold text-neutral-800 dark:text-neutral-200 flex items-center gap-2">
                                    <i class="fas fa-user-tie text-indigo-500"></i>
                                    Öğretmen Atamaları (Ana Öğretmen & Yardımcı Öğretmenler)
                                </h4>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 bg-neutral-50 dark:bg-neutral-800/40 rounded-xl border border-neutral-200 dark:border-neutral-700">
                                    <x-admin.form.field-group label="Ana Öğretmen (Primary Teacher)" id="primary_teacher_id">
                                        <select name="primary_teacher_id" id="primary_teacher_id" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                            <option value="">Ana Öğretmen Seçiniz...</option>
                                            @foreach($teachers as $t)
                                                @php
                                                    $isPrimary = isset($course) && $course->teachers->contains(function($teacher) use ($t) {
                                                        return $teacher->id === $t->id && $teacher->pivot->is_primary;
                                                    });
                                                @endphp
                                                <option value="{{ $t->id }}" {{ $isPrimary ? 'selected' : '' }}>{{ $t->user?->name ?? 'Öğretmen #' . $t->id }}</option>
                                            @endforeach
                                        </select>
                                    </x-admin.form.field-group>

                                    <x-admin.form.field-group label="Yardımcı Öğretmenler (Assistant Teachers)" id="assistant_teacher_ids">
                                        <select name="assistant_teacher_ids[]" id="assistant_teacher_ids" multiple class="w-full h-28 bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                            @foreach($teachers as $t)
                                                @php
                                                    $isAsst = isset($course) && $course->teachers->contains(function($teacher) use ($t) {
                                                        return $teacher->id === $t->id && !$teacher->pivot->is_primary;
                                                    });
                                                @endphp
                                                <option value="{{ $t->id }}" {{ $isAsst ? 'selected' : '' }}>{{ $t->user?->name ?? 'Öğretmen #' . $t->id }}</option>
                                            @endforeach
                                        </select>
                                        <div class="text-[11px] text-neutral-500 mt-1">Birden fazla yardımcı öğretmen için CTRL / CMD tuşu ile çoklu seçim yapın.</div>
                                    </x-admin.form.field-group>
                                </div>
                            </div>

                            <div class="pt-6 mt-6 border-t border-neutral-100 dark:border-neutral-800 flex items-center justify-end">
                                <x-admin.button type="submit" variant="primary" icon="M5 13l4 4L19 7">
                                    {{ isset($course) ? 'Dersi Güncelle' : 'Dersi Kaydet' }}
                                </x-admin.button>
                            </div>

                        </div>
                    </div>

                    <!-- Sağ Panel: Bilgiler -->
                    <div class="space-y-6">
                        @if(isset($course))
                            <div class="bg-neutral-50 dark:bg-neutral-800/40 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800/60 shadow-sm space-y-3">
                                <h4 class="text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Sistem Bilgileri</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-neutral-500 dark:text-neutral-400">Oluşturulma:</span>
                                        <span class="font-medium text-neutral-900 dark:text-white">{{ $course->created_at->format('d.m.Y H:i') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-neutral-500 dark:text-neutral-400">Son Güncelleme:</span>
                                        <span class="font-medium text-neutral-900 dark:text-white">{{ $course->updated_at->format('d.m.Y H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

            </x-admin.form.layout>
        </x-admin.crud.index-layout>
    </div>
@endsection
