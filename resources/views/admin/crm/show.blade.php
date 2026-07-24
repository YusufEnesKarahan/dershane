@extends('layouts.admin')
@section('title', 'Lead Detay & Timeline')
@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Sol Panel: Bilgiler & Atamalar -->
        <div class="space-y-6">
            
            <!-- Lead Bilgi Kartı -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <div class="flex items-center justify-between border-b border-neutral-100 dark:border-neutral-800 pb-4">
                    <div>
                        <h2 class="text-base font-bold text-neutral-900 dark:text-white">{{ $lead->first_name }} {{ $lead->last_name }}</h2>
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full mt-1 inline-block" style="background-color: {{ $lead->status->color ?? '#E5E7EB' }}20; color: {{ $lead->status->color ?? '#374151' }}">
                            {{ $lead->status->name }}
                        </span>
                    </div>
                </div>

                <div class="space-y-3 pt-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-neutral-400">Telefon:</span>
                        <span class="font-bold text-neutral-800 dark:text-neutral-200 font-mono">{{ $lead->phone }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-neutral-400">WhatsApp:</span>
                        <span class="font-bold text-neutral-800 dark:text-neutral-200 font-mono">{{ $lead->whatsapp ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-neutral-400">Program:</span>
                        <span class="font-bold text-neutral-800 dark:text-neutral-200">{{ $lead->program ?? 'Genel' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-neutral-400">Okul / Sınıf:</span>
                        <span class="font-semibold text-neutral-700 dark:text-neutral-300">{{ $lead->school ?? 'N/A' }} / {{ $lead->grade ?? 'N/A' }}</span>
                    </div>
                </div>

                <!-- Dönüştür veya Kapat Hızlı Aksiyonları -->
                <div class="pt-4 border-t border-neutral-100 dark:border-neutral-800 flex gap-2">
                    @if($lead->status->code !== 'REGISTERED')
                        <form method="POST" action="{{ route('admin.crm.pipeline.convert', $lead->id) }}" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full py-2 bg-green-600 hover:bg-green-700 text-white text-[10px] font-bold rounded-lg transition">Kayıt Al (Dönüştür)</button>
                        </form>
                    @endif
                    @if($lead->status->code !== 'LOST')
                        <form method="POST" action="{{ route('admin.crm.pipeline.close', $lead->id) }}" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full py-2 bg-rose-600 hover:bg-rose-700 text-white text-[10px] font-bold rounded-lg transition">Kaybedildi İşaretle</button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Danışman & Şube Atama Güncelleme -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Danışman & Şube Ataması</h3>
                
                <form method="POST" action="{{ route('admin.leads.assign', $lead->id) }}" class="space-y-4">
                    @csrf
                    <x-admin.form.field-group label="Eğitim Danışmanı" id="advisor_id">
                        <select name="advisor_id" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                            <option value="">Seçiniz</option>
                            @foreach($advisors as $adv)
                                <option value="{{ $adv->id }}" {{ $lead->advisor_id === $adv->id ? 'selected' : '' }}>{{ $adv->name }}</option>
                            @endforeach
                        </select>
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Şube" id="branch_id">
                        <select name="branch_id" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                            <option value="">Seçiniz</option>
                            @foreach($branches as $br)
                                <option value="{{ $br->id }}" {{ $lead->branch_id === $br->id ? 'selected' : '' }}>{{ $br->name }}</option>
                            @endforeach
                        </select>
                    </x-admin.form.field-group>

                    <button type="submit" class="w-full py-2 bg-primary text-white text-xs font-semibold rounded-xl hover:bg-primary-dark transition shadow-sm">
                        Atama Değişikliklerini Kaydet
                    </button>
                </form>
            </div>

        </div>

        <!-- Sağ Panel: Görüşme Notları & Aktivite Timeline -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Görüşme Notu Ekleme Formu -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Yeni Görüşme Notu Ekle</h3>
                
                <form method="POST" action="{{ route('admin.leads.note.store', $lead->id) }}" class="space-y-4">
                    @csrf
                    <textarea name="note_text" required rows="3" placeholder="Görüşme detayları, veli beklentileri ve fiyat teklifleri..." class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2"></textarea>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2.5 bg-primary text-white text-xs font-bold rounded-xl hover:bg-primary-dark transition shadow-sm">
                            Notu Kaydet
                        </button>
                    </div>
                </form>
            </div>

            <!-- Görüşme Notları & Timeline -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-6">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Aktivite Geçmişi & Timeline</h3>
                
                <div class="flow-root">
                    <ul role="list" class="-mb-8">
                        @forelse($lead->activities as $act)
                            <li>
                                <div class="relative pb-8">
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-rose-500/10 text-rose-600 flex items-center justify-center ring-8 ring-white dark:ring-neutral-900 text-[10px] font-bold">
                                                {{ substr($act->action_type, 0, 3) }}
                                            </span>
                                        </div>
                                        <div class="flex-1 min-w-0 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-xs text-neutral-800 dark:text-neutral-200">
                                                    {{ $act->description }}
                                                </p>
                                                <span class="text-[10px] text-neutral-400 font-semibold mt-0.5 block">Ekleyen: {{ $act->user->name ?? 'Sistem' }}</span>
                                            </div>
                                            <div class="text-right text-[10px] whitespace-nowrap text-neutral-400 font-mono">
                                                {{ $act->created_at->format('d.m H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <div class="text-center text-xs text-neutral-400 py-6">Timeline aktivitesi bulunmamaktadır.</div>
                        @endforelse
                    </ul>
                </div>
            </div>

        </div>

    </div>
@endsection
