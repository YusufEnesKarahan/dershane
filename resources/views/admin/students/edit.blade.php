@extends('layouts.admin')
@section('title', isset($student) ? 'Öğrenci Profili' : 'Yeni Öğrenci Kaydı')
@section('content')
    <x-admin.crud.index-layout title="{{ isset($student) ? 'Öğrenci Profil Kartı' : 'Yeni Öğrenci Oluştur' }}" description="Öğrencinin özlük bilgilerini, veli iletişimini, kurs kayıtlarını ve transfer durumlarını yönetin.">
        <x-slot name="actions">
            <x-admin.button href="{{ route('admin.students.index') }}" variant="secondary" icon="M10 19l-7-7m0 0l7-7m-7 7h18">
                Listeye Geri Dön
            </x-admin.button>
        </x-slot>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Panel: Temel Bilgiler -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-neutral-900 p-6 sm:p-8 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-6">
                    <h3 class="text-base font-bold text-neutral-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        Öğrenci Kimlik & Özlük Bilgileri
                    </h3>
                    
                    <x-admin.form.layout :action="isset($student) ? route('admin.students.update', $student->id) : route('admin.students.store')" method="POST">
                        @if(isset($student))
                            @method('PUT')
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <x-admin.form.field-group label="Öğrenci No" id="student_number" required>
                                <input type="text" name="student_number" id="student_number" required value="{{ $student->student_number ?? 'OGR-' . rand(1000, 9999) }}" {{ isset($student) ? 'disabled' : '' }} class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-neutral-900 dark:text-white transition-colors {{ isset($student) ? 'opacity-50 cursor-not-allowed bg-neutral-50 dark:bg-neutral-800' : '' }}">
                            </x-admin.form.field-group>

                            <x-admin.form.field-group label="Adı" id="first_name" required>
                                <input type="text" name="first_name" id="first_name" required value="{{ $student->first_name ?? '' }}" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-neutral-900 dark:text-white transition-colors">
                            </x-admin.form.field-group>

                            <x-admin.form.field-group label="Soyadı" id="last_name" required>
                                <input type="text" name="last_name" id="last_name" required value="{{ $student->last_name ?? '' }}" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-neutral-900 dark:text-white transition-colors">
                            </x-admin.form.field-group>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <x-admin.form.field-group label="TC / Pasaport No" id="identity_number">
                                <input type="text" name="identity_number" id="identity_number" value="{{ $student->identity_number ?? '' }}" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-neutral-900 dark:text-white transition-colors font-mono uppercase">
                            </x-admin.form.field-group>

                            <x-admin.form.field-group label="Kayıtlı Şube" id="branch_id" required>
                                <select name="branch_id" id="branch_id" required class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-neutral-900 dark:text-white transition-colors">
                                    @foreach($branches as $b)
                                        <option value="{{ $b->id }}" {{ (isset($student) && $student->branch_id === $b->id) ? 'selected' : '' }}>{{ $b->name }}</option>
                                    @endforeach
                                </select>
                            </x-admin.form.field-group>

                            <x-admin.form.field-group label="Atandığı Sınıf" id="classroom_id">
                                <select name="classroom_id" id="classroom_id" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-neutral-900 dark:text-white transition-colors">
                                    <option value="">Sınıf Seçiniz</option>
                                    @foreach($classrooms as $c)
                                        <option value="{{ $c->id }}" {{ (isset($student) && $student->classroom_id === $c->id) ? 'selected' : '' }}>{{ $c->name }} ({{ $c->code }})</option>
                                    @endforeach
                                </select>
                            </x-admin.form.field-group>
                        </div>

                        @if(!isset($student))
                            <div class="p-5 bg-neutral-50 dark:bg-neutral-800/50 rounded-xl border border-neutral-200 dark:border-neutral-700/50 space-y-4">
                                <h4 class="text-sm font-bold text-neutral-700 dark:text-neutral-300 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                    Veli Bilgileri (Hızlı Ekleme)
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">Veli Ad Soyad</label>
                                        <input type="text" name="guardian_name" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-lg shadow-sm focus:ring-primary focus:border-primary text-sm text-neutral-900 dark:text-white transition-colors">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">Yakınlık</label>
                                        <input type="text" name="guardian_relation" placeholder="Örn: Baba" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-lg shadow-sm focus:ring-primary focus:border-primary text-sm text-neutral-900 dark:text-white transition-colors">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">Veli Telefon</label>
                                        <input type="text" name="guardian_phone" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-lg shadow-sm focus:ring-primary focus:border-primary text-sm text-neutral-900 dark:text-white transition-colors">
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="pt-6 mt-6 flex justify-end border-t border-neutral-100 dark:border-neutral-800">
                            <x-admin.button type="submit" variant="primary" icon="M5 13l4 4L19 7">
                                {{ isset($student) ? 'Profil Bilgilerini Kaydet' : 'Öğrenciyi Kaydet' }}
                            </x-admin.button>
                        </div>
                    </x-admin.form.layout>
                </div>

                @if(isset($student))
                    <!-- Kurs Kaydı Kartı -->
                    <div class="bg-white dark:bg-neutral-900 p-6 sm:p-8 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-6">
                        <h4 class="text-base font-bold text-neutral-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                            Kayıtlı Olunan Kurslar
                        </h4>
                        
                        <div class="space-y-3">
                            @forelse($student->enrollments as $enr)
                                <div class="p-4 bg-neutral-50 dark:bg-neutral-800/50 border border-neutral-200 dark:border-neutral-700/50 rounded-xl flex items-center justify-between group hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors">
                                    <span class="text-sm font-bold text-neutral-900 dark:text-white">{{ $enr->course->name }}</span>
                                    <span class="text-sm font-semibold text-neutral-500 dark:text-neutral-400">{{ number_format($enr->price_paid, 2) }} TL</span>
                                </div>
                            @empty
                                <div class="p-6 text-center text-sm text-neutral-500 dark:text-neutral-400 border-2 border-dashed border-neutral-200 dark:border-neutral-700 rounded-xl">
                                    Henüz bir kursa kayıt yapılmamış.
                                </div>
                            @endforelse
                        </div>

                        <form action="{{ route('admin.students.enrollment.store') }}" method="POST" class="mt-4 pt-6 border-t border-neutral-100 dark:border-neutral-800">
                            @csrf
                            <input type="hidden" name="student_id" value="{{ $student->id }}">
                            <div class="flex flex-col sm:flex-row gap-3">
                                <select name="course_id" required class="flex-1 bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary text-sm text-neutral-900 dark:text-white transition-colors">
                                    <option value="">Kurs Seçip Ekle</option>
                                    @foreach($courses as $co)
                                        <option value="{{ $co->id }}">{{ $co->name }} ({{ number_format($co->currentPricing?->price ?? 0, 2) }} TL)</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-colors whitespace-nowrap flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                                    Kursa Kaydet
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>

            <!-- Sağ Panel: Veli ve Transfer İşlemleri -->
            <div class="space-y-6">
                @if(isset($student))
                    <!-- Veli Kartı -->
                    <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-4">
                        <h4 class="text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Veli / İletişim Detayları</h4>
                        <div class="space-y-3">
                            @forelse($student->guardians as $g)
                                <div class="p-4 bg-neutral-50 dark:bg-neutral-800/50 border border-neutral-200 dark:border-neutral-700/50 rounded-xl space-y-1">
                                    <div class="text-sm font-bold text-neutral-900 dark:text-white">{{ $g->guardian_name }} <span class="text-xs font-medium text-neutral-500 ml-1">({{ $g->relation }})</span></div>
                                    <div class="text-sm font-mono text-neutral-600 dark:text-neutral-300 flex items-center gap-2 mt-1">
                                        <svg class="w-3.5 h-3.5 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                        {{ $g->phone }}
                                    </div>
                                </div>
                            @empty
                                <div class="text-sm text-neutral-400 italic">Veli kaydı eklenmedi.</div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Şube / Sınıf Transfer Kartı -->
                    <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-4">
                        <h4 class="text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Şube / Sınıf Transfer Et</h4>
                        <form action="{{ route('admin.students.transfer', $student->id) }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-semibold text-neutral-600 dark:text-neutral-400 mb-1">Hedef Şube</label>
                                    <select name="to_branch_id" required class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary text-sm text-neutral-900 dark:text-white transition-colors">
                                        @foreach($branches as $b)
                                            <option value="{{ $b->id }}" {{ $student->branch_id === $b->id ? 'disabled' : '' }}>{{ $b->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-neutral-600 dark:text-neutral-400 mb-1">Hedef Sınıf</label>
                                    <select name="to_classroom_id" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary text-sm text-neutral-900 dark:text-white transition-colors">
                                        <option value="">Sınıf Seçiniz</option>
                                        @foreach($classrooms as $c)
                                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="w-full py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-xl shadow-sm transition-colors flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                                Transfer İşlemini Başlat
                            </button>
                        </form>
                    </div>

                    <!-- Öğrenci Durum (Mezuniyet/Ayrılış) Kartı -->
                    <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-4 mt-4">
                        <h4 class="text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Öğrenci Durumu</h4>
                        <form action="{{ route('admin.students.status.update', $student->id) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-semibold text-neutral-600 dark:text-neutral-400 mb-1">Mevcut Durum</label>
                                <select name="status" required class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary text-sm text-neutral-900 dark:text-white transition-colors">
                                    <option value="Active" {{ $student->status === 'Active' ? 'selected' : '' }}>Aktif</option>
                                    <option value="Graduated" {{ $student->status === 'Graduated' ? 'selected' : '' }}>Mezun Oldu</option>
                                    <option value="Dropped" {{ $student->status === 'Dropped' ? 'selected' : '' }}>Ayrıldı / İptal</option>
                                    <option value="Suspended" {{ $student->status === 'Suspended' ? 'selected' : '' }}>Donduruldu</option>
                                </select>
                            </div>
                            <button type="submit" class="w-full py-2.5 bg-primary hover:bg-primary-dark text-white text-sm font-semibold rounded-xl shadow-sm transition-colors flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                Durumu Güncelle
                            </button>
                        </form>
                    </div>
                @endif
            </div>

        </div>
    </x-admin.crud.index-layout>
@endsection
