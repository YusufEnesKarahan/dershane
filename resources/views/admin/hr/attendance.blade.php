@extends('layouts.admin')
@section('title', 'Personel Devamsızlık & Giriş-Çıkış')
@section('content')
    <x-admin.crud.index-layout title="Personel Giriş - Çıkış Takibi" description="Personellerin günlük devamsızlık durumlarını, mesai başlama saatlerini ve fazla mesailerini kayıt altına alın.">
        <x-slot name="actions">
            <button onclick="toggleModal('attendance-modal')" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-xl transition-colors shadow-lg shadow-blue-900/20 border border-blue-500/50">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Giriş Çıkış Kaydı Ekle
            </button>
        </x-slot>

        <!-- Devam Giriş-Çıkış Kayıtları -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden flex flex-col h-full">
            <div class="p-0 flex-1">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                        <thead class="bg-slate-50/80 dark:bg-slate-900/80 backdrop-blur-sm">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider bg-slate-50/50 dark:bg-slate-800/30">Tarih</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider bg-slate-50/50 dark:bg-slate-800/30">Personel</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider bg-slate-50/50 dark:bg-slate-800/30">Giriş / Çıkış</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider bg-slate-50/50 dark:bg-slate-800/30">Çalışma Süresi</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider bg-slate-50/50 dark:bg-slate-800/30">Geç Kalma</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider bg-slate-50/50 dark:bg-slate-800/30">Fazla Mesai</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50 bg-white dark:bg-slate-900">
                            @forelse($attendances as $att)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors border-b border-slate-100 dark:border-slate-800/50 last:border-0 group">
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-300 font-mono">
                                            {{ \Carbon\Carbon::parse($att->date)->format('d.m.Y') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-slate-900 dark:text-white">{{ $att->employee->first_name }} {{ $att->employee->last_name }}</div>
                                        <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-1.5"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg> {{ $att->employee->department->name ?? 'Yok' }}</div>
                                    </td>
                                    <td class="px-6 py-4 font-mono">
                                        <div class="text-sm font-medium text-slate-900 dark:text-white mb-0.5">Giriş: <span class="font-bold">{{ substr($att->check_in, 0, 5) }}</span></div>
                                        <div class="text-sm font-medium text-slate-500 dark:text-slate-400">Çıkış: <span>{{ $att->check_out ? substr($att->check_out, 0, 5) : 'Girilmemiş' }}</span></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-bold text-slate-900 dark:text-white font-mono">{{ round($att->worked_minutes/60, 1) }} Saat</span>
                                        <div class="text-[10px] text-slate-400 mt-0.5">{{ $att->worked_minutes }} Dk</div>
                                    </td>
                                    <td class="px-6 py-4 font-mono">
                                        @if($att->late_minutes > 0)
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 text-xs font-bold border border-amber-200/50 dark:border-amber-500/20">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                {{ $att->late_minutes }} Dk
                                            </span>
                                        @else
                                            <span class="text-slate-400 text-xs flex items-center gap-1"><svg class="w-3.5 h-3.5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg> Zamanında</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 font-mono">
                                        @if($att->overtime_minutes > 0)
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-green-50 dark:bg-green-500/10 text-green-600 dark:text-green-400 text-xs font-bold border border-green-200/50 dark:border-green-500/20">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                                {{ $att->overtime_minutes }} Dk
                                            </span>
                                        @else
                                            <span class="text-slate-400 text-xs">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <x-admin.empty-state
                                            icon="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                            title="Giriş Çıkış Kaydı Bulunamadı"
                                            description="Sistemde henüz bir mesai veya devamsızlık kaydı bulunmuyor."
                                        />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </x-admin.crud.index-layout>

        <!-- Giriş Çıkış Ekleme Modal -->
        <div id="attendance-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 p-6 max-w-md w-full shadow-premium space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Giriş Çıkış Kaydı Ekle</h3>
                    <button onclick="toggleModal('attendance-modal')" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>
                
                <form method="POST" action="{{ route('admin.attendance.store') }}" class="space-y-3 text-xs">
                    @csrf
                    
                    <div class="space-y-1">
                        <label class="font-bold text-slate-600 dark:text-slate-400">Personel</label>
                        <select name="employee_id" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-slate-600 dark:text-slate-400">Tarih</label>
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="font-bold text-slate-600 dark:text-slate-400">Giriş Saati</label>
                            <input type="text" name="check_in" required placeholder="09:00:00" value="09:00:00" class="w-full p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-slate-600 dark:text-slate-400">Çıkış Saati</label>
                            <input type="text" name="check_out" placeholder="17:00:00" value="17:00:00" class="w-full p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" onclick="toggleModal('attendance-modal')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 font-bold rounded-xl transition">Vazgeç</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition">Kaydet</button>
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
