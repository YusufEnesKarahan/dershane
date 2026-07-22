@extends('layouts.admin')
@section('title', 'Eğitmen & Öğretmen Yönetimi')
@section('content')
    <x-admin.crud.index-layout title="Öğretmen / Personel Yönetimi" description="Kurum eğitmenlerini, şube ve uzmanlık alanlarını yönetin, portal profillerini ve performans değerlendirmelerini takip edin.">
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Panel: Yeni Öğretmen Kaydet -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Yeni Eğitmen Tanımla</h3>
                
                <x-admin.form.layout :action="route('admin.teachers.store')" method="POST">
                    
                    <x-admin.form.field-group label="Kullanıcı Seçimi" id="user_id">
                        <select name="user_id" required class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                            @endforeach
                        </select>
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Branş / Şube" id="branch_id">
                        <select name="branch_id" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                            <option value="">Merkez / HQ</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}">{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Ünvan" id="title">
                        <input type="text" name="title" required placeholder="Örn: Matematik Zümre Başkanı" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Uzmanlık Alanları" id="specialties">
                        <input type="text" name="specialties" required placeholder="Örn: TYT Geometri, AYT Analitik" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Mezuniyet / Eğitim" id="education">
                        <input type="text" name="education" placeholder="Örn: Boğaziçi Üniversitesi Matematik Öğretmenliği" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Deneyim Yılı" id="experience_years">
                        <input type="number" name="experience_years" min="0" value="5" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                    </x-admin.form.field-group>

                    <div class="pt-4">
                        <button type="submit" class="w-full py-2.5 bg-primary text-white text-xs font-semibold rounded-xl hover:bg-primary-dark transition shadow-sm">
                            Öğretmen Kaydını Tamamla
                        </button>
                    </div>

                </x-admin.form.layout>
            </div>

            <!-- Sağ Panel: Öğretmen Listesi -->
            <div class="lg:col-span-2 bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Kurum Öğretmenleri</h3>
                
                <x-admin.table.layout>
                    <x-slot name="head">
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Öğretmen / Ünvan</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Uzmanlık</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Deneyim</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">İşlemler</th>
                    </x-slot>
                    <x-slot name="body">
                        @forelse($teachers as $t)
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40">
                                <td class="px-4 py-3 text-xs">
                                    <span class="font-bold text-neutral-900">{{ $t->user->name }}</span>
                                    <div class="text-[10px] text-neutral-400 mt-0.5">{{ $t->title }}</div>
                                </td>
                                <td class="px-4 py-3 text-xs text-neutral-600 font-semibold">
                                    {{ $t->specialties }}
                                </td>
                                <td class="px-4 py-3 text-xs text-neutral-500">
                                    {{ $t->experience_years }} Yıl
                                </td>
                                <td class="px-4 py-3 text-xs flex items-center gap-2">
                                    <a href="{{ route('admin.teachers.edit', $t->id) }}" class="text-primary hover:underline font-bold">Düzenle</a>
                                    <a href="{{ route('admin.teachers.performance', $t->id) }}" class="text-green-600 hover:underline font-bold">Performans</a>
                                    <a href="{{ route('admin.teachers.analytics', $t->id) }}" class="text-amber-600 hover:underline font-bold">Analiz</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-xs text-neutral-400">Henüz kayıtlı öğretmen bulunmamaktadır.</td>
                            </tr>
                        @endforelse
                    </x-slot>
                </x-admin.table.layout>
            </div>

        </div>
    </x-admin.crud.index-layout>
@endsection
