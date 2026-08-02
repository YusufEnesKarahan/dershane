@extends('layouts.admin')
@section('title', 'Aday Öğrenci Listesi (CRM)')
@section('content')
    <x-admin.crud.index-layout title="Aday Öğrenci Yönetimi" description="Yeni aday kayıtları oluşturun, uzmanlık ve ilgilendiği programları inceleyip satış pipeline'ına dahil edin.">
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Panel: Hızlı Aday Kaydet -->
            <div class="space-y-6">
                <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-6">
                    <h3 class="text-base font-bold text-neutral-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        Yeni Aday Kaydet
                    </h3>
                    
                    <x-admin.form.layout :action="route('admin.leads.store')" method="POST">
                        
                        <div class="grid grid-cols-2 gap-4">
                            <x-admin.form.field-group label="Adı" id="first_name" required>
                                <input type="text" name="first_name" id="first_name" required placeholder="Örn: Ahmet" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                            </x-admin.form.field-group>
                            
                            <x-admin.form.field-group label="Soyadı" id="last_name" required>
                                <input type="text" name="last_name" id="last_name" required placeholder="Örn: Yılmaz" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                            </x-admin.form.field-group>
                        </div>

                        <x-admin.form.field-group label="Telefon No" id="phone" required>
                            <input type="text" name="phone" id="phone" required placeholder="0555 555 5555" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors font-mono">
                        </x-admin.form.field-group>

                        <x-admin.form.field-group label="İlgi Duyulan Program" id="program">
                            <input type="text" name="program" id="program" placeholder="Örn: YKS Sayısal Yoğun" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                        </x-admin.form.field-group>

                        <x-admin.form.field-group label="Kayıt Kaynağı" id="lead_source_id" required>
                            <select name="lead_source_id" id="lead_source_id" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                                @foreach($sources as $src)
                                    <option value="{{ $src->id }}">{{ $src->name }}</option>
                                @endforeach
                            </select>
                        </x-admin.form.field-group>

                        <x-admin.form.field-group label="Başlangıç Durumu" id="lead_status_id" required>
                            <select name="lead_status_id" id="lead_status_id" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                                @foreach($statuses as $st)
                                    <option value="{{ $st->id }}">{{ $st->name }}</option>
                                @endforeach
                            </select>
                        </x-admin.form.field-group>

                        <x-admin.form.field-group label="Hedef Şube" id="branch_id">
                            <select name="branch_id" id="branch_id" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                                <option value="">Merkez / HQ</option>
                                @foreach($branches as $br)
                                    <option value="{{ $br->id }}">{{ $br->name }}</option>
                                @endforeach
                            </select>
                        </x-admin.form.field-group>

                        <x-admin.form.field-group label="Sorumlu Danışman" id="advisor_id">
                            <select name="advisor_id" id="advisor_id" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                                <option value="">Atanmamış</option>
                                @foreach($advisors as $adv)
                                    <option value="{{ $adv->id }}">{{ $adv->name }}</option>
                                @endforeach
                            </select>
                        </x-admin.form.field-group>

                        <div class="pt-6 mt-6 border-t border-neutral-100 dark:border-neutral-800">
                            <x-admin.button type="submit" variant="primary" icon="M12 4v16m8-8H4" class="w-full justify-center">
                                Aday Kaydını Başlat
                            </x-admin.button>
                        </div>

                    </x-admin.form.layout>
                </div>
            </div>

            <!-- Sağ Panel: Lead Listesi -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-bold text-neutral-900 dark:text-white">Kayıt Öncesi Aday Öğrenciler</h3>
                        <x-admin.button href="{{ route('admin.leads.pipeline') }}" variant="primary" icon="M14 5l7 7m0 0l-7 7m7-7H3">
                            Kanban Board
                        </x-admin.button>
                    </div>
                    
                    @if($leads->count() > 0)
                        <div class="overflow-hidden border border-neutral-100 dark:border-neutral-800 rounded-xl">
                            <table class="w-full whitespace-nowrap">
                                <thead>
                                    <tr class="bg-neutral-50/50 dark:bg-neutral-800/50 border-b border-neutral-100 dark:border-neutral-800">
                                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Aday Öğrenci</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Program / Kaynak</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Durum</th>
                                        <th class="px-6 py-4 text-right text-xs font-bold text-neutral-500 uppercase tracking-wider">İşlem</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800 bg-white dark:bg-neutral-900">
                                    @foreach($leads as $lead)
                                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40 transition-colors group">
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-bold text-neutral-900 dark:text-white">{{ $lead->first_name }} {{ $lead->last_name }}</div>
                                                <div class="text-xs text-neutral-500 dark:text-neutral-400 font-mono mt-0.5">{{ $lead->phone }}</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">{{ $lead->program ?? 'Genel Program' }}</div>
                                                <div class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">Kaynak: <span class="font-medium">{{ $lead->source->name ?? 'N/A' }}</span></div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold" style="background-color: {{ $lead->status->color ?? '#E5E7EB' }}20; color: {{ $lead->status->color ?? '#374151' }}">
                                                    <span class="w-1.5 h-1.5 rounded-full mr-1.5" style="background-color: {{ $lead->status->color ?? '#374151' }}"></span>
                                                    {{ $lead->status->name ?? 'Yeni' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-right text-sm">
                                                <x-admin.button href="{{ route('admin.leads.show', $lead->id) }}" variant="secondary" icon="M9 5l7 7-7 7" icon-position="right">
                                                    Detaylar
                                                </x-admin.button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if(method_exists($leads, 'links'))
                            <div class="mt-4">
                                {{ $leads->links() }}
                            </div>
                        @endif
                    @else
                        <div class="p-8 text-center border-2 border-dashed border-neutral-200 dark:border-neutral-800 rounded-xl bg-neutral-50 dark:bg-neutral-800/20">
                            <div class="mx-auto w-12 h-12 bg-neutral-100 dark:bg-neutral-800 text-neutral-400 rounded-full flex items-center justify-center mb-3">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                            </div>
                            <h4 class="text-sm font-bold text-neutral-900 dark:text-white mb-1">Kayıt Bulunamadı</h4>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Henüz kayıtlı aday öğrenci bulunmamaktadır. Sol menüden yeni bir aday ekleyebilirsiniz.</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </x-admin.crud.index-layout>
@endsection
