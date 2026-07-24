@extends('layouts.admin')
@section('title', 'Performans Değerlendirmeleri')
@section('content')
    <div class="space-y-6">
        
        <!-- Header -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm flex justify-between items-center">
            <div>
                <h1 class="text-lg font-bold text-neutral-900 dark:text-white">Personel Performans Değerlendirmeleri</h1>
                <p class="text-xs text-neutral-500 mt-1">Personellerin dönemlik hedeflerini, güçlü/zayıf yönlerini ve performans puanlamalarını yönetin.</p>
            </div>
            
            <button onclick="toggleModal('review-modal')" class="px-4 py-2 bg-violet-600 hover:bg-violet-700 text-xs font-bold text-white rounded-xl transition shadow-lg shadow-violet-950">
                Değerlendirme Ekle
            </button>
        </div>

        <!-- Değerlendirme Kayıtları Tablosu -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <x-admin.table.layout>
                <x-slot name="head">
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Dönem</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Personel</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Puan</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Değerlendiren</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Detaylar</th>
                </x-slot>
                <x-slot name="body">
                    @forelse($reviews as $rev)
                        <tr>
                            <td class="px-4 py-3 text-xs font-bold font-mono text-neutral-900 dark:text-white">{{ $rev->period }}</td>
                            <td class="px-4 py-3 text-xs font-bold">
                                {{ $rev->employee->first_name }} {{ $rev->employee->last_name }}
                                <div class="text-[10px] font-normal text-neutral-400 mt-0.5">{{ $rev->employee->department->name ?? 'Yok' }}</div>
                            </td>
                            <td class="px-4 py-3 text-xs font-mono font-bold">
                                <span class="px-2 py-0.5 rounded {{ $rev->score >= 80 ? 'bg-green-100 text-green-700' : ($rev->score >= 50 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                                    {{ $rev->score }} / 100
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs">{{ $rev->reviewer->name ?? 'Belirlenmedi' }}</td>
                            <td class="px-4 py-3 text-xs space-y-1">
                                <div class="text-neutral-700 dark:text-neutral-300 font-bold">Güçlü Yönler: <span class="font-normal text-neutral-500">{{ $rev->strengths }}</span></div>
                                <div class="text-neutral-700 dark:text-neutral-300 font-bold">Gelişmeli: <span class="font-normal text-neutral-500">{{ $rev->weaknesses }}</span></div>
                                <p class="text-[10px] text-neutral-400 italic">Yorum: {{ $rev->comments }}</p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-xs text-neutral-400">Performans değerlendirmesi bulunmamaktadır.</td>
                        </tr>
                    @endforelse
                </x-slot>
            </x-admin.table.layout>
        </div>

        <!-- Performans Modal -->
        <div id="review-modal" class="fixed inset-0 z-50 hidden bg-neutral-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 p-6 max-w-md w-full shadow-premium space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Yeni Performans Değerlendirmesi Ekle</h3>
                    <button onclick="toggleModal('review-modal')" class="text-neutral-400 hover:text-neutral-600">&times;</button>
                </div>
                
                <form method="POST" action="{{ route('admin.performance.store') }}" class="space-y-3 text-xs">
                    @csrf
                    
                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Personel</label>
                        <select name="employee_id" required class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Dönem</label>
                        <input type="text" name="period" required placeholder="Örn: 2026-Q1, 2026-Yıllık" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Puan (1-100)</label>
                        <input type="number" name="score" required min="1" max="100" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Güçlü Yönler</label>
                        <textarea name="strengths" rows="2" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl"></textarea>
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Geliştirilmesi Gereken Yönler</label>
                        <textarea name="weaknesses" rows="2" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl"></textarea>
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Değerlendirme Yorumu</label>
                        <textarea name="comments" rows="2" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" onclick="toggleModal('review-modal')" class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 font-bold rounded-xl transition">Vazgeç</button>
                        <button type="submit" class="px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white font-bold rounded-xl transition">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        function toggleModal(id) {
            const el = document.getElementById(id);
            el.classList.toggle('hidden');
        }
    </script>
@endsection
