@extends('layouts.admin')
@section('title', 'Kayıt Evrakları Yönetimi')
@section('content')
    <div class="space-y-6">
        
        <!-- Header -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <h1 class="text-lg font-bold text-neutral-900 dark:text-white">Merkezi Evrak Yönetimi</h1>
            <p class="text-xs text-neutral-500 mt-1">Tüm ön kayıt başvurularına ait yüklenen kimlik, muvafakatname ve sözleşme belgelerini inceleyin.</p>
        </div>

        <!-- Evrak Listesi -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
            
            <x-admin.table.layout>
                <x-slot name="head">
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Öğrenci / Başvuru</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Belge Türü</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Dosya Adı</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Durum</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">İşlem</th>
                </x-slot>
                <x-slot name="body">
                    @forelse($admissions as $adm)
                        @foreach($adm->documents as $doc)
                            <tr>
                                <td class="px-4 py-3 text-xs">
                                    <a href="{{ route('admin.admission.show', $adm->id) }}" class="font-bold text-neutral-900 dark:text-white hover:text-primary">
                                        {{ $adm->first_name }} {{ $adm->last_name }}
                                    </a>
                                    <div class="text-[10px] text-neutral-400 font-mono mt-0.5">{{ $adm->admission_no }}</div>
                                </td>
                                <td class="px-4 py-3 text-xs font-bold text-neutral-700 dark:text-neutral-300">{{ $doc->document_type }}</td>
                                <td class="px-4 py-3 text-xs text-neutral-600 dark:text-neutral-300 font-mono">{{ $doc->file_name }}</td>
                                <td class="px-4 py-3 text-xs">
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded {{ $doc->status === 'approved' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ $doc->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    @if($doc->status !== 'approved')
                                        <form method="POST" action="{{ route('admin.enrollment.document.approve', $doc->id) }}">
                                            @csrf
                                            <button type="submit" class="text-xs text-green-600 font-bold hover:underline">Onayla</button>
                                        </form>
                                    @else
                                        <span class="text-neutral-400">Onaylandı</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-xs text-neutral-400">Yüklü evrak bulunmamaktadır.</td>
                        </tr>
                    @endforelse
                </x-slot>
            </x-admin.table.layout>
        </div>

    </div>
@endsection
