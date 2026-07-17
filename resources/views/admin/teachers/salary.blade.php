@extends('layouts.admin')
@section('title', 'Maaş Yapılandırması')
@section('content')
    <x-admin.crud.index-layout title="Özlük Maaş Tanımları" description="Eğitmenlerinizin aylık baz maaşlarını, ödeme türlerini, prim ve kesinti oranlarını yapılandırın.">
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Panel: Maaş Atama Formu -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white mb-4">Maaş Tanımla</h3>
                <x-admin.form.layout :action="route('admin.teachers.salary.store')" method="POST">
                    <x-admin.form.field-group label="Eğitmen" id="teacher_id">
                        <select name="teacher_id" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2">
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}">{{ $t->user->name }}</option>
                            @endforeach
                        </select>
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Baz Maaş (TL)" id="base_salary">
                        <input type="number" name="base_salary" required value="45000" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2">
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Ödeme Türü" id="payment_type">
                        <select name="payment_type" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2">
                            <option value="Monthly">Monthly (Aylık)</option>
                            <option value="Hourly">Hourly (Ders Başı Ücret)</option>
                        </select>
                    </x-admin.form.field-group>

                    <div class="grid grid-cols-2 gap-4">
                        <x-admin.form.field-group label="Ek Prim" id="bonus">
                            <input type="number" name="bonus" value="0" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2">
                        </x-admin.form.field-group>

                        <x-admin.form.field-group label="Kesintiler" id="deductions">
                            <input type="number" name="deductions" value="0" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg px-3 py-2">
                        </x-admin.form.field-group>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full px-4 py-2 bg-primary text-white text-xs font-semibold rounded-lg hover:bg-primary-dark transition shadow-sm">
                            Maaş Profilini Güncelle
                        </button>
                    </div>
                </x-admin.form.layout>
            </div>

            <!-- Sağ Panel: Eğitmen Maaş Özetleri -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                    <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Güncel Maaş Listesi</h3>
                    
                    <div class="divide-y divide-neutral-100">
                        @foreach($teachers as $t)
                            <div class="py-3 flex items-center justify-between text-xs">
                                <div>
                                    <span class="font-semibold text-neutral-800">{{ $t->user->name }}</span>
                                    <div class="text-[10px] text-neutral-400 mt-0.5">Tür: {{ $t->salaryProfile ? $t->salaryProfile->payment_type : 'Tanımsız' }}</div>
                                </div>
                                <span class="font-bold text-neutral-900">
                                    {{ $t->salaryProfile ? number_format($t->salaryProfile->base_salary, 2) . ' TL' : 'Maaş Tanımlanmamış' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </x-admin.crud.index-layout>
@endsection
