@extends('layouts.admin')
@section('title', 'Sınav Sonuç Girişi')
@section('content')
    <x-admin.crud.index-layout title="Sınav Sonuçları & Net Hesaplama" description="{{ $exam->title }} ({{ $exam->code }}) — {{ \Carbon\Carbon::parse($exam->exam_date)->format('d.m.Y') }}">
        <x-slot name="actions">
            <x-admin.button href="{{ route('admin.exams.index') }}" variant="secondary">
                Sınav Listesine Dön
            </x-admin.button>
        </x-slot>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Panel: Öğrenci Sonucu Gir -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Öğrenci Sınav Sonucu Ekle</h3>
                
                <x-admin.form.layout :action="route('admin.exams.results.store', $exam->id)" method="POST">
                    
                    <x-admin.form.field-group label="Öğrenci" id="student_id">
                        <select name="student_id" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                            @foreach($students as $s)
                                <option value="{{ $s->id }}">{{ $s->full_name }} ({{ $s->student_number }})</option>
                            @endforeach
                        </select>
                    </x-admin.form.field-group>

                    <div class="grid grid-cols-3 gap-2">
                        <x-admin.form.field-group label="Doğru" id="total_correct">
                            <input type="number" name="total_correct" required value="0" min="0" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 font-bold text-emerald-600 focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                        </x-admin.form.field-group>

                        <x-admin.form.field-group label="Yanlış" id="total_wrong">
                            <input type="number" name="total_wrong" required value="0" min="0" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 font-bold text-red-600 focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                        </x-admin.form.field-group>

                        <x-admin.form.field-group label="Boş" id="total_empty">
                            <input type="number" name="total_empty" required value="0" min="0" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 font-bold text-neutral-500 focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                        </x-admin.form.field-group>
                    </div>

                    <div class="p-3 bg-neutral-50 dark:bg-neutral-800/50 rounded-xl border border-neutral-100 dark:border-neutral-800 text-[11px] text-neutral-500 font-mono">
                        * Net hesabı: Doğru - (Yanlış / 4)
                    </div>

                    <div class="pt-4">
                        <x-admin.button type="submit" variant="primary" class="w-full justify-center">
                            Sonucu Kaydet & Net Hesapla
                        </x-admin.button>
                    </div>

                </x-admin.form.layout>
            </div>

            <!-- Sağ Panel: Sonuç Tablosu ve Sıralamalar -->
            <div class="lg:col-span-2 bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Açıklanan Sonuçlar ve Sıralamalar</h3>
                
                <x-admin.table.layout>
                    <x-slot name="head">
                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Öğrenci</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Şube</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">D / Y / B</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Net / Puan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Şube Derecesi</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Genel Derece</th>
                    </x-slot>
                    <x-slot name="body">
                        @forelse($results as $res)
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40">
                                <td class="px-6 py-4 text-sm font-bold text-neutral-900 dark:text-white">
                                    {{ $res->student->full_name }}
                                </td>
                                <td class="px-6 py-4 text-sm text-neutral-500">
                                    {{ $res->student->branch->name ?? '--' }}
                                </td>
                                <td class="px-6 py-4 text-sm font-mono">
                                    <span class="text-emerald-600 font-bold">{{ $res->total_correct }}D</span> /
                                    <span class="text-red-500 font-bold">{{ $res->total_wrong }}Y</span> /
                                    <span class="text-neutral-500">{{ $res->total_empty }}B</span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="font-bold text-primary">{{ $res->total_net }} Net</div>
                                    <div class="text-[11px] text-neutral-500">{{ $res->score }} Puan</div>
                                </td>
                                <td class="px-6 py-4 text-sm font-bold text-neutral-800 dark:text-neutral-200">
                                    {{ $res->branch_rank }}. Sıra
                                </td>
                                <td class="px-6 py-4 text-sm font-bold text-amber-600 dark:text-amber-500">
                                    {{ $res->global_rank }}. Sıra
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-xs text-neutral-400">Henüz sonuç girişi yapılmamıştır.</td>
                            </tr>
                        @endforelse
                    </x-slot>
                </x-admin.table.layout>
            </div>

        </div>
    </x-admin.crud.index-layout>
@endsection
