@extends('layouts.admin')
@section('title', 'Öğrenci Yönetimi')
@section('content')
    <x-admin.crud.index-layout title="Öğrenci Listesi" description="Dershane öğrencilerinizin kayıtlarını, şubelerini ve akademik yaşam döngülerini yönetin.">
        <x-slot name="actions">
            <a href="{{ route('admin.students.create') }}" class="px-4 py-2 bg-primary text-white text-xs font-semibold rounded-xl hover:bg-primary-dark transition shadow-sm">
                Yeni Öğrenci Kaydı
            </a>
            <a href="{{ route('admin.students.analytics') }}" class="px-4 py-2 bg-neutral-100 text-neutral-700 text-xs font-semibold rounded-xl hover:bg-neutral-200 transition">
                Öğrenci Analitiği
            </a>
        </x-slot>

        <x-admin.table.layout>
            <x-slot name="head">
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">Öğrenci No</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">Ad Soyad</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">Şube</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">Sınıf / Derslik</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">Veli İletişim</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">Durum</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase">İşlemler</th>
            </x-slot>
            <x-slot name="body">
                @forelse($students as $student)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40">
                        <td class="px-6 py-4 text-sm font-bold text-neutral-900">
                            {{ $student->student_number }}
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold text-neutral-900 dark:text-white">
                            {{ $student->full_name }}
                            @if($student->identity_number)
                                <div class="text-[10px] text-neutral-400">TC: {{ $student->identity_number }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-neutral-500">
                            {{ $student->branch ? $student->branch->name : 'Genel' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-neutral-500">
                            {{ $student->classroom ? $student->classroom->name : 'Atanmadı' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-neutral-500">
                            @if($student->primaryGuardian)
                                <div class="font-medium text-neutral-800">{{ $student->primaryGuardian->guardian_name }} ({{ $student->primaryGuardian->relation }})</div>
                                <div class="text-[11px] text-neutral-400">{{ $student->primaryGuardian->phone }}</div>
                            @else
                                Tanımsız
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-2.5 py-1 text-[10px] font-bold rounded-full {{ $student->status === 'Active' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $student->status === 'Active' ? 'Aktif Öğrenci' : $student->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm space-x-2">
                            <a href="{{ route('admin.students.edit', $student->id) }}" class="text-primary hover:underline">Düzenle</a>
                            <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline">Sil</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-sm text-neutral-400">Kayıtlı öğrenci bulunamadı.</td>
                    </tr>
                @endforelse
            </x-slot>
        </x-admin.table.layout>
    </x-admin.crud.index-layout>
@endsection
