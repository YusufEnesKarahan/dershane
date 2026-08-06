@extends('layouts.admin')
@section('title', 'Ders Yönetimi')
@section('content')
    <x-admin.crud.index-layout title="Ders Kataloğu" description="Dershaneniz bünyesinde okutulan tüm dersleri, öğretmen atamalarını ve ders detaylarını yönetin.">
        <x-slot name="actions">
            <x-admin.button href="{{ route('admin.courses.create') }}" variant="primary" icon="M12 4v16m8-8H4">
                Yeni Ders Ekle
            </x-admin.button>
        </x-slot>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl shadow-sm flex items-center gap-2 font-bold text-sm">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        <x-admin.table.layout>
            <x-slot name="head">
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider w-24">Kod</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Ders Adı</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Atanan Öğretmenler</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Seviye</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Fiyat</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider w-24">Durum</th>
                <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider w-24">İşlemler</th>
            </x-slot>
            <x-slot name="body">
                @forelse($courses as $course)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors group">
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-mono font-bold">
                                {{ $course->code }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-slate-900 dark:text-white">{{ $course->name }}</div>
                            <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-mono">/{{ $course->slug }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($course->teachers->count() > 0)
                                <div class="space-y-1">
                                    @foreach($course->teachers as $t)
                                        <div class="text-xs flex items-center gap-1.5">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $t->pivot->is_primary ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}">
                                                {{ $t->pivot->is_primary ? 'Ana Öğretmen' : 'Yardımcı' }}
                                            </span>
                                            <span class="font-medium text-slate-800 dark:text-slate-200">{{ $t->user?->name ?? 'Öğretmen #' . $t->id }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-xs text-slate-400 italic">Öğretmen Atanmadı</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-medium text-slate-700 dark:text-slate-300">
                                {{ $course->level ? $course->level->name : 'Genel Seviye' }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-slate-900 dark:text-white">
                                {{ $course->currentPricing ? number_format($course->currentPricing->price, 2) . ' ₺' : 'Fiyat Tanımsız' }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($course->is_active)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400 mr-1.5"></span>
                                    Pasif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.courses.edit', $course->id) }}" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Düzenle">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </a>
                                <form action="{{ route('admin.courses.destroy', $course->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Bu dersi silmek istediğinize emin misiniz?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Sil">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-0 py-0">
                            <x-admin.empty-state
                                icon="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                                title="Ders Bulunamadı"
                                description="Sistemde henüz kayıtlı bir ders bulunmuyor. Yeni bir ders ekleyerek başlayabilirsiniz."
                                action-url="{{ route('admin.courses.create') }}"
                                action-text="İlk Dersi Ekle"
                            />
                        </td>
                    </tr>
                @endforelse
            </x-slot>
        </x-admin.table.layout>
    </x-admin.crud.index-layout>
@endsection
