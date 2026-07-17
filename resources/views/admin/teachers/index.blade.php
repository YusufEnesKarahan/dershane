@extends('layouts.admin')
@section('title', 'Eğitmen Yönetimi')
@section('content')
    <x-admin.crud.index-layout title="Eğitmen Kadrosu" description="Dershane eğitmen profillerini, şubelerini ve çalışma planlarını yönetin.">
        <x-slot name="actions">
            <a href="{{ route('admin.teachers.create') }}" class="px-4 py-2 bg-primary text-white text-xs font-semibold rounded-xl hover:bg-primary-dark transition shadow-sm">
                Yeni Eğitmen Tanımla
            </a>
            <a href="{{ route('admin.teachers.schedules.index') }}" class="px-4 py-2 bg-neutral-100 text-neutral-700 text-xs font-semibold rounded-xl hover:bg-neutral-200 transition">
                Ders Programları & Çakışmalar
            </a>
        </x-slot>

        <x-admin.table.layout>
            <x-slot name="head">
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">Eğitmen</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">Ünvan / Branş</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">Şube</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">Deneyim</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">Durum</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">İşlemler</th>
            </x-slot>
            <x-slot name="body">
                @forelse($teachers as $teacher)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40">
                        <td class="px-6 py-4 text-sm font-semibold text-neutral-900 dark:text-white">
                            {{ $teacher->user->name }}
                            <div class="text-[10px] text-neutral-400 mt-0.5">{{ $teacher->user->email }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-neutral-900">
                            {{ $teacher->title }}
                            @if($teacher->specialties)
                                <div class="text-[10px] text-neutral-400 mt-0.5">{{ $teacher->specialties }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-neutral-500">
                            {{ $teacher->branch ? $teacher->branch->name : 'Şube Yok' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-neutral-500">
                            {{ $teacher->experience_years }} Yıl
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-2.5 py-1 text-[10px] font-bold rounded-full {{ $teacher->status === 'Active' ? 'bg-green-100 text-green-800' : 'bg-neutral-100 text-neutral-800' }}">
                                {{ $teacher->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm space-x-2">
                            <a href="{{ route('admin.teachers.edit', $teacher->id) }}" class="text-primary hover:underline">Düzenle</a>
                            <form action="{{ route('admin.teachers.destroy', $teacher->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline">Sil</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-sm text-neutral-400">Kayıtlı eğitmen bulunamadı.</td>
                    </tr>
                @endforelse
            </x-slot>
        </x-admin.table.layout>
    </x-admin.crud.index-layout>
@endsection
