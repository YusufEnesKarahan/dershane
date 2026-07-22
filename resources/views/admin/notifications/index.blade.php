@extends('layouts.admin')
@section('title', 'Sistem Bildirimleri')
@section('content')
    <x-admin.crud.index-layout title="Bildirim Gönderimi & Yönetimi" description="Öğretmen, veli ve öğrencilere SMS, E-Posta veya sistem içi anlık bildirimler gönderin.">
        <x-slot name="actions">
            <a href="{{ route('admin.notifications.templates') }}" class="px-4 py-2 bg-neutral-100 text-neutral-700 text-xs font-semibold rounded-xl hover:bg-neutral-200 transition">
                Mesaj Şablonları
            </a>
            <a href="{{ route('admin.notifications.analytics') }}" class="px-4 py-2 bg-neutral-100 text-neutral-700 text-xs font-semibold rounded-xl hover:bg-neutral-200 transition">
                Gönderim Analitiği
            </a>
        </x-slot>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Panel: Yeni Bildirim Gönder -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Anlık Bildirim Gönder</h3>
                
                <x-admin.form.layout :action="route('admin.notifications.store')" method="POST">
                    
                    <x-admin.form.field-group label="Alıcı Kullanıcı" id="user_id">
                        <select name="user_id" required class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                            @endforeach
                        </select>
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Gönderim Kanalı" id="type">
                        <select name="type" required class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                            <option value="System">Uygulama İçi (Sistem)</option>
                            <option value="SMS">SMS (Kısa Mesaj)</option>
                            <option value="Email">E-Posta</option>
                        </select>
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Bildirim Başlığı" id="title">
                        <input type="text" name="title" required placeholder="Örn: Sınav Sonuçları Açıklandı" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Mesaj İçeriği" id="content">
                        <textarea name="content" required rows="4" placeholder="Gönderilecek bildirim mesajı detayı..." class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2"></textarea>
                    </x-admin.form.field-group>

                    <div class="pt-4">
                        <button type="submit" class="w-full py-2.5 bg-primary text-white text-xs font-semibold rounded-xl hover:bg-primary-dark transition shadow-sm">
                            Bildirimi Sıraya Al & Gönder
                        </button>
                    </div>

                </x-admin.form.layout>
            </div>

            <!-- Sağ Panel: Son Gönderilen Bildirimler -->
            <div class="lg:col-span-2 bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Son Gönderilen Bildirimler</h3>
                
                <x-admin.table.layout>
                    <x-slot name="head">
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Alıcı</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Kanal / Başlık</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Tarih</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Durum</th>
                    </x-slot>
                    <x-slot name="body">
                        @forelse($notifications as $notif)
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40">
                                <td class="px-4 py-3 text-xs font-bold text-neutral-900">
                                    {{ $notif->user->name }}
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    <span class="px-1.5 py-0.5 text-[9px] font-bold bg-neutral-100 text-neutral-700 rounded mr-1">{{ $notif->type }}</span>
                                    <span class="font-semibold text-neutral-800">{{ $notif->title }}</span>
                                    <div class="text-[10px] text-neutral-400 mt-1">{{ Str::limit($notif->content, 50) }}</div>
                                </td>
                                <td class="px-4 py-3 text-xs text-neutral-500 font-mono">
                                    {{ $notif->created_at->format('d.m.Y H:i') }}
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full {{ $notif->status === 'Read' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $notif->status === 'Read' ? 'Okundu' : 'İletildi' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-xs text-neutral-400">Henüz gönderilmiş bildirim bulunmamaktadır.</td>
                            </tr>
                        @endforelse
                    </x-slot>
                </x-admin.table.layout>
            </div>

        </div>
    </x-admin.crud.index-layout>
@endsection
