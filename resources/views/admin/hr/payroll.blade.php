@extends('layouts.admin')
@section('title', 'Maaş Bordroları')
@section('content')
    <x-admin.crud.index-layout title="Maaş Bordroları & Ödemeler" description="Personellerin dönemsel maaş hak edişlerini, ek ödeneklerini, kesintilerini ve vergi matrahlarını hesaplayın.">
        <x-slot name="actions">
            <button onclick="toggleModal('payroll-modal')" class="inline-flex items-center gap-2 px-4 py-2 bg-violet-600 hover:bg-violet-500 text-white text-xs font-bold rounded-xl transition-colors shadow-lg shadow-violet-900/20 border border-violet-500/50">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Bordro Hesapla
            </button>
        </x-slot>

        <!-- Bordrolar Tablosu -->
        <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm overflow-hidden flex flex-col h-full">
            <div class="p-0 flex-1">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-800">
                        <thead class="bg-neutral-50/80 dark:bg-neutral-900/80 backdrop-blur-sm">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider bg-neutral-50/50 dark:bg-neutral-800/30">Dönem</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider bg-neutral-50/50 dark:bg-neutral-800/30">Personel</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider bg-neutral-50/50 dark:bg-neutral-800/30">Brüt / Net Maaş</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider bg-neutral-50/50 dark:bg-neutral-800/30">Sosyal Hak & Kesinti</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider bg-neutral-50/50 dark:bg-neutral-800/30">Durum</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider bg-neutral-50/50 dark:bg-neutral-800/30 w-32">İşlem</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800/50 bg-white dark:bg-neutral-900">
                            @forelse($payrolls as $pay)
                                <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40 transition-colors border-b border-neutral-100 dark:border-neutral-800/50 last:border-0 group">
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-neutral-100 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 text-xs font-bold text-neutral-700 dark:text-neutral-300 font-mono">
                                            {{ $pay->month }}/{{ $pay->year }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-neutral-900 dark:text-white">{{ $pay->employee->first_name }} {{ $pay->employee->last_name }}</div>
                                        <div class="text-[11px] text-neutral-500 dark:text-neutral-400 mt-1">{{ $pay->employee->position->name ?? 'Yok' }}</div>
                                    </td>
                                    <td class="px-6 py-4 font-mono">
                                        <div class="text-[11px] text-neutral-500 dark:text-neutral-400 mb-0.5">Brüt: ₺{{ number_format($pay->gross_salary, 2) }}</div>
                                        <div class="text-sm font-bold text-neutral-900 dark:text-white">Net: ₺{{ number_format($pay->net_salary, 2) }}</div>
                                    </td>
                                    <td class="px-6 py-4 font-mono">
                                        <div class="text-[11px] text-neutral-500 dark:text-neutral-400 mb-0.5">Ek: ₺{{ number_format($pay->bonus + $pay->overtime_amount, 2) }}</div>
                                        <div class="text-[11px] text-red-500 dark:text-red-400">Kesinti: ₺{{ number_format($pay->deductions, 2) }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($pay->status === 'Paid')
                                            <x-admin.badge variant="success" dot="true">Ödendi</x-admin.badge>
                                        @elseif($pay->status === 'Approved')
                                            <x-admin.badge variant="info" dot="true">Onaylandı</x-admin.badge>
                                        @else
                                            <x-admin.badge variant="warning" dot="true">Taslak</x-admin.badge>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            @if($pay->status === 'Draft')
                                                <form method="POST" action="{{ route('admin.payroll.approve', $pay->id) }}" class="inline-block">
                                                    @csrf
                                                    <x-admin.button type="submit" variant="primary" size="sm">Onayla</x-admin.button>
                                                </form>
                                            @elseif($pay->status === 'Approved')
                                                <form method="POST" action="{{ route('admin.payroll.pay', $pay->id) }}" class="inline-block">
                                                    @csrf
                                                    <x-admin.button type="submit" variant="success" size="sm">Ödeme Yap</x-admin.button>
                                                </form>
                                            @else
                                                <span class="text-[10px] font-medium text-neutral-400 flex items-center justify-end gap-1"><svg class="w-3 h-3 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg> Ödendi ({{ $pay->payment_date }})</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <x-admin.empty-state
                                            icon="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                            title="Bordro Bulunamadı"
                                            description="Sistemde henüz hesaplanmış bir maaş bordrosu bulunmuyor."
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

        <!-- Bordro Hesapla Modal -->
        <div id="payroll-modal" class="fixed inset-0 z-50 hidden bg-neutral-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 p-6 max-w-md w-full shadow-premium space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Yeni Maaş Bordrosu Hesapla</h3>
                    <button onclick="toggleModal('payroll-modal')" class="text-neutral-400 hover:text-neutral-600">&times;</button>
                </div>
                
                <form method="POST" action="{{ route('admin.payroll.store') }}" class="space-y-3 text-xs">
                    @csrf
                    
                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Personel</label>
                        <select name="employee_id" required class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }} (Maaş: ₺{{ number_format($emp->salary, 2) }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="font-bold text-neutral-600 dark:text-neutral-400">Ay</label>
                            <select name="month" required class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                                @for($m=1; $m<=12; $m++)
                                    <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>{{ $m }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-neutral-600 dark:text-neutral-400">Yıl</label>
                            <input type="number" name="year" required value="{{ date('Y') }}" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Prim / Bonus</label>
                        <input type="number" name="bonus" step="0.01" value="0.00" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Mesai Ücreti</label>
                        <input type="number" name="overtime_amount" step="0.01" value="0.00" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Kesintiler</label>
                        <input type="number" name="deductions" step="0.01" value="0.00" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" onclick="toggleModal('payroll-modal')" class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 font-bold rounded-xl transition">Vazgeç</button>
                        <button type="submit" class="px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white font-bold rounded-xl transition">Bordro Oluştur</button>
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
