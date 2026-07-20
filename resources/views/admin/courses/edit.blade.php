@extends('layouts.admin')
@section('title', isset($course) ? 'Kurs Düzenle' : 'Yeni Kurs Ekle')
@section('content')
    <div class="space-y-6">
        <x-admin.crud.index-layout title="{{ isset($course) ? 'Kurs Detaylarını Düzenle' : 'Yeni Kurs Tanımla' }}" description="Kurs temel tanımlamalarını, şube/eğitmen atamalarını ve fiyatlandırmasını belirleyin.">
            <x-slot name="actions">
                <a href="{{ route('admin.courses.index') }}" class="px-4 py-2 bg-neutral-100 text-neutral-700 text-xs font-semibold rounded-xl hover:bg-neutral-200 transition">
                    Listeye Geri Dön
                </a>
            </x-slot>

            <x-admin.form.layout :action="isset($course) ? route('admin.courses.update', $course->id) : route('admin.courses.store')" method="POST">
                @if(isset($course))
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                    <!-- Sol Panel: Form Elemanları -->
                    <div class="lg:col-span-3 space-y-6">
                        <div class="bg-white dark:bg-neutral-900 p-8 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-6">
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <x-admin.form.field-group label="Kurs Kodu (Benzersiz)" id="code">
                                    <input type="text" name="code" required value="{{ $course->code ?? '' }}" {{ isset($course) ? 'disabled' : '' }} class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                                    @if($errors->has('code'))
                                        <div class="text-[10px] text-red-500 mt-1">{{ $errors->first('code') }}</div>
                                    @endif
                                </x-admin.form.field-group>

                                <x-admin.form.field-group label="Kurs Adı" id="name">
                                    <input type="text" name="name" required value="{{ $course->name ?? '' }}" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                                </x-admin.form.field-group>

                                <x-admin.form.field-group label="Kurs Seviyesi" id="course_level_id">
                                    <select name="course_level_id" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2">
                                        <option value="">Seçiniz</option>
                                        @foreach($levels as $l)
                                            <option value="{{ $l->id }}" {{ (isset($course) && $course->course_level_id === $l->id) ? 'selected' : '' }}>{{ $l->name }}</option>
                                        @endforeach
                                    </select>
                                </x-admin.form.field-group>
                            </div>

                            <x-admin.form.field-group label="Açıklama" id="description">
                                <textarea name="description" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 h-24">{{ $course->description ?? '' }}</textarea>
                            </x-admin.form.field-group>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <x-admin.form.field-group label="Süre (Örn: 10 Ay)" id="duration">
                                    <input type="text" name="duration" value="{{ $course->duration ?? '' }}" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                                </x-admin.form.field-group>

                                <x-admin.form.field-group label="Kontenjan (Kişi)" id="capacity">
                                    <input type="number" name="capacity" value="{{ $course->capacity ?? 0 }}" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                                </x-admin.form.field-group>

                                <x-admin.form.field-group label="Fiyat (TL)" id="price">
                                    <input type="number" name="price" required value="{{ $course->currentPricing ? $course->currentPricing->price : '' }}" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                                </x-admin.form.field-group>

                                <x-admin.form.field-group label="Aktiflik Durumu" id="is_active">
                                    <select name="is_active" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2">
                                        <option value="1" {{ (!isset($course) || $course->is_active) ? 'selected' : '' }}>Aktif (Satışta)</option>
                                        <option value="0" {{ (isset($course) && !$course->is_active) ? 'selected' : '' }}>Pasif (Gizli)</option>
                                    </select>
                                </x-admin.form.field-group>
                            </div>

                            @if(isset($course))
                                <!-- Atama Pivot Kartları -->
                                <div class="p-6 bg-neutral-50 dark:bg-neutral-800/40 border rounded-2xl space-y-4">
                                    <h4 class="text-xs font-bold text-neutral-800 dark:text-white uppercase tracking-wider">Atamalar & Koşullar</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        
                                        <x-admin.form.field-group label="Eğitmenler (Çoklu Seçim)" id="teachers">
                                            <select name="teachers[]" multiple class="w-full h-28 text-xs bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 rounded-lg p-2">
                                                @foreach($teachers as $t)
                                                    <option value="{{ $t->id }}" {{ (isset($course) && $course->teachers->contains($t->id)) ? 'selected' : '' }}>{{ $t->user->name }}</option>
                                                @endforeach
                                            </select>
                                        </x-admin.form.field-group>

                                        <x-admin.form.field-group label="Sunulduğu Şubeler" id="branches">
                                            <select name="branches[]" multiple class="w-full h-28 text-xs bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 rounded-lg p-2">
                                                @foreach($branches as $b)
                                                    <option value="{{ $b->id }}" {{ (isset($course) && $course->branches->contains($b->id)) ? 'selected' : '' }}>{{ $b->name }}</option>
                                                @endforeach
                                            </select>
                                        </x-admin.form.field-group>

                                        <x-admin.form.field-group label="Ön Koşul Kurslar" id="prerequisites">
                                            <select name="prerequisites[]" multiple class="w-full h-28 text-xs bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 rounded-lg p-2">
                                                @foreach($prerequisites as $prereq)
                                                    <option value="{{ $prereq->id }}" {{ (isset($course) && $course->prerequisites->contains($prereq->id)) ? 'selected' : '' }}>{{ $prereq->name }}</option>
                                                @endforeach
                                            </select>
                                        </x-admin.form.field-group>

                                    </div>
                                </div>
                            @endif

                            <div class="pt-6 border-t border-neutral-100 dark:border-neutral-800 flex items-center justify-between">
                                <button type="submit" class="px-4 py-2 bg-primary text-white text-sm font-medium rounded-xl hover:bg-primary-dark transition shadow-sm">
                                    Kursu Kaydet
                                </button>
                            </div>

                        </div>
                    </div>

                    <!-- Sağ Panel: Kapak Görseli / Bilgiler -->
                    <div class="space-y-6">
                        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                            <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider">Kapak Görseli</h4>
                            <x-admin.media-picker name="cover_image" value="{{ $course->cover_image ?? '' }}" />
                        </div>
                    </div>
                </div>

            </x-admin.form.layout>
        </x-admin.crud.index-layout>
    </div>
@endsection
