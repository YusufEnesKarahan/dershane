@extends('layouts.admin')
@section('title', 'Takip Aramaları & Hatırlatıcılar')
@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Sol Panel: Yeni Takip Görevi Ekle -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
            <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Yeni Takip Arama Planla</h3>
            <p class="text-xs text-neutral-400 font-semibold">Aday öğrenci için sonraki arama/görüşme zamanını belirleyin.</p>
            
            <form method="POST" action="{{ route('admin.crm.followups.store') }}" class="space-y-4">
                @csrf
                <x-admin.form.field-group label="Aday Öğrenci" id="lead_id">
                    <select name="lead_id" required class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                        @foreach($leads as $lead)
                            <option value="{{ $lead->id }}">{{ $lead->first_name }} {{ $lead->last_name }} ({{ $lead->phone }})</option>
                        @endforeach
                    </select>
                </x-admin.form.field-group>

                <x-admin.form.field-group label="Takip Arama Zamanı" id="followup_date">
                    <input type="datetime-local" name="followup_date" required class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                </x-admin.form.field-group>

                <x-admin.form.field-group label="Arama Notu / Hatırlatıcı" id="reminder_note">
                    <textarea name="reminder_note" required rows="3" placeholder="Fiyat teklifi sorulacak, veli aranacak..." class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2"></textarea>
                </x-admin.form.field-group>

                <x-admin.form.field-group label="Öncelik Seviyesi" id="priority">
                    <select name="priority" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                        <option value="Low">Düşük</option>
                        <option value="Medium" selected>Orta</option>
                        <option value="High">Yüksek</option>
                    </select>
                </x-admin.form.field-group>

                <button type="submit" class="w-full py-2.5 bg-primary text-white text-xs font-bold rounded-xl hover:bg-primary-dark transition shadow-sm">
                    Takip Görevi Ekle
                </button>
            </form>
        </div>

        <!-- Sağ Panel: Takip Listesi -->
        <div class="lg:col-span-2 bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
            <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Planlanan Arama & Takipler</h3>
            
            <x-admin.table.layout>
                <x-slot name="head">
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Aday</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Not / Hatırlatıcı</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Zaman</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Öncelik</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Durum</th>
                </x-slot>
                <x-slot name="body">
                    @forelse($followups as $f)
                        <tr>
                            <td class="px-4 py-3 text-xs">
                                <span class="font-bold text-neutral-900 dark:text-white">{{ $f->lead->first_name }} {{ $f->lead->last_name }}</span>
                                <div class="text-[10px] text-neutral-400 font-mono mt-0.5">{{ $f->lead->phone }}</div>
                            </td>
                            <td class="px-4 py-3 text-xs text-neutral-600 dark:text-neutral-300">{{ $f->reminder_note }}</td>
                            <td class="px-4 py-3 text-xs font-mono text-neutral-500">{{ $f->followup_date->format('d.m.Y H:i') }}</td>
                            <td class="px-4 py-3 text-xs">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded {{ $f->priority === 'High' ? 'bg-rose-100 text-rose-700' : 'bg-neutral-100 text-neutral-700' }}">
                                    {{ $f->priority }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs">
                                @if($f->status === 'Pending')
                                    <form method="POST" action="{{ route('admin.crm.followups.complete', $f->id) }}">
                                        @csrf
                                        <button type="submit" class="text-xs text-green-600 hover:underline font-bold">Tamamla</button>
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
