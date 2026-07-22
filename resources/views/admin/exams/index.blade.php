@extends('layouts.admin')
@section('title', 'Sınav & Ölçme Değerlendirme')
@section('content')
    <x-admin.crud.index-layout title="Sınav Yönetimi" description="Kurumsal deneme, TYT, AYT ve konu tarama sınavlarını tanımlayın ve sonuçlarını işleyin.">
        <x-slot name="actions">
            <a href="{{ route('admin.exams.analytics') }}" class="px-4 py-2 bg-neutral-100 text-neutral-700 text-xs font-semibold rounded-xl hover:bg-neutral-200 transition">
                Sınav Analizleri & Sıralama
            </a>
        </x-slot>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Panel: Yeni Sınav Tanımlama -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Yeni Sınav Tanımla</h3>
                
                <x-admin.form.layout :action="route('admin.exams.store')" method="POST">
                    
                    <x-admin.form.field-group label="Sınav Kodu (Benzersiz)" id="code">
                        <input type="text" name="code" required value="SNV-{{ date('Y') }}-001" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Sınav Adı" id="title">
                        <input type="text" name="title" required placeholder="Örn: YKS Genel Deneme Sınavı - 1" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                    </x-admin.form.field-group>

                    <div class="grid grid-cols-2 gap-3">
                        <x-admin.form.field-group label="Sınav Türü" id="exam_type">
                            <select name="exam_type" required class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                                <option value="TYT">TYT Deneme</option>
                                <option value="AYT">AYT Deneme</option>
                                <option value="Trial">Kurum İçi Deneme</option>
                                <option value="Subject">Konu Taraması</option>
                            </select>
                        </x-admin.form.field-group>

                        <x-admin.form.field-group label="Tarih" id="exam_date">
                            <input type="date" name="exam_date" required value="{{ date('Y-m-d') }}" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                        </x-admin.form.field-group>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <x-admin.form.field-group label="Soru Sayısı" id="total_questions">
                            <input type="number" name="total_questions" required value="120" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                        </x-admin.form.field-group>

                        <x-admin.form.field-group label="Süre (Dk)" id="duration_minutes">
                            <input type="number" name="duration_minutes" required value="135" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                        </x-admin.form.field-group>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full py-2.5 bg-primary text-white text-xs font-semibold rounded-xl hover:bg-primary-dark transition shadow-sm">
                            Sınavı Kaydet & Sonuç Girişi Başlat
                        </button>
                    </div>

                </x-admin.form.layout>
            </div>

            <!-- Sağ Panel: Tanımlı Sınavlar -->
            <div class="lg:col-span-2 bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Tanımlı Sınav Listesi</h3>
                
                <x-admin.table.layout>
                    <x-slot name="head">
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Kod / Tür</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Sınav Adı</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Tarih</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Kayıtlı Sonuç</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">İşlemler</th>
                    </x-slot>
                    <x-slot name="body">
                        @forelse($exams as $exam)
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40">
                                <td class="px-4 py-3 text-xs font-bold text-neutral-900">
                                    {{ $exam->code }}
                                    <div class="text-[10px] text-neutral-400 font-normal">{{ $exam->exam_type }}</div>
                                </td>
                                <td class="px-4 py-3 text-xs font-semibold text-neutral-800">
                                    {{ $exam->title }}
                                </td>
                                <td class="px-4 py-3 text-xs text-neutral-500">
                                    {{ \Carbon\Carbon::parse($exam->exam_date)->format('d.m.Y') }}
                                </td>
                                <td class="px-4 py-3 text-xs font-bold text-primary">
                                    {{ $exam->results_count }} Öğrenci
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    <a href="{{ route('admin.exams.results.index', $exam->id) }}" class="px-3 py-1 bg-primary/10 text-primary text-[11px] font-bold rounded-lg hover:bg-primary/20 transition">
                                        Sonuçlar & Sıralama
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-xs text-neutral-400">Henüz sınav kaydı bulunmamaktadır.</td>
                            </tr>
                        @endforelse
                    </x-slot>
                </x-admin.table.layout>
            </div>

        </div>
    </x-admin.crud.index-layout>
@endsection
