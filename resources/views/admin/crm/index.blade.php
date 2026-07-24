@extends('layouts.admin')
@section('title', 'Aday Öğrenci Listesi (CRM)')
@section('content')
    <x-admin.crud.index-layout title="Aday Öğrenci Yönetimi" description="Yeni aday kayıtları oluşturun, uzmanlık ve ilgilendiği programları inceleyip satış pipeline'ına dahil edin.">
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Panel: Hızlı Aday Kaydet -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Yeni Aday Öğrenci Kaydet</h3>
                
                <x-admin.form.layout :action="route('admin.leads.store')" method="POST">
                    
                    <div class="grid grid-cols-2 gap-4">
                        <x-admin.form.field-group label="Adı" id="first_name">
                            <input type="text" name="first_name" required placeholder="Ahmet" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                        </x-admin.form.field-group>
                        
                        <x-admin.form.field-group label="Soyadı" id="last_name">
                            <input type="text" name="last_name" required placeholder="Yılmaz" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                        </x-admin.form.field-group>
                    </div>

                    <x-admin.form.field-group label="Telefon No" id="phone">
                        <input type="text" name="phone" required placeholder="0555 555 5555" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="İlgi Duyulan Program" id="program">
                        <input type="text" name="program" placeholder="Örn: YKS Sayısal Yoğun" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Kayıt Kaynağı" id="lead_source_id">
                        <select name="lead_source_id" required class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                            @foreach($sources as $src)
                                <option value="{{ $src->id }}">{{ $src->name }}</option>
                            @endforeach
                        </select>
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Başlangıç Durumu" id="lead_status_id">
                        <select name="lead_status_id" required class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                            @foreach($statuses as $st)
                                <option value="{{ $st->id }}">{{ $st->name }}</option>
                            @endforeach
                        </select>
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Hedef Şube" id="branch_id">
                        <select name="branch_id" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                            <option value="">Merkez / HQ</option>
                            @foreach($branches as $br)
                                <option value="{{ $br->id }}">{{ $br->name }}</option>
                            @endforeach
                        </select>
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Sorumlu Danışman" id="advisor_id">
                        <select name="advisor_id" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                            <option value="">Atanmamış</option>
                            @foreach($advisors as $adv)
                                <option value="{{ $adv->id }}">{{ $adv->name }}</option>
                            @endforeach
                        </select>
                    </x-admin.form.field-group>

                    <div class="pt-4">
                        <button type="submit" class="w-full py-2.5 bg-primary text-white text-xs font-semibold rounded-xl hover:bg-primary-dark transition shadow-sm">
                            Aday Kaydını Başlat
                        </button>
                    </div>

                </x-admin.form.layout>
            </div>

            <!-- Sağ Panel: Lead Listesi -->
            <div class="lg:col-span-2 bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Kayıt Öncesi Aday Öğrenciler</h3>
                
                <x-admin.table.layout>
                    <x-slot name="head">
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Aday Öğrenci</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Program / Kaynak</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Durum</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">İşlem</th>
                    </x-slot>
                    <x-slot name="body">
                        @forelse($leads as $lead)
                            <tr>
                                <td class="px-4 py-3 text-xs">
                                    <span class="font-bold text-neutral-900 dark:text-white">{{ $lead->first_name }} {{ $lead->last_name }}</span>
                                    <div class="text-[10px] text-neutral-400 font-mono mt-0.5">{{ $lead->phone }}</div>
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    <span class="font-semibold text-neutral-700 dark:text-neutral-300">{{ $lead->program ?? 'Genel Program' }}</span>
                                    <div class="text-[10px] text-neutral-400 mt-0.5">Kaynak: {{ $lead->source->name ?? 'N/A' }}</div>
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full" style="background-color: {{ $lead->status->color ?? '#E5E7EB' }}20; color: {{ $lead->status->color ?? '#374151' }}">
                                        {{ $lead->status->name ?? 'Yeni' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    <a href="{{ route('admin.leads.show', $lead->id) }}" class="text-primary hover:underline font-bold">Detay & Timeline</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-xs text-neutral-400">Henüz kayıtlı aday öğrenci bulunmamaktadır.</td>
                            </tr>
                        @endforelse
                    </x-slot>
                </x-admin.table.layout>
            </div>

        </div>
    </x-admin.crud.index-layout>
@endsection
