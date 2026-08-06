@extends('layouts.admin')
@section('title', 'Zimmet Kayıtları')
@section('content')
    <div class="space-y-6">
        
        <!-- Header -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm">
            <h1 class="text-lg font-bold text-slate-900 dark:text-white">Aktif & Geçmiş Zimmet Handovers</h1>
            <p class="text-xs text-slate-500 mt-1">Personellere teslim edilen demirbaşların takibini yapın, iade süreçlerini yönetin.</p>
        </div>

        <!-- Zimmetler Tablosu -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm">
            <x-admin.table.layout>
                <x-slot name="head">
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">Demirbaş</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">Personel</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">Zimmet Tarihi</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">İade Tarihi</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">Durum</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">İşlem</th>
                </x-slot>
                <x-slot name="body">
                    @forelse($assignments as $asg)
                        <tr>
                            <td class="px-4 py-3 text-xs font-bold text-slate-900 dark:text-white">
                                {{ $asg->asset->name ?? 'Silinmiş Demirbaş' }}
                                <div class="text-[10px] font-mono font-normal text-slate-400 mt-0.5">{{ $asg->asset->asset_code ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 text-xs font-bold">
                                {{ $asg->employee->first_name ?? '-' }} {{ $asg->employee->last_name ?? '-' }}
                                <div class="text-[10px] font-normal text-slate-400 mt-0.5">{{ $asg->employee->department->name ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 text-xs font-mono">{{ $asg->assigned_date }}</td>
                            <td class="px-4 py-3 text-xs font-mono text-slate-500">{{ $asg->returned_date ?? '-' }}</td>
                            <td class="px-4 py-3 text-xs">
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold {{ $asg->status === 'assigned' ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700' }}">
                                    {{ $asg->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs">
                                @if($asg->status === 'assigned')
                                    <button onclick="openReturnModal({{ $asg->id }})" class="text-teal-600 hover:underline font-bold">İade Al</button>
                                @else
                                    <span class="text-slate-400">İade Alındı</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-xs text-slate-400">Zimmet kaydı bulunmamaktadır.</td>
                        </tr>
                    @endforelse
                </x-slot>
            </x-admin.table.layout>
        </div>

        <!-- İade Modal -->
        <div id="return-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 p-6 max-w-md w-full shadow-md space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Demirbaş İade Al</h3>
                    <button onclick="toggleModal('return-modal')" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>
                
                <form id="return-form" method="POST" action="" class="space-y-3 text-xs">
                    @csrf
                    
                    <div class="space-y-1">
                        <label class="font-bold text-slate-600 dark:text-slate-400">İade Tarihi</label>
                        <input type="date" name="returned_date" required value="{{ date('Y-m-d') }}" class="w-full p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-slate-600 dark:text-slate-400">Cihaz Durumu / Kondisyon</label>
                        <input type="text" name="condition" placeholder="Temiz, Yıpranmış, Hasarlı" class="w-full p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-slate-600 dark:text-slate-400">Notlar</label>
                        <textarea name="notes" rows="2" class="w-full p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <x-admin.button type="button" onclick="toggleModal('return-modal')" variant="secondary">Vazgeç</x-admin.button>
                        <x-admin.button type="submit" variant="primary">İade İşlemini Tamamla</x-admin.button>
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

        function openReturnModal(id) {
            document.getElementById('return-form').action = `/admin/assets/return/${id}`;
            toggleModal('return-modal');
        }
    </script>
@endsection
