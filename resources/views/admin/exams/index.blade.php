@extends('layouts.admin')
@section('title', 'Sınav & Ölçme Değerlendirme')
@section('content')
    <x-admin.crud.index-layout title="Sınav Yönetimi" description="Kurumsal deneme, TYT, AYT ve konu tarama sınavlarını tanımlayın ve sonuçlarını işleyin.">
        <x-slot name="actions">
            <x-admin.button href="{{ route('admin.exams.analytics') }}" variant="secondary">
                Sınav Analizleri & Sıralama
            </x-admin.button>
        </x-slot>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Panel: Yeni Sınav Tanımlama -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Yeni Sınav Tanımla</h3>
                
                <x-admin.form.layout :action="route('admin.exams.store')" method="POST">
                    
                    <x-admin.form.field-group label="Sınav Kodu (Benzersiz)" id="code">
                        <input type="text" name="code" required value="SNV-{{ date('Y') }}-001" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Sınav Adı" id="title">
                        <input type="text" name="title" required placeholder="Örn: YKS Genel Deneme Sınavı - 1" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                    </x-admin.form.field-group>

                    <div class="grid grid-cols-2 gap-3">
                        <x-admin.form.field-group label="Sınav Türü" id="exam_type">
                            <select name="exam_type" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                                <option value="TYT">TYT Deneme</option>
                                <option value="AYT">AYT Deneme</option>
                                <option value="Trial">Kurum İçi Deneme</option>
                                <option value="Subject">Konu Taraması</option>
                            </select>
                        </x-admin.form.field-group>

                        <x-admin.form.field-group label="Tarih" id="exam_date">
                            <input type="date" name="exam_date" required value="{{ date('Y-m-d') }}" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                        </x-admin.form.field-group>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <x-admin.form.field-group label="Soru Sayısı" id="total_questions">
                            <input type="number" name="total_questions" required value="120" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                        </x-admin.form.field-group>

                        <x-admin.form.field-group label="Süre (Dk)" id="duration_minutes">
                            <input type="number" name="duration_minutes" required value="135" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                        </x-admin.form.field-group>
                    </div>

                    <div class="pt-4">
                        <x-admin.button type="submit" variant="primary" class="w-full justify-center">
                            Sınavı Kaydet & Sonuç Girişi Başlat
                        </x-admin.button>
                    </div>

                </x-admin.form.layout>
            </div>

            <!-- Sağ Panel: Tanımlı Sınavlar -->
            <div class="lg:col-span-2 bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Tanımlı Sınav Listesi</h3>
                
                <x-admin.table.layout>
                    <x-slot name="head">
                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Kod / Tür</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Sınav Adı</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Tarih</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Kayıtlı Sonuç</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">İşlemler</th>
                    </x-slot>
                    <x-slot name="body">
                        @forelse($exams as $exam)
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40">
                                <td class="px-6 py-4 text-sm font-bold text-neutral-900 dark:text-white">
                                    {{ $exam->code }}
                                    <div class="text-[11px] text-neutral-500 font-normal mt-0.5">{{ $exam->exam_type }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-neutral-800 dark:text-neutral-200">
                                    {{ $exam->title }}
                                </td>
                                <td class="px-6 py-4 text-sm text-neutral-500">
                                    {{ \Carbon\Carbon::parse($exam->exam_date)->format('d.m.Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm font-bold text-primary">
                                    {{ $exam->results_count }} Öğrenci
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <x-admin.button href="{{ route('admin.exams.results.index', $exam->id) }}" variant="secondary" size="sm">
                                        Sonuçlar & Sıralama
                                    </x-admin.button>
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
