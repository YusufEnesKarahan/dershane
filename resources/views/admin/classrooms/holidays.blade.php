@extends('layouts.admin')
@section('title', 'Tatil Günleri')
@section('content')
    <x-admin.crud.index-layout title="Resmi & Kurumsal Tatiller" description="Tatil tarihlerinde çakışan ders yapılmasını engelleyen tatil günlerini tanımlayın.">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white mb-4">Yeni Tatil Tanımla</h3>
                <x-admin.form.layout :action="route('admin.classrooms.holidays.store')" method="POST">
                    
                    <x-admin.form.field-group label="Tatil Adı" id="name">
                        <input type="text" name="name" required placeholder="Örn: 29 Ekim Cumhuriyet Bayramı" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                    </x-admin.form.field-group>
                    
                    <div class="grid grid-cols-2 gap-3 mt-4">
                        <x-admin.form.field-group label="Başlangıç" id="start_date">
                            <input type="date" name="start_date" required class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                        </x-admin.form.field-group>
                        <x-admin.form.field-group label="Bitiş" id="end_date">
                            <input type="date" name="end_date" required class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                        </x-admin.form.field-group>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="px-4 py-2 bg-primary text-white text-xs font-semibold rounded-lg hover:bg-primary-dark transition shadow-sm">
                            Tatili Kaydet
                        </button>
                    </div>

                </x-admin.form.layout>
            </div>

            <div class="lg:col-span-2 bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Kayıtlı Tatil Takvimi</h3>
                <div class="divide-y divide-neutral-100">
                    @forelse($holidays as $holiday)
                        <div class="py-3 flex items-center justify-between text-xs">
                            <div>
                                <span class="font-bold text-neutral-900">{{ $holiday->name }}</span>
                                <span class="text-neutral-500 ml-2">({{ \Carbon\Carbon::parse($holiday->start_date)->format('d.m.Y') }} - {{ \Carbon\Carbon::parse($holiday->end_date)->format('d.m.Y') }})</span>
                            </div>
                            <span class="px-2 py-0.5 text-[10px] font-bold bg-amber-100 text-amber-800 rounded-full">Ders Yapılamaz</span>
                        </div>
                    @empty
                        <div class="text-center text-sm text-neutral-400 py-8">Tanımlı tatil bulunmuyor.</div>
                    @endforelse
                </div>
            </div>

        </div>
    </x-admin.crud.index-layout>
@endsection
