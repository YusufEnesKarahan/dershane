@extends('layouts.admin')
@section('title', 'Kurs Yönetimi')
@section('content')
    <x-admin.crud.index-layout title="Kurs Kataloğu" description="Dershaneniz bünyesinde verilen tüm ders ve kurs programlarını yönetin.">
        <x-slot name="actions">
            <x-admin.button href="{{ route('admin.courses.analytics') }}" variant="secondary" icon="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                Analitik Raporlar
            </x-admin.button>
            <x-admin.button href="{{ route('admin.courses.levels.index') }}" variant="secondary" icon="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12">
                Seviye Yönetimi
            </x-admin.button>
            <x-admin.button href="{{ route('admin.courses.create') }}" variant="primary" icon="M12 4v16m8-8H4">
                Yeni Kurs Ekle
            </x-admin.button>
        </x-slot>

        <x-admin.table.layout>
            <x-slot name="head">
                <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider w-24">Kod</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Kurs Adı</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Seviye</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Süre / Kontenjan</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Fiyat</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider w-24">Durum</th>
                <th class="px-6 py-4 text-right text-xs font-bold text-neutral-500 uppercase tracking-wider w-24">İşlemler</th>
            </x-slot>
            <x-slot name="body">
                @forelse($courses as $course)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40 transition-colors group">
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2 py-1 rounded-md bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400 text-xs font-mono font-bold">
                                {{ $course->code }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-neutral-900 dark:text-white">{{ $course->name }}</div>
                            <div class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5 font-mono">/{{ $course->slug }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-neutral-100 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 text-xs font-medium text-neutral-700 dark:text-neutral-300">
                                <svg class="w-3.5 h-3.5 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                                {{ $course->level ? $course->level->name : 'Genel Seviye' }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="flex items-center gap-1.5 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                    <svg class="w-4 h-4 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    {{ $course->duration }}
                                </div>
                                <div class="flex items-center gap-1.5 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                    <svg class="w-4 h-4 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                    {{ $course->capacity }} Kişi
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-neutral-900 dark:text-white">
                                {{ $course->currentPricing ? number_format($course->currentPricing->price, 2) . ' ₺' : 'Fiyat Tanımsız' }}
                            </div>
                            @if($course->currentPricing)
                                <div class="text-[10px] text-neutral-400 mt-0.5">Aktif Fiyatlandırma</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($course->is_active)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-neutral-100 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-neutral-400 mr-1.5"></span>
                                    Pasif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.courses.edit', $course->id) }}" class="p-2 text-neutral-400 hover:text-primary hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-lg transition-colors" title="Düzenle">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </a>
                                <button type="button" onclick="openDeleteModal('{{ route('admin.courses.destroy', $course->id) }}')" class="p-2 text-neutral-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" title="Sil">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-0 py-0">
                            <x-admin.empty-state
                                icon="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                                title="Kurs Bulunamadı"
                                description="Sistemde kayıtlı herhangi bir kurs programı bulunmuyor. Yeni bir kurs ekleyerek başlayabilirsiniz."
                                action-url="{{ route('admin.courses.create') }}"
                                action-text="İlk Kursu Ekle"
                            />
                        </td>
                    </tr>
                @endforelse
            </x-slot>
        </x-admin.table.layout>
    </x-admin.crud.index-layout>

    <x-admin.delete-modal />
@endsection
