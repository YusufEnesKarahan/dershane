@extends('layouts.admin')
@section('title', 'Personel Devamsızlık & Giriş-Çıkış')
@section('content')
    <div class="space-y-6">
        
        <!-- Header -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm flex justify-between items-center">
            <div>
                <h1 class="text-lg font-bold text-neutral-900 dark:text-white">Personel Giriş - Çıkış Takibi</h1>
                <p class="text-xs text-neutral-500 mt-1">Personellerin günlük devamsızlık durumlarını, mesai başlama saatlerini ve fazla mesailerini kayıt altına alın.</p>
            </div>
            
            <button onclick="toggleModal('attendance-modal')" class="px-4 py-2 bg-violet-600 hover:bg-violet-700 text-xs font-bold text-white rounded-xl transition shadow-lg shadow-violet-950">
                Giriş Çıkış Kaydı Ekle
            </button>
        </div>

        <!-- Devam Giriş-Çıkış Kayıtları -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <x-admin.table.layout>
                <x-slot name="head">
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Tarih</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Personel</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Giriş / Çıkış</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Çalışma Süresi</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Geç Kalma</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Fazla Mesai</th>
                </x-slot>
                <x-slot name="body">
                    @forelse($attendances as $att)
                        <tr>
                            <td class="px-4 py-3 text-xs font-bold font-mono text-neutral-900 dark:text-white">{{ $att->date }}</td>
                            <td class="px-4 py-3 text-xs font-bold">
                                {{ $att->employee->first_name }} {{ $att->employee->last_name }}
                                <div class="text-[10px] font-normal text-neutral-400 mt-0.5">{{ $att->employee->department->name ?? 'Yok' }}</div>
                            </td>
                            <td class="px-4 py-3 text-xs font-mono">
                                <span>Giriş: {{ $att->check_in }}</span>
                                <div class="mt-0.5 text-neutral-500">Çıkış: {{ $att->check_out ?? 'Girilmemiş' }}</div>
                            </td>
                            <td class="px-4 py-3 text-xs font-mono font-bold">{{ $att->worked_minutes }} Dk ({{ round($att->worked_minutes/60, 1) }} Saat)</td>
                            <td class="px-4 py-3 text-xs font-mono text-amber-500">{{ $att->late_minutes > 0 ? $att->late_minutes . ' Dk' : 'Zamanında' }}</td>
                            <td class="px-4 py-3 text-xs font-mono text-green-500">{{ $att->overtime_minutes > 0 ? $att->overtime_minutes . ' Dk' : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-xs text-neutral-400">Giriş çıkış kaydı bulunmamaktadır.</td>
                        </tr>
                    @endforelse
                </x-slot>
            </x-admin.table.layout>
        </div>

        <!-- Giriş Çıkış Ekleme Modal -->
        <div id="attendance-modal" class="fixed inset-0 z-50 hidden bg-neutral-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 p-6 max-w-md w-full shadow-premium space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Giriş Çıkış Kaydı Ekle</h3>
                    <button onclick="toggleModal('attendance-modal')" class="text-neutral-400 hover:text-neutral-600">&times;</button>
                </div>
                
                <form method="POST" action="{{ route('admin.attendance.store') }}" class="space-y-3 text-xs">
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
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Tarih</label>
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="font-bold text-neutral-600 dark:text-neutral-400">Giriş Saati</label>
                            <input type="text" name="check_in" required placeholder="09:00:00" value="09:00:00" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-neutral-600 dark:text-neutral-400">Çıkış Saati</label>
                            <input type="text" name="check_out" placeholder="17:00:00" value="17:00:00" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" onclick="toggleModal('attendance-modal')" class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 font-bold rounded-xl transition">Vazgeç</button>
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
