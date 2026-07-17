@extends('layouts.admin')
@section('title', 'Sözleşmeler')
@section('content')
    <x-admin.crud.index-layout title="İstihdam Sözleşmeleri" description="Eğitmenlerinizin işe başlama/bitiş tarihlerini ve çalışma sözleşmelerini tanımlayın.">
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Panel: Yeni Sözleşme Atama Formu -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white mb-4">Sözleşme Oluştur</h3>
                <x-admin.form.layout :action="route('admin.teachers.contracts.store')" method="POST">
                    <x-admin.form.field-group label="Eğitmen" id="teacher_id">
                        <select name="teacher_id" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2">
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}">{{ $t->user->name }}</option>
                            @endforeach
                        </select>
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Sözleşme Başlangıç Tarihi" id="start_date">
                        <input type="date" name="start_date" required value="{{ date('Y-m-d') }}" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2">
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Sözleşme Bitiş Tarihi" id="end_date">
                        <input type="date" name="end_date" value="{{ date('Y-m-d', strtotime('+1 year')) }}" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2">
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="İstihdam Türü" id="employment_type">
                        <select name="employment_type" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2">
                            <option value="Full-time">Full-time (Tam Zamanlı)</option>
                            <option value="Part-time">Part-time (Yarı Zamanlı)</option>
                            <option value="Contract">Freelance / Sözleşmeli</option>
                        </select>
                    </x-admin.form.field-group>

                    <div class="pt-4">
                        <button type="submit" class="w-full px-4 py-2 bg-primary text-white text-xs font-semibold rounded-lg hover:bg-primary-dark transition shadow-sm">
                            Sözleşmeyi İmzala
                        </button>
                    </div>
                </x-admin.form.layout>
            </div>

            <!-- Sağ Panel: Sözleşme Geçmişi -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                    <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Aktif Sözleşmeler</h3>
                    
                    <div class="divide-y divide-neutral-100">
                        @foreach($teachers as $t)
                            <div class="py-3 flex items-center justify-between text-xs">
                                <div>
                                    <span class="font-semibold text-neutral-800">{{ $t->user->name }}</span>
                                    @if($t->contracts->count() > 0)
                                        <div class="text-[10px] text-neutral-400 mt-0.5">
                                            Başlangıç: {{ $t->contracts->last()->start_date->format('d.m.Y') }}
                                            @if($t->contracts->last()->end_date)
                                                | Bitiş: {{ $t->contracts->last()->end_date->format('d.m.Y') }}
                                            @endif
                                        </div>
                                    @else
                                        <div class="text-[10px] text-neutral-400 mt-0.5">Sözleşme kaydı bulunmuyor.</div>
                                    @endif
                                </div>
                                <span class="px-2 py-0.5 text-[9px] bg-green-100 text-green-800 font-bold rounded">
                                    {{ $t->contracts->count() > 0 ? $t->contracts->last()->employment_type : 'Yok' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </x-admin.crud.index-layout>
@endsection
