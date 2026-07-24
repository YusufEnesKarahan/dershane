@extends('layouts.admin')
@section('title', 'Kayıt Sözleşmeleri & Şablonlar')
@section('content')
    <div class="space-y-6">
        
        <!-- Header -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <h1 class="text-lg font-bold text-neutral-900 dark:text-white">Kayıt Sözleşmeleri & Şablon Yönetimi</h1>
            <p class="text-xs text-neutral-500 mt-1">Dinamik değişkenli öğrenci kayıt sözleşmelerini üretin ve imza durumlarını takip edin.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Panel: Aktif Şablonlar -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Aktif Sözleşme Şablonları</h3>
                
                <div class="space-y-3">
                    @forelse($templates as $tpl)
                        <div class="p-4 bg-neutral-50 dark:bg-neutral-800/40 rounded-xl border border-neutral-100 dark:border-neutral-800 space-y-2">
                            <div class="flex justify-between items-center text-xs font-bold">
                                <span>{{ $tpl->title }}</span>
                                <span class="px-2 py-0.5 text-[10px] bg-indigo-100 text-indigo-700 rounded font-mono">{{ $tpl->code }}</span>
                            </div>
                            <p class="text-[11px] text-neutral-500 line-clamp-3 font-mono">{!! nl2br(e($tpl->content)) !!}</p>
                        </div>
                    @empty
                        <div class="text-center text-xs text-neutral-400 py-4">Aktif şablon bulunamadı.</div>
                    @endforelse
                </div>
            </div>

            <!-- Sağ Panel: Üretilen Sözleşmeler -->
            <div class="lg:col-span-2 bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Üretilen Kayıt Sözleşmeleri</h3>
                
                <x-admin.table.layout>
                    <x-slot name="head">
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Sözleşme No</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Öğrenci / Başvuru</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Durum</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">İşlem</th>
                    </x-slot>
                    <x-slot name="body">
                        @forelse($contracts as $cnt)
                            <tr>
                                <td class="px-4 py-3 text-xs font-bold font-mono text-neutral-900 dark:text-white">{{ $cnt->contract_no }}</td>
                                <td class="px-4 py-3 text-xs">
                                    <a href="{{ route('admin.admission.show', $cnt->admission->id) }}" class="font-bold text-neutral-800 dark:text-neutral-200 hover:text-primary">
                                        {{ $cnt->admission->first_name }} {{ $cnt->admission->last_name }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded {{ $cnt->status === 'signed' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ $cnt->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    @if($cnt->status !== 'signed')
                                        <form method="POST" action="{{ route('admin.contracts.sign', $cnt->id) }}">
                                            @csrf
                                            <button type="submit" class="text-xs text-green-600 font-bold hover:underline">İmzalattır</button>
                                        </form>
                                    @else
                                        <span class="text-neutral-400">İmzalandı</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-xs text-neutral-400">Henüz üretilmiş sözleşme bulunmamaktadır.</td>
                            </tr>
                        @endforelse
                    </x-slot>
                </x-admin.table.layout>
            </div>

        </div>

    </div>
@endsection
