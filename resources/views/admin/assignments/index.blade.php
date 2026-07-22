@extends('layouts.admin')
@section('title', 'Ödev & Ödev Takibi')
@section('content')
    <x-admin.crud.index-layout title="Ödev Yönetimi" description="Sınıf veya kurs bazlı ödevler oluşturun, teslim tarihlerini ve öğrenci ödevlerini yönetin.">
        <x-slot name="actions">
            <a href="{{ route('admin.assignments.analytics') }}" class="px-4 py-2 bg-neutral-100 text-neutral-700 text-xs font-semibold rounded-xl hover:bg-neutral-200 transition">
                Ödev Analitikleri
            </a>
        </x-slot>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Panel: Yeni Ödev Ekle -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Yeni Ödev Tanımla</h3>
                
                <x-admin.form.layout :action="route('admin.assignments.store')" method="POST">
                    
                    <x-admin.form.field-group label="Ödev Kodu (Benzersiz)" id="code">
                        <input type="text" name="code" required value="ODV-{{ date('Y') }}-001" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Ödev Başlığı" id="title">
                        <input type="text" name="title" required placeholder="Örn: 12-A Matematik Türev Problem Seti" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                    </x-admin.form.field-group>

                    <div class="grid grid-cols-2 gap-3">
                        <x-admin.form.field-group label="Atanan Sınıf" id="classroom_id">
                            <select name="classroom_id" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                                <option value="">Tüm Sınıflar</option>
                                @foreach($classrooms as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </x-admin.form.field-group>

                        <x-admin.form.field-group label="Eğitmen" id="teacher_id">
                            <select name="teacher_id" required class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                                @foreach($teachers as $t)
                                    <option value="{{ $t->id }}">{{ $t->user->name }}</option>
                                @endforeach
                            </select>
                        </x-admin.form.field-group>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <x-admin.form.field-group label="Son Teslim Tarihi" id="due_date">
                            <input type="datetime-local" name="due_date" required value="{{ date('Y-m-d\TH:i', strtotime('+7 days')) }}" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                        </x-admin.form.field-group>

                        <x-admin.form.field-group label="Maksimum Puan" id="max_score">
                            <input type="number" name="max_score" required value="100" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                        </x-admin.form.field-group>
                    </div>

                    <x-admin.form.field-group label="Açıklama / Yönerge" id="description">
                        <textarea name="description" rows="3" placeholder="Ödev detayları ve teslim kuralları..." class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2"></textarea>
                    </x-admin.form.field-group>

                    <div class="pt-4">
                        <button type="submit" class="w-full py-2.5 bg-primary text-white text-xs font-semibold rounded-xl hover:bg-primary-dark transition shadow-sm">
                            Ödevi Yayınla & Öğrencilere Duyur
                        </button>
                    </div>

                </x-admin.form.layout>
            </div>

            <!-- Sağ Panel: Tanımlı Ödevler -->
            <div class="lg:col-span-2 bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Aktif Ödev Listesi</h3>
                
                <x-admin.table.layout>
                    <x-slot name="head">
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Kod / Sınıf</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Ödev Başlığı</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Son Teslim</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Teslim Sayısı</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">İşlemler</th>
                    </x-slot>
                    <x-slot name="body">
                        @forelse($assignments as $assignment)
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40">
                                <td class="px-4 py-3 text-xs font-bold text-neutral-900">
                                    {{ $assignment->code }}
                                    <div class="text-[10px] text-neutral-400 font-normal">{{ $assignment->classroom->name ?? 'Tüm Sınıflar' }}</div>
                                </td>
                                <td class="px-4 py-3 text-xs font-semibold text-neutral-800">
                                    {{ $assignment->title }}
                                </td>
                                <td class="px-4 py-3 text-xs text-neutral-500 font-mono">
                                    {{ \Carbon\Carbon::parse($assignment->due_date)->format('d.m.Y H:i') }}
                                </td>
                                <td class="px-4 py-3 text-xs font-bold text-primary">
                                    {{ $assignment->submissions_count }} Teslim
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    <a href="{{ route('admin.assignments.submissions.index', $assignment->id) }}" class="px-3 py-1 bg-primary/10 text-primary text-[11px] font-bold rounded-lg hover:bg-primary/20 transition">
                                        Teslimler & Puanlama
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-xs text-neutral-400">Henüz tanımlı ödev bulunmamaktadır.</td>
                            </tr>
                        @endforelse
                    </x-slot>
                </x-admin.table.layout>
            </div>

        </div>
    </x-admin.crud.index-layout>
@endsection
