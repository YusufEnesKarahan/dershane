@extends('layouts.admin')
@section('title', 'Kurumsal Duyurular')
@section('content')
    <x-admin.crud.index-layout title="Duyuru Yönetimi" description="Öğrenci, veli veya şubelere genel veya grup bazlı kurumsal duyurular yayınlayın.">
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Panel: Yeni Duyuru Yayınla -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Yeni Duyuru Yayınla</h3>
                
                <x-admin.form.layout :action="route('admin.announcements.store')" method="POST">
                    
                    <x-admin.form.field-group label="Duyuru Grubu" id="announcement_group_id">
                        <select name="announcement_group_id" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                            <option value="">Genel (Herkes)</option>
                            @foreach($groups as $g)
                                <option value="{{ $g->id }}">{{ $g->name }}</option>
                            @endforeach
                        </select>
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Duyuru Başlığı" id="title">
                        <input type="text" name="title" required placeholder="Örn: 29 Ekim Cumhuriyet Bayramı Tatili" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Duyuru Metni" id="content">
                        <textarea name="content" required rows="5" placeholder="Duyuru detayları..." class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2"></textarea>
                    </x-admin.form.field-group>

                    <div class="pt-4">
                        <button type="submit" class="w-full py-2.5 bg-primary text-white text-xs font-semibold rounded-xl hover:bg-primary-dark transition shadow-sm">
                            Duyuruyu Yayınla
                        </button>
                    </div>

                </x-admin.form.layout>
            </div>

            <!-- Sağ Panel: Yayınlanan Duyurular -->
            <div class="lg:col-span-2 bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Yayınlanan Duyurular</h3>
                
                <x-admin.table.layout>
                    <x-slot name="head">
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Grup / Başlık</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Tarih</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Okuyan Kişi</th>
                    </x-slot>
                    <x-slot name="body">
                        @forelse($announcements as $ann)
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40">
                                <td class="px-4 py-3 text-xs">
                                    <span class="px-1.5 py-0.5 text-[9px] font-bold bg-primary/10 text-primary rounded mr-1">
                                        {{ $ann->group->name ?? 'Genel' }}
                                    </span>
                                    <span class="font-bold text-neutral-800">{{ $ann->title }}</span>
                                    <div class="text-[10px] text-neutral-400 mt-1">{{ Str::limit($ann->content, 60) }}</div>
                                </td>
                                <td class="px-4 py-3 text-xs text-neutral-500 font-mono">
                                    {{ \Carbon\Carbon::parse($ann->published_at)->format('d.m.Y H:i') }}
                                </td>
                                <td class="px-4 py-3 text-xs font-bold text-primary">
                                    {{ $ann->reads_count }} Kişi
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-6 text-center text-xs text-neutral-400">Henüz yayınlanmış duyuru bulunmamaktadır.</td>
                            </tr>
                        @endforelse
                    </x-slot>
                </x-admin.table.layout>
            </div>

        </div>
    </x-admin.crud.index-layout>
@endsection
