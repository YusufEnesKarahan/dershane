@extends('layouts.admin')
@section('title', 'Kurs Yönetimi')
@section('content')
    <x-admin.crud.index-layout title="Kurs Kataloğu" description="Dershaneniz bünyesinde verilen tüm ders ve kurs programlarını yönetin.">
        <x-slot name="actions">
            <a href="{{ route('admin.courses.create') }}" class="px-4 py-2 bg-primary text-white text-xs font-semibold rounded-xl hover:bg-primary-dark transition shadow-sm">
                Yeni Kurs Ekle
            </a>
            <a href="{{ route('admin.courses.levels.index') }}" class="px-4 py-2 bg-neutral-100 text-neutral-700 text-xs font-semibold rounded-xl hover:bg-neutral-200 transition">
                Seviye Yönetimi
            </a>
            <a href="{{ route('admin.courses.analytics') }}" class="px-4 py-2 bg-neutral-100 text-neutral-700 text-xs font-semibold rounded-xl hover:bg-neutral-200 transition">
                Analitik Raporlar
            </a>
        </x-slot>

        <x-admin.table.layout>
            <x-slot name="head">
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">Kod</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">Kurs Adı</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">Seviye</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">Süre</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">Kontenjan</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">Fiyat</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">Durum</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">İşlemler</th>
            </x-slot>
            <x-slot name="body">
                @forelse($courses as $course)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40">
                        <td class="px-6 py-4 text-sm font-bold text-neutral-900">
                            {{ $course->code }}
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold text-neutral-900 dark:text-white">
                            {{ $course->name }}
                            <div class="text-[10px] text-neutral-400 mt-0.5">/{{ $course->slug }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-neutral-500">
                            {{ $course->level ? $course->level->name : 'Genel' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-neutral-500">
                            {{ $course->duration }}
                        </td>
                        <td class="px-6 py-4 text-sm text-neutral-500">
                            {{ $course->capacity }} Kişi
                        </td>
                        <td class="px-6 py-4 text-sm text-neutral-950 font-bold">
                            {{ $course->currentPricing ? number_format($course->currentPricing->price, 2) . ' TL' : 'Tanımsız' }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-2.5 py-1 text-[10px] font-bold rounded-full {{ $course->is_active ? 'bg-green-100 text-green-800' : 'bg-neutral-100 text-neutral-800' }}">
                                {{ $course->is_active ? 'Aktif' : 'Pasif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm space-x-2">
                            <a href="{{ route('admin.courses.edit', $course->id) }}" class="text-primary hover:underline">Düzenle</a>
                            <form action="{{ route('admin.courses.destroy', $course->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline">Sil</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-sm text-neutral-400">Kayıtlı kurs bulunamadı.</td>
                    </tr>
                @endforelse
            </x-slot>
        </x-admin.table.layout>
    </x-admin.crud.index-layout>
@endsection
