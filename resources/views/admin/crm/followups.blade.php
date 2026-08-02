@extends('layouts.admin')
@section('title', 'Takip Aramaları & Hatırlatıcılar')
@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Sol Panel: Yeni Takip Görevi Ekle -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Yeni Takip Arama Planla</h3>
            <p class="text-xs text-neutral-400 font-semibold">Aday öğrenci için sonraki arama/görüşme zamanını belirleyin.</p>
            
            <form method="POST" action="{{ route('admin.crm.followups.store') }}" class="space-y-4">
                @csrf
                <x-admin.form.field-group label="Aday Öğrenci" id="lead_id">
                    <select name="lead_id" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                        @foreach($leads as $lead)
                            <option value="{{ $lead->id }}">{{ $lead->first_name }} {{ $lead->last_name }} ({{ $lead->phone }})</option>
                        @endforeach
                    </select>
                </x-admin.form.field-group>

                <x-admin.form.field-group label="Takip Arama Zamanı" id="followup_date">
                    <input type="datetime-local" name="followup_date" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                </x-admin.form.field-group>

                <x-admin.form.field-group label="Arama Notu / Hatırlatıcı" id="reminder_note">
                    <textarea name="reminder_note" required rows="3" placeholder="Fiyat teklifi sorulacak, veli aranacak..." class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors resize-none"></textarea>
                </x-admin.form.field-group>

                <x-admin.form.field-group label="Öncelik Seviyesi" id="priority">
                    <select name="priority" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                        <option value="Low">Düşük</option>
                        <option value="Medium" selected>Orta</option>
                        <option value="High">Yüksek</option>
                    </select>
                </x-admin.form.field-group>

                <div class="pt-2">
                    <x-admin.button type="submit" variant="primary" class="w-full justify-center">
                        Takip Görevi Ekle
                    </x-admin.button>
                </div>
            </form>
        </div>

        <!-- Sağ Panel: Takip Listesi -->
        <div class="lg:col-span-2 bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Planlanan Arama & Takipler</h3>
            
            <x-admin.table.layout>
                <x-slot name="head">
                    <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Aday</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Not / Hatırlatıcı</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Zaman</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Öncelik</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Durum</th>
                </x-slot>
                <x-slot name="body">
                    @forelse($followups as $f)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40">
                            <td class="px-6 py-4">
                                <span class="font-bold text-sm text-neutral-900 dark:text-white">{{ $f->lead->first_name }} {{ $f->lead->last_name }}</span>
                                <div class="text-[11px] text-neutral-500 dark:text-neutral-400 font-mono mt-0.5">{{ $f->lead->phone }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-neutral-600 dark:text-neutral-300">{{ $f->reminder_note }}</td>
                            <td class="px-6 py-4 text-sm font-mono text-neutral-500">{{ $f->followup_date->format('d.m.Y H:i') }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $f->priority === 'High' ? 'bg-rose-100 text-rose-800 border-rose-200/50 dark:bg-rose-500/20 dark:text-rose-400 dark:border-rose-500/20' : 'bg-neutral-100 text-neutral-800 border-neutral-200/50 dark:bg-neutral-800 dark:text-neutral-300 dark:border-neutral-700' }}">
                                    {{ $f->priority }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if($f->status === 'Pending')
                                    <form method="POST" action="{{ route('admin.crm.followups.complete', $f->id) }}">
                                        @csrf
                                        <button type="submit" class="text-emerald-600 hover:text-emerald-700 font-bold transition-colors">Tamamla</button>
                                    </form>
                                @else
                                    <span class="text-neutral-400">Tamamlandı</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-xs text-neutral-400">Yakında planlanmış takip araması bulunmamaktadır.</td>
                        </tr>
                    @endforelse
                </x-slot>
            </x-admin.table.layout>
        </div>

    </div>
@endsection
