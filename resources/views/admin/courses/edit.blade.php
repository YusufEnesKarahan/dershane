@extends('layouts.admin')
@section('title', isset($course) ? 'Kurs Düzenle' : 'Yeni Kurs Ekle')
@section('content')
    <div class="space-y-6">
        <x-admin.crud.index-layout title="{{ isset($course) ? 'Kurs Detaylarını Düzenle' : 'Yeni Kurs Tanımla' }}" description="Kurs temel tanımlamalarını, şube/eğitmen atamalarını ve fiyatlandırmasını belirleyin.">
            <x-slot name="actions">
                <x-admin.button href="{{ route('admin.courses.index') }}" variant="secondary" icon="M10 19l-7-7m0 0l7-7m-7 7h18">
                    Listeye Geri Dön
                </x-admin.button>
            </x-slot>

            <x-admin.form.layout :action="isset($course) ? route('admin.courses.update', $course->id) : route('admin.courses.store')" method="POST">
                @if(isset($course))
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                    <!-- Sol Panel: Form Elemanları -->
                    <div class="lg:col-span-3 space-y-6">
                        <div class="bg-white dark:bg-neutral-900 p-6 sm:p-8 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-6">
                            
                            <h3 class="text-base font-bold text-neutral-900 dark:text-white flex items-center gap-2 mb-6 border-b border-neutral-100 dark:border-neutral-800 pb-4">
                                <svg class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                Temel Kurs Bilgileri
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <x-admin.form.field-group label="Kurs Kodu (Benzersiz)" id="code" required>
                                    <input type="text" name="code" id="code" required value="{{ $course->code ?? '' }}" {{ isset($course) ? 'disabled' : '' }} class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-neutral-900 dark:text-white transition-colors {{ isset($course) ? 'opacity-50 cursor-not-allowed bg-neutral-50 dark:bg-neutral-800' : '' }} font-mono">
                                    @if($errors->has('code'))
                                        <div class="text-xs text-red-500 mt-1">{{ $errors->first('code') }}</div>
                                    @endif
                                </x-admin.form.field-group>

                                <x-admin.form.field-group label="Kurs Adı" id="name" required>
                                    <input type="text" name="name" id="name" required value="{{ $course->name ?? '' }}" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-neutral-900 dark:text-white transition-colors">
                                </x-admin.form.field-group>

                                <x-admin.form.field-group label="Kurs Seviyesi" id="course_level_id">
                                    <select name="course_level_id" id="course_level_id" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-neutral-900 dark:text-white transition-colors">
                                        <option value="">Seçiniz</option>
                                        @foreach($levels as $l)
                                            <option value="{{ $l->id }}" {{ (isset($course) && $course->course_level_id === $l->id) ? 'selected' : '' }}>{{ $l->name }}</option>
                                        @endforeach
                                    </select>
                                </x-admin.form.field-group>
                            </div>

                            <x-admin.form.field-group label="Açıklama" id="description">
                                <textarea name="description" id="description" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-neutral-900 dark:text-white transition-colors h-24">{{ $course->description ?? '' }}</textarea>
                            </x-admin.form.field-group>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                <x-admin.form.field-group label="Süre" id="duration">
                                    <input type="text" name="duration" id="duration" placeholder="Örn: 10 Ay" value="{{ $course->duration ?? '' }}" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-neutral-900 dark:text-white transition-colors">
                                </x-admin.form.field-group>

                                <x-admin.form.field-group label="Kontenjan (Kişi)" id="capacity">
                                    <input type="number" name="capacity" id="capacity" value="{{ $course->capacity ?? 0 }}" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-neutral-900 dark:text-white transition-colors">
                                </x-admin.form.field-group>

                                <x-admin.form.field-group label="Fiyat (₺)" id="price" required>
                                    <input type="number" step="0.01" name="price" id="price" required value="{{ $course->currentPricing ? $course->currentPricing->price : '' }}" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-neutral-900 dark:text-white transition-colors">
                                </x-admin.form.field-group>

                                <x-admin.form.field-group label="Aktiflik Durumu" id="is_active">
                                    <select name="is_active" id="is_active" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-neutral-900 dark:text-white transition-colors">
                                        <option value="1" {{ (!isset($course) || $course->is_active) ? 'selected' : '' }}>Aktif (Satışta)</option>
                                        <option value="0" {{ (isset($course) && !$course->is_active) ? 'selected' : '' }}>Pasif (Gizli)</option>
                                    </select>
                                </x-admin.form.field-group>
                            </div>

                            @if(isset($course))
                                <!-- Atama Pivot Kartları -->
                                <div class="mt-8 pt-6 border-t border-neutral-100 dark:border-neutral-800 space-y-4">
                                    <h4 class="text-sm font-bold text-neutral-700 dark:text-neutral-300 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
                                        Atamalar & Koşullar
                                    </h4>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                        <x-admin.form.field-group label="Eğitmenler (Çoklu Seçim)" id="teachers">
                                            <select name="teachers[]" id="teachers" multiple class="w-full h-32 bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-neutral-900 dark:text-white transition-colors">
                                                @foreach($teachers as $t)
                                                    <option value="{{ $t->id }}" {{ (isset($course) && $course->teachers->contains($t->id)) ? 'selected' : '' }}>{{ $t->user->name }}</option>
                                                @endforeach
                                            </select>
                                            <div class="text-xs text-neutral-500 mt-1">CTRL/CMD ile çoklu seçim yapabilirsiniz.</div>
                                        </x-admin.form.field-group>

                                        <x-admin.form.field-group label="Sunulduğu Şubeler" id="branches">
                                            <select name="branches[]" id="branches" multiple class="w-full h-32 bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-neutral-900 dark:text-white transition-colors">
                                                @foreach($branches as $b)
                                                    <option value="{{ $b->id }}" {{ (isset($course) && $course->branches->contains($b->id)) ? 'selected' : '' }}>{{ $b->name }}</option>
                                                @endforeach
                                            </select>
                                        </x-admin.form.field-group>

                                        <x-admin.form.field-group label="Ön Koşul Kurslar" id="prerequisites">
                                            <select name="prerequisites[]" id="prerequisites" multiple class="w-full h-32 bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-neutral-900 dark:text-white transition-colors">
                                                @foreach($prerequisites as $prereq)
                                                    <option value="{{ $prereq->id }}" {{ (isset($course) && $course->prerequisites->contains($prereq->id)) ? 'selected' : '' }}>{{ $prereq->name }}</option>
                                                @endforeach
                                            </select>
                                        </x-admin.form.field-group>
                                    </div>
                                </div>
                            @endif

                            <div class="pt-6 mt-6 border-t border-neutral-100 dark:border-neutral-800 flex items-center justify-end">
                                <x-admin.button type="submit" variant="primary" icon="M5 13l4 4L19 7">
                                    {{ isset($course) ? 'Kursu Güncelle' : 'Kursu Kaydet' }}
                                </x-admin.button>
                            </div>

                        </div>
                    </div>

                    <!-- Sağ Panel: Kapak Görseli / Bilgiler -->
                    <div class="space-y-6">
                        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-4">
                            <h4 class="text-sm font-bold text-neutral-900 dark:text-white flex items-center gap-2">
                                <svg class="w-4 h-4 text-neutral-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                Kapak Görseli
                            </h4>
                            <div class="text-xs text-neutral-500 dark:text-neutral-400 mb-2">Kurs listelerinde ve detay sayfasında gösterilecek temsili görsel.</div>
                            <x-admin.media-picker name="cover_image" value="{{ $course->cover_image ?? '' }}" />
                        </div>
                        
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
                                    <div class="flex justify-between">
                                        <span class="text-neutral-500 dark:text-neutral-400">Kayıtlı Öğrenci:</span>
                                        <span class="font-bold text-primary">{{ $course->enrollments()->count() }} Kişi</span>
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
