@extends('layouts.admin')
@section('title', isset($teacher) ? 'Eğitmen Profili' : 'Yeni Eğitmen Tanımla')
@section('content')
    <div class="space-y-6">
        <x-admin.crud.index-layout title="{{ isset($teacher) ? 'Eğitmen Profilini Düzenle' : 'Yeni Eğitmen Oluştur' }}" description="Eğitmen özlük bilgilerini, şubelerini ve özel niteliklerini düzenleyin.">
            <x-slot name="actions">
                <a href="{{ route('admin.teachers.index') }}" class="px-4 py-2 bg-neutral-100 text-neutral-700 text-xs font-semibold rounded-xl hover:bg-neutral-200 transition">
                    Listeye Geri Dön
                </a>
            </x-slot>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Sol Panel: Düzenleme Formu -->
                <div class="lg:col-span-3 space-y-6">
                    <x-admin.form.layout :action="isset($teacher) ? route('admin.teachers.update', $teacher->id) : route('admin.teachers.store')" method="POST">
                        @if(isset($teacher))
                            @method('PUT')
                        @endif

                        <div class="bg-white dark:bg-neutral-900 p-8 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-6">
                            
                            @if(!isset($teacher))
                                <x-admin.form.field-group label="Kullanıcı Hesap Eşleşmesi" id="user_id">
                                    <select name="user_id" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2">
                                        @foreach($users as $u)
                                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                                        @endforeach
                                    </select>
                                </x-admin.form.field-group>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <x-admin.form.field-group label="Ünvan (Örn: Matematik Öğretmeni)" id="title">
                                    <input type="text" name="title" required value="{{ $teacher->title ?? '' }}" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                                </x-admin.form.field-group>

                                <x-admin.form.field-group label="Branşlar / Uzmanlık (Virgülle ayırın)" id="specialties">
                                    <input type="text" name="specialties" value="{{ $teacher->specialties ?? '' }}" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                                </x-admin.form.field-group>
                            </div>

                            <x-admin.form.field-group label="Eğitim / Mezuniyet" id="education">
                                <input type="text" name="education" value="{{ $teacher->education ?? '' }}" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                            </x-admin.form.field-group>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <x-admin.form.field-group label="Şube" id="branch_id">
                                    <select name="branch_id" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2">
                                        <option value="">Seçiniz</option>
                                        @foreach($branches as $b)
                                            <option value="{{ $b->id }}" {{ (isset($teacher) && $teacher->branch_id === $b->id) ? 'selected' : '' }}>{{ $b->name }}</option>
                                        @endforeach
                                    </select>
                                </x-admin.form.field-group>

                                <x-admin.form.field-group label="Deneyim Süresi (Yıl)" id="experience_years">
                                    <input type="number" name="experience_years" value="{{ $teacher->experience_years ?? 0 }}" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                                </x-admin.form.field-group>

                                <x-admin.form.field-group label="Çalışma Durumu" id="status">
                                    <select name="status" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2">
                                        <option value="Active" {{ (isset($teacher) && $teacher->status === 'Active') ? 'selected' : '' }}>Active (Aktif)</option>
                                        <option value="Inactive" {{ (isset($teacher) && $teacher->status === 'Inactive') ? 'selected' : '' }}>Inactive (Pasif)</option>
                                        <option value="Leave" {{ (isset($teacher) && $teacher->status === 'Leave') ? 'selected' : '' }}>Leave (İzinli)</option>
                                    </select>
                                </x-admin.form.field-group>
                            </div>

                            <x-admin.form.field-group label="Acil Durum İletişim Bilgisi" id="emergency_contact">
                                <input type="text" name="emergency_contact" value="{{ $teacher->emergency_contact ?? '' }}" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200">
                            </x-admin.form.field-group>

                            <x-admin.form.field-group label="Biyografi / Özgeçmiş" id="bio">
                                <textarea name="bio" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2 text-neutral-800 dark:text-neutral-200 h-32">{{ $teacher->bio ?? '' }}</textarea>
                            </x-admin.form.field-group>

                            <div class="pt-6 border-t border-neutral-100 dark:border-neutral-800 flex items-center justify-between">
                                <button type="submit" class="px-4 py-2 bg-primary text-white text-sm font-medium rounded-xl hover:bg-primary-dark transition shadow-sm">
                                    Profil Bilgilerini Kaydet
                                </button>
                            </div>

                        </div>
                    </x-admin.form.layout>
                </div>

                <!-- Sağ Panel: Performans & Sözleşmeler Link Kartları -->
                <div class="space-y-6">
                    @if(isset($teacher))
                        <!-- Analytics Performans Özeti -->
                        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                            <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider">KPI Özet Raporu</h4>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between text-xs py-1 border-b">
                                    <span class="text-neutral-500">Öğrenci Memnuniyeti:</span>
                                    <span class="font-bold text-neutral-800">{{ $analytics['student_satisfaction'] }}/5.0</span>
                                </div>
                                <div class="flex items-center justify-between text-xs py-1 border-b">
                                    <span class="text-neutral-500">Katılım Oranı:</span>
                                    <span class="font-bold text-neutral-800">%{{ $analytics['attendance_rate'] }}</span>
                                </div>
                                <div class="flex items-center justify-between text-xs py-1">
                                    <span class="text-neutral-500">Ders Sayısı:</span>
                                    <span class="font-bold text-neutral-800">{{ $analytics['lesson_count'] }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Özlük Belgeleri (Documents upload) -->
                        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                            <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider">Eğitmen Özlük Belgeleri</h4>
                            <div class="space-y-2">
                                @forelse($teacher->documents as $doc)
                                    <div class="flex items-center justify-between text-xs p-2 bg-neutral-50 dark:bg-neutral-800/40 rounded-xl">
                                        <div>
                                            <div class="font-semibold">{{ $doc->title }}</div>
                                            <div class="text-[10px] text-neutral-400">{{ $doc->type }}</div>
                                        </div>
                                        <a href="{{ $doc->file_path }}" target="_blank" class="text-primary hover:underline text-[10px]">İndir</a>
                                    </div>
                                @empty
                                    <div class="text-[10px] text-neutral-400">Kayıtlı belge bulunmuyor.</div>
                                @endforelse
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </x-admin.crud.index-layout>
    </div>
@endsection
