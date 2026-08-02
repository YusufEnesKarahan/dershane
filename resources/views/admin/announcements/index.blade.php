@extends('layouts.admin')
@section('title', 'Kurumsal Duyurular')
@section('content')
    <x-admin.crud.index-layout title="Duyuru Yönetimi" description="Öğrenci, veli veya şubelere genel veya grup bazlı kurumsal duyurular yayınlayın.">
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Panel: Yeni Duyuru Yayınla -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Yeni Duyuru Yayınla</h3>
                
                <x-admin.form.layout :action="route('admin.announcements.store')" method="POST">
                    
                    <x-admin.form.field-group label="Duyuru Grubu" id="announcement_group_id">
                        <select name="announcement_group_id" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                            <option value="">Genel (Herkes)</option>
                            @foreach($groups as $g)
                                <option value="{{ $g->id }}">{{ $g->name }}</option>
                            @endforeach
                        </select>
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Duyuru Başlığı" id="title">
                        <input type="text" name="title" required placeholder="Örn: 29 Ekim Cumhuriyet Bayramı Tatili" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Duyuru Metni" id="content">
                        <textarea name="content" required rows="5" placeholder="Duyuru detayları..." class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 text-neutral-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors resize-none"></textarea>
                    </x-admin.form.field-group>

                    <div class="pt-4 border-t border-neutral-100 dark:border-neutral-800 mt-4">
                        <x-admin.button type="submit" variant="primary" icon="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" class="w-full justify-center">
                            Duyuruyu Yayınla
                        </x-admin.button>
                    </div>

                </x-admin.form.layout>
            </div>

            <!-- Sağ Panel: Yayınlanan Duyurular -->
            <div class="lg:col-span-2 bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Yayınlanan Duyurular</h3>
                
                <x-admin.table.layout>
                    <x-slot name="head">
                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Grup / Başlık</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Tarih</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Okuyan Kişi</th>
                    </x-slot>
                    <x-slot name="body">
                        @forelse($announcements as $ann)
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40">
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="px-2 py-0.5 text-[10px] font-bold bg-primary/10 text-primary border border-primary/20 rounded-md">
                                                {{ $ann->group->name ?? 'Genel' }}
                                            </span>
                                        </div>
                                        <span class="font-bold text-sm text-neutral-900 dark:text-white">{{ $ann->title }}</span>
                                        <div class="text-[11px] text-neutral-500 dark:text-neutral-400 mt-1 line-clamp-1">{{ Str::limit($ann->content, 60) }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-neutral-50 dark:bg-neutral-800/50 border border-neutral-100 dark:border-neutral-700/50 text-[11px] font-medium text-neutral-600 dark:text-neutral-300 font-mono">
                                        <svg class="w-3.5 h-3.5 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        {{ \Carbon\Carbon::parse($ann->published_at)->format('d.m.Y H:i') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-200/50 dark:border-emerald-500/20">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                        {{ $ann->reads_count }} Kişi
                                    </span>
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
