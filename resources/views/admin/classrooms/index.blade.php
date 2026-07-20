@extends('layouts.admin')
@section('title', 'Sınıflar & Derslikler')
@section('content')
    <x-admin.crud.index-layout title="Derslik Yönetimi" description="Kurum bünyesindeki fiziki derslikleri, kapasiteleri ve sınıf tiplerini yönetin.">
        <x-slot name="actions">
            <a href="{{ route('admin.classrooms.create') }}" class="px-4 py-2 bg-primary text-white text-xs font-semibold rounded-xl hover:bg-primary-dark transition shadow-sm">
                Yeni Derslik Ekle
            </a>
            <a href="{{ route('admin.classrooms.schedules.index') }}" class="px-4 py-2 bg-neutral-100 text-neutral-700 text-xs font-semibold rounded-xl hover:bg-neutral-200 transition">
                Haftalık Program
            </a>
            <a href="{{ route('admin.classrooms.academic-calendar.index') }}" class="px-4 py-2 bg-neutral-100 text-neutral-700 text-xs font-semibold rounded-xl hover:bg-neutral-200 transition">
                Akademik Takvim
            </a>
            <a href="{{ route('admin.classrooms.holidays.index') }}" class="px-4 py-2 bg-neutral-100 text-neutral-700 text-xs font-semibold rounded-xl hover:bg-neutral-200 transition">
                Tatil Günleri
            </a>
            <a href="{{ route('admin.classrooms.analytics') }}" class="px-4 py-2 bg-neutral-100 text-neutral-700 text-xs font-semibold rounded-xl hover:bg-neutral-200 transition">
                Analitik
            </a>
        </x-slot>

        <x-admin.table.layout>
            <x-slot name="head">
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">Kod</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">Derslik Adı</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">Şube</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">Derslik Tipi</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">Kapasite</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">Renk Kodu</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">Durum</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">İşlemler</th>
            </x-slot>
            <x-slot name="body">
                @forelse($classrooms as $classroom)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40">
                        <td class="px-6 py-4 text-sm font-bold text-neutral-900">
                            {{ $classroom->code }}
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold text-neutral-900 dark:text-white">
                            {{ $classroom->name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-neutral-500">
                            {{ $classroom->branch ? $classroom->branch->name : 'Tüm Şubeler' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-neutral-500">
                            {{ $classroom->type ? $classroom->type->name : 'Standart Derslik' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-neutral-500 font-medium">
                            {{ $classroom->capacity }} Öğrenci
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span class="inline-flex items-center gap-1.5 text-xs font-mono">
                                <span class="w-3.5 h-3.5 rounded-full border border-neutral-200" style="background-color: {{ $classroom->color_code }}"></span>
                                {{ $classroom->color_code }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-2.5 py-1 text-[10px] font-bold rounded-full {{ $classroom->is_active ? 'bg-green-100 text-green-800' : 'bg-neutral-100 text-neutral-800' }}">
                                {{ $classroom->is_active ? 'Aktif' : 'Pasif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm space-x-2">
                            <a href="{{ route('admin.classrooms.edit', $classroom->id) }}" class="text-primary hover:underline">Düzenle</a>
                            <form action="{{ route('admin.classrooms.destroy', $classroom->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline">Sil</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-sm text-neutral-400">Kayıtlı derslik bulunamadı.</td>
                    </tr>
                @endforelse
            </x-slot>
        </x-admin.table.layout>
    </x-admin.crud.index-layout>
@endsection
