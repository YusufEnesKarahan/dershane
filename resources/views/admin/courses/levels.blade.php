@extends('layouts.admin')
@section('title', 'Kurs Seviyeleri')
@section('content')
    <x-admin.crud.index-layout title="Seviye Yönetimi" description="Kurs programlarınız için seviyeler (Örn: Başlangıç, Orta, İleri) oluşturun.">
        <x-slot name="actions">
            <x-admin.button href="{{ route('admin.courses.index') }}" variant="secondary" icon="M10 19l-7-7m0 0l7-7m-7 7h18">
                Listeye Geri Dön
            </x-admin.button>
        </x-slot>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Panel: Yeni Ekle -->
            <div class="space-y-6">
                <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm">
                    <h3 class="text-base font-bold text-neutral-900 dark:text-white mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        Yeni Seviye Ekle
                    </h3>
                    
                    <x-admin.form.layout :action="route('admin.courses.levels.store')" method="POST">
                        <x-admin.form.field-group label="Seviye Adı" id="name" required>
                            <input type="text" name="name" id="name" required placeholder="Örn: Başlangıç (A1)" class="w-full bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm text-neutral-900 dark:text-white transition-colors">
                        </x-admin.form.field-group>
                        
                        <div class="pt-6 mt-6 border-t border-neutral-100 dark:border-neutral-800">
                            <x-admin.button type="submit" variant="primary" icon="M12 4v16m8-8H4" class="w-full justify-center">
                                Seviye Oluştur
                            </x-admin.button>
                        </div>
                    </x-admin.form.layout>
                </div>
            </div>

            <!-- Sağ Panel: Seviyeler Listesi -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-6">
                    <h3 class="text-base font-bold text-neutral-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></svg>
                        Tanımlı Seviyeler
                    </h3>
                    
                    @if($levels->count() > 0)
                        <div class="overflow-hidden border border-neutral-100 dark:border-neutral-800 rounded-xl">
                            <table class="w-full whitespace-nowrap">
                                <thead>
                                    <tr class="bg-neutral-50/50 dark:bg-neutral-800/50 border-b border-neutral-100 dark:border-neutral-800">
                                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Seviye Adı</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">URL Sümüklüböceği (Slug)</th>
                                        <th class="px-6 py-4 text-right text-xs font-bold text-neutral-500 uppercase tracking-wider w-24">İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800 bg-white dark:bg-neutral-900">
                                    @foreach($levels as $level)
                                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40 transition-colors group">
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-bold text-neutral-900 dark:text-white">{{ $level->name }}</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="inline-flex items-center px-2 py-1 rounded-md bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400 text-xs font-mono font-medium">
                                                    /{{ $level->slug }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <button type="button" onclick="openDeleteModal('{{ route('admin.courses.levels.destroy', $level->id) }}')" class="p-2 text-neutral-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" title="Sil">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <x-admin.empty-state
                            icon="M4 6h16M4 10h16M4 14h16M4 18h16"
                            title="Seviye Bulunamadı"
                            description="Henüz kurs seviyesi eklenmemiş. Sol taraftaki formu kullanarak yeni seviyeler ekleyebilirsiniz."
                        />
                    @endif
                </div>
            </div>

        </div>
    </x-admin.crud.index-layout>

    <x-admin.delete-modal />
@endsection
