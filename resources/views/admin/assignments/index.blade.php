@extends('layouts.admin')
@section('title', 'Ödev & Ödev Takibi')
@section('content')
    <x-admin.crud.index-layout title="Ödev Yönetimi" description="Sınıf veya kurs bazlı ödevler oluşturun, teslim tarihlerini ve öğrenci ödevlerini yönetin.">
        <x-slot name="actions">
            <x-admin.button href="{{ route('admin.assignments.analytics') }}" variant="secondary" icon="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                Ödev Analitikleri
            </x-admin.button>
        </x-slot>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Panel: Yeni Ödev Ekle -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Yeni Ödev Tanımla</h3>
                
                <x-admin.form.layout :action="route('admin.assignments.store')" method="POST">
                    
                    <x-admin.form.field-group label="Ödev Kodu (Benzersiz)" id="code">
                        <input type="text" name="code" required value="ODV-{{ date('Y') }}-001" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors font-mono">
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Ödev Başlığı" id="title">
                        <input type="text" name="title" required placeholder="Örn: 12-A Matematik Türev Problem Seti" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                    </x-admin.form.field-group>

                    <div class="grid grid-cols-2 gap-3">
                        <x-admin.form.field-group label="Atanan Sınıf" id="classroom_id">
                            <select name="classroom_id" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                                <option value="">Tüm Sınıflar</option>
                                @foreach($classrooms as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </x-admin.form.field-group>

                        <x-admin.form.field-group label="Eğitmen" id="teacher_id">
                            <select name="teacher_id" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                                @foreach($teachers as $t)
                                    <option value="{{ $t->id }}">{{ $t->user->name }}</option>
                                @endforeach
                            </select>
                        </x-admin.form.field-group>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <x-admin.form.field-group label="Son Teslim Tarihi" id="due_date">
                            <input type="datetime-local" name="due_date" required value="{{ date('Y-m-d\TH:i', strtotime('+7 days')) }}" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                        </x-admin.form.field-group>

                        <x-admin.form.field-group label="Maksimum Puan" id="max_score">
                            <input type="number" name="max_score" required value="100" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                        </x-admin.form.field-group>
                    </div>

                    <x-admin.form.field-group label="Açıklama / Yönerge" id="description">
                        <textarea name="description" rows="3" placeholder="Ödev detayları ve teslim kuralları..." class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors resize-none"></textarea>
                    </x-admin.form.field-group>

                    <div class="pt-4 border-t border-neutral-100 dark:border-neutral-800 mt-4">
                        <x-admin.button type="submit" variant="primary" icon="M12 4v16m8-8H4" class="w-full justify-center">
                            Ödevi Yayınla & Öğrencilere Duyur
                        </x-admin.button>
                    </div>

                </x-admin.form.layout>
            </div>

            <!-- Sağ Panel: Tanımlı Ödevler -->
            <div class="lg:col-span-2 bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Aktif Ödev Listesi</h3>
                
                <x-admin.table.layout>
                    <x-slot name="head">
                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Kod / Sınıf</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Ödev Başlığı</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Son Teslim</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Teslim Sayısı</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">İşlemler</th>
                    </x-slot>
                    <x-slot name="body">
                        @forelse($assignments as $assignment)
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40">
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-bold font-mono bg-neutral-100 dark:bg-neutral-800 text-neutral-800 dark:text-neutral-200 rounded-md border border-neutral-200 dark:border-neutral-700 shadow-sm w-fit mb-1">
                                            {{ $assignment->code }}
                                        </span>
                                        <div class="text-[11px] text-neutral-500 dark:text-neutral-400 font-medium">{{ $assignment->classroom->name ?? 'Tüm Sınıflar' }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-bold text-sm text-neutral-900 dark:text-white">{{ $assignment->title }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-neutral-50 dark:bg-neutral-800/50 border border-neutral-100 dark:border-neutral-700/50 text-[11px] font-medium text-neutral-600 dark:text-neutral-300 font-mono">
                                        <svg class="w-3.5 h-3.5 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        {{ \Carbon\Carbon::parse($assignment->due_date)->format('d.m.Y H:i') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-primary/10 text-primary border border-primary/20">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        {{ $assignment->submissions_count }} Teslim
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <x-admin.button href="{{ route('admin.assignments.submissions.index', $assignment->id) }}" variant="secondary" class="!px-3 !py-1.5 !text-[11px]">
                                        Teslimler & Puanlama
                                    </x-admin.button>
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
