@extends('layouts.admin')
@section('title', isset($student) ? 'Öğrenci Profili' : 'Yeni Öğrenci Kaydı')
@section('content')
    <x-admin.crud.index-layout title="{{ isset($student) ? 'Öğrenci Profil Kartı' : 'Yeni Öğrenci Oluştur' }}" description="Öğrencinin özlük bilgilerini, veli iletişimini, kurs kayıtlarını ve transfer durumlarını yönetin.">
        <x-slot name="actions">
            <a href="{{ route('admin.students.index') }}" class="px-4 py-2 bg-neutral-100 text-neutral-700 text-xs font-semibold rounded-xl hover:bg-neutral-200 transition">
                Listeye Geri Dön
            </a>
        </x-slot>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Panel: Temel Bilgiler -->
            <div class="lg:col-span-2 bg-white dark:bg-neutral-900 p-8 rounded-2xl border border-neutral-100 shadow-premium-sm space-y-6">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Öğrenci Kimlik & Özlük Bilgileri</h3>
                
                <x-admin.form.layout :action="isset($student) ? route('admin.students.update', $student->id) : route('admin.students.store')" method="POST">
                    @if(isset($student))
                        @method('PUT')
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <x-admin.form.field-group label="Öğrenci No (Benzersiz)" id="student_number">
                            <input type="text" name="student_number" required value="{{ $student->student_number ?? 'OGR-' . rand(1000, 9999) }}" {{ isset($student) ? 'disabled' : '' }} class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                        </x-admin.form.field-group>

                        <x-admin.form.field-group label="Adı" id="first_name">
                            <input type="text" name="first_name" required value="{{ $student->first_name ?? '' }}" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                        </x-admin.form.field-group>

                        <x-admin.form.field-group label="Soyadı" id="last_name">
                            <input type="text" name="last_name" required value="{{ $student->last_name ?? '' }}" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                        </x-admin.form.field-group>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <x-admin.form.field-group label="TC / Pasaport No" id="identity_number">
                            <input type="text" name="identity_number" value="{{ $student->identity_number ?? '' }}" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                        </x-admin.form.field-group>

                        <x-admin.form.field-group label="Kayıtlı Şube" id="branch_id">
                            <select name="branch_id" required class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                                @foreach($branches as $b)
                                    <option value="{{ $b->id }}" {{ (isset($student) && $student->branch_id === $b->id) ? 'selected' : '' }}>{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </x-admin.form.field-group>

                        <x-admin.form.field-group label="Atandığı Sınıf" id="classroom_id">
                            <select name="classroom_id" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                                <option value="">Sınıf Seçiniz</option>
                                @foreach($classrooms as $c)
                                    <option value="{{ $c->id }}" {{ (isset($student) && $student->classroom_id === $c->id) ? 'selected' : '' }}>{{ $c->name }} ({{ $c->code }})</option>
                                @endforeach
                            </select>
                        </x-admin.form.field-group>
                    </div>

                    @if(!isset($student))
                        <div class="p-4 bg-neutral-50 rounded-xl space-y-3">
                            <h4 class="text-xs font-bold text-neutral-700">Veli Bilgileri (Hızlı Ekleme)</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <input type="text" name="guardian_name" placeholder="Veli Ad Soyad" class="text-xs bg-white border rounded-lg px-3 py-2">
                                <input type="text" name="guardian_relation" placeholder="Yakınlık (Örn: Baba)" class="text-xs bg-white border rounded-lg px-3 py-2">
                                <input type="text" name="guardian_phone" placeholder="Veli Telefon" class="text-xs bg-white border rounded-lg px-3 py-2">
                            </div>
                        </div>
                    @endif

                    <div class="pt-4 flex items-center justify-between border-t">
                        <button type="submit" class="px-4 py-2 bg-primary text-white text-sm font-medium rounded-xl hover:bg-primary-dark transition shadow-sm">
                            Profil Bilgilerini Kaydet
                        </button>
                    </div>

                </x-admin.form.layout>

                @if(isset($student))
                    <!-- Kurs Kaydı Kartı -->
                    <div class="pt-6 border-t space-y-4">
                        <h4 class="text-xs font-bold text-neutral-800 uppercase tracking-wider">Kayıtlı Olunan Kurslar</h4>
                        <div class="space-y-2">
                            @forelse($student->enrollments as $enr)
                                <div class="p-3 bg-neutral-50 rounded-xl flex items-center justify-between text-xs">
                                    <span class="font-bold text-neutral-900">{{ $enr->course->name }}</span>
                                    <span class="text-neutral-500">{{ number_format($enr->price_paid, 2) }} TL</span>
                                </div>
                            @empty
                                <div class="text-xs text-neutral-400">Henüz bir kursa kayıt yapılmamış.</div>
                            @endforelse
                        </div>

                        <form action="{{ route('admin.students.enrollment.store') }}" method="POST" class="flex gap-2 pt-2">
                            @csrf
                            <input type="hidden" name="student_id" value="{{ $student->id }}">
                            <select name="course_id" required class="text-xs bg-neutral-50 border rounded-lg px-3 py-2 flex-1">
                                <option value="">Kurs Seçip Ekle</option>
                                @foreach($courses as $co)
                                    <option value="{{ $co->id }}">{{ $co->name }} ({{ number_format($co->currentPricing?->price ?? 0, 2) }} TL)</option>
                                @endforeach
                            </select>
                            <button type="submit" class="px-3 py-2 bg-green-600 text-white text-xs font-bold rounded-lg hover:bg-green-700">Kursa Kaydet</button>
                        </form>
                    </div>
                @endif
            </div>

            <!-- Sağ Panel: Veli ve Transfer İşlemleri -->
            <div class="space-y-6">
                @if(isset($student))
                    <!-- Veli Kartı -->
                    <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm space-y-3">
                        <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider">Veli / İletişim Detayları</h4>
                        @forelse($student->guardians as $g)
                            <div class="p-3 bg-neutral-50 rounded-xl text-xs space-y-1">
                                <div class="font-bold text-neutral-900">{{ $g->guardian_name }} ({{ $g->relation }})</div>
                                <div class="text-neutral-500">Tel: {{ $g->phone }}</div>
                            </div>
                        @empty
                            <div class="text-xs text-neutral-400">Veli kaydı eklenmedi.</div>
                        @endforelse
                    </div>

                    <!-- Şube / Sınıf Transfer Kartı -->
                    <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm space-y-3">
                        <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider">Şube / Sınıf Transfer Et</h4>
                        <form action="{{ route('admin.students.transfer', $student->id) }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <label class="text-[10px] font-bold text-neutral-500">Hedef Şube</label>
                                <select name="to_branch_id" required class="w-full text-xs bg-neutral-50 border rounded-lg p-2">
                                    @foreach($branches as $b)
                                        <option value="{{ $b->id }}" {{ $student->branch_id === $b->id ? 'disabled' : '' }}>{{ $b->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-neutral-500">Hedef Sınıf</label>
                                <select name="to_classroom_id" class="w-full text-xs bg-neutral-50 border rounded-lg p-2">
                                    <option value="">Sınıf Seçiniz</option>
                                    @foreach($classrooms as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="w-full py-2 bg-amber-600 text-white text-xs font-bold rounded-lg hover:bg-amber-700">Transfer İşlemini Başlat</button>
                        </form>
                    </div>
                @endif
            </div>

        </div>
    </x-admin.crud.index-layout>
@endsection
