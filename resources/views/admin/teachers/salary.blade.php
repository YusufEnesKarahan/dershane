@extends('layouts.admin')
@section('title', 'Maaş Yapılandırması')
@section('content')
    <x-admin.crud.index-layout title="Özlük Maaş Tanımları" description="Eğitmenlerinizin aylık baz maaşlarını, ödeme türlerini, prim ve kesinti oranlarını yapılandırın.">
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Panel: Maaş Atama Formu -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm flex flex-col">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Maaş Tanımla
                </h3>
                <x-admin.form.layout :action="route('admin.teachers.salary.store')" method="POST">
                    <x-admin.form.field-group label="Eğitmen" id="teacher_id">
                        <select name="teacher_id" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 focus:ring-violet-500 focus:border-violet-500 dark:text-white transition-colors">
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}">{{ $t->user->name }}</option>
                            @endforeach
                        </select>
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Baz Maaş (TL)" id="base_salary">
                        <input type="number" name="base_salary" required value="45000" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 focus:ring-violet-500 focus:border-violet-500 dark:text-white transition-colors">
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Ödeme Türü" id="payment_type">
                        <select name="payment_type" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 focus:ring-violet-500 focus:border-violet-500 dark:text-white transition-colors">
                            <option value="Monthly">Monthly (Aylık)</option>
                            <option value="Hourly">Hourly (Ders Başı Ücret)</option>
                        </select>
                    </x-admin.form.field-group>

                    <div class="grid grid-cols-2 gap-4">
                        <x-admin.form.field-group label="Ek Prim" id="bonus">
                            <input type="number" name="bonus" value="0" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 focus:ring-violet-500 focus:border-violet-500 dark:text-white transition-colors">
                        </x-admin.form.field-group>

                        <x-admin.form.field-group label="Kesintiler" id="deductions">
                            <input type="number" name="deductions" value="0" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 focus:ring-violet-500 focus:border-violet-500 dark:text-white transition-colors">
                        </x-admin.form.field-group>
                    </div>

                    <div class="pt-4 mt-auto">
                        <button type="submit" class="w-full py-2.5 bg-violet-600 text-white text-sm font-bold rounded-xl hover:bg-violet-500 transition-colors shadow-lg shadow-violet-900/20 border border-violet-500/50 flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            Maaş Profilini Güncelle
                        </button>
                    </div>
                </x-admin.form.layout>
            </div>

            <!-- Sağ Panel: Eğitmen Maaş Özetleri -->
            <div class="lg:col-span-2 space-y-6 flex flex-col h-full">
                <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm flex-1 overflow-hidden flex flex-col">
                    <div class="p-6 border-b border-neutral-100 dark:border-neutral-800/50 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-neutral-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                            Güncel Maaş Listesi
                        </h3>
                    </div>
                    
                    <div class="p-0 overflow-y-auto flex-1">
                        <div class="divide-y divide-neutral-100 dark:divide-neutral-800/50">
                            @foreach($teachers as $t)
                                <div class="px-6 py-4 flex items-center justify-between hover:bg-neutral-50 dark:hover:bg-neutral-800/40 transition-colors group">
                                    <div>
                                        <div class="text-sm font-bold text-neutral-900 dark:text-white">{{ $t->user->name }}</div>
                                        <div class="text-[11px] font-medium text-neutral-500 dark:text-neutral-400 mt-1 flex items-center gap-1.5">
                                            Tür: {{ $t->salaryProfile ? $t->salaryProfile->payment_type : 'Tanımsız' }}
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded bg-neutral-100 dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300 text-sm font-bold border border-neutral-200 dark:border-neutral-700 font-mono">
                                        {{ $t->salaryProfile ? number_format($t->salaryProfile->base_salary, 2) . ' TL' : 'Tanımsız' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </x-admin.crud.index-layout>
@endsection
