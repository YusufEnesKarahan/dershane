@extends('layouts.admin')
@section('title', 'Sınav Sonuç Girişi')
@section('content')
    <x-admin.crud.index-layout title="Sınav Sonuçları & Net Hesaplama" description="{{ $exam->title }} ({{ $exam->code }}) — {{ \Carbon\Carbon::parse($exam->exam_date)->format('d.m.Y') }}">
        <x-slot name="actions">
            <a href="{{ route('admin.exams.index') }}" class="px-4 py-2 bg-neutral-100 text-neutral-700 text-xs font-semibold rounded-xl hover:bg-neutral-200 transition">
                Sınav Listesine Dön
            </a>
        </x-slot>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Panel: Öğrenci Sonucu Gir -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Öğrenci Sınav Sonucu Ekle</h3>
                
                <x-admin.form.layout :action="route('admin.exams.results.store', $exam->id)" method="POST">
                    
                    <x-admin.form.field-group label="Öğrenci" id="student_id">
                        <select name="student_id" required class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                            @foreach($students as $s)
                                <option value="{{ $s->id }}">{{ $s->full_name }} ({{ $s->student_number }})</option>
                            @endforeach
                        </select>
                    </x-admin.form.field-group>

                    <div class="grid grid-cols-3 gap-2">
                        <x-admin.form.field-group label="Doğru" id="total_correct">
                            <input type="number" name="total_correct" required value="0" min="0" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-2 py-2 font-bold text-green-600">
                        </x-admin.form.field-group>

                        <x-admin.form.field-group label="Yanlış" id="total_wrong">
                            <input type="number" name="total_wrong" required value="0" min="0" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-2 py-2 font-bold text-red-600">
                        </x-admin.form.field-group>

                        <x-admin.form.field-group label="Boş" id="total_empty">
                            <input type="number" name="total_empty" required value="0" min="0" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-2 py-2 font-bold text-neutral-400">
                        </x-admin.form.field-group>
                    </div>

                    <div class="p-3 bg-neutral-50 rounded-lg text-[11px] text-neutral-500 font-mono">
                        * Net hesabı: Doğru - (Yanlış / 4)
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full py-2.5 bg-primary text-white text-xs font-semibold rounded-xl hover:bg-primary-dark transition shadow-sm">
                            Sonucu Kaydet & Net Hesapla
                        </button>
                    </div>

                </x-admin.form.layout>
            </div>

            <!-- Sağ Panel: Sonuç Tablosu ve Sıralamalar -->
            <div class="lg:col-span-2 bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Açıklanan Sonuçlar ve Sıralamalar</h3>
                
                <x-admin.table.layout>
                    <x-slot name="head">
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Öğrenci</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Şube</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">D / Y / B</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Net / Puan</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Şube Derecesi</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Genel Derece</th>
                    </x-slot>
                    <x-slot name="body">
                        @forelse($results as $res)
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40">
                                <td class="px-4 py-3 text-xs font-bold text-neutral-900">
                                    {{ $res->student->full_name }}
                                </td>
                                <td class="px-4 py-3 text-xs text-neutral-500">
                                    {{ $res->student->branch->name ?? '--' }}
                                </td>
                                <td class="px-4 py-3 text-xs font-mono">
                                    <span class="text-green-600 font-bold">{{ $res->total_correct }}D</span> /
                                    <span class="text-red-500 font-bold">{{ $res->total_wrong }}Y</span> /
                                    <span class="text-neutral-400">{{ $res->total_empty }}B</span>
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    <div class="font-bold text-primary">{{ $res->total_net }} Net</div>
                                    <div class="text-[10px] text-neutral-400">{{ $res->score }} Puan</div>
                                </td>
                                <td class="px-4 py-3 text-xs font-bold text-neutral-800">
                                    {{ $res->branch_rank }}. Sıra
                                </td>
                                <td class="px-4 py-3 text-xs font-bold text-amber-600">
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
