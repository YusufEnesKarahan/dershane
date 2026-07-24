@extends('layouts.admin')
@section('title', 'Maaş Bordroları')
@section('content')
    <div class="space-y-6">
        
        <!-- Header -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm flex justify-between items-center">
            <div>
                <h1 class="text-lg font-bold text-neutral-900 dark:text-white">Maaş Bordroları & Ödemeler</h1>
                <p class="text-xs text-neutral-500 mt-1">Personellerin dönemsel maaş hak edişlerini, ek ödeneklerini, kesintilerini ve vergi matrahlarını hesaplayın.</p>
            </div>
            
            <button onclick="toggleModal('payroll-modal')" class="px-4 py-2 bg-violet-600 hover:bg-violet-700 text-xs font-bold text-white rounded-xl transition shadow-lg shadow-violet-950">
                Bordro Hesapla
            </button>
        </div>

        <!-- Bordrolar Tablosu -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <x-admin.table.layout>
                <x-slot name="head">
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Dönem</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Personel</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Brüt / Net Maaş</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Sosyal Hak & Kesinti</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Durum</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">İşlem</th>
                </x-slot>
                <x-slot name="body">
                    @forelse($payrolls as $pay)
                        <tr>
                            <td class="px-4 py-3 text-xs font-bold font-mono text-neutral-900 dark:text-white">{{ $pay->month }}/{{ $pay->year }}</td>
                            <td class="px-4 py-3 text-xs">
                                <span class="font-bold text-neutral-800 dark:text-neutral-200">{{ $pay->employee->first_name }} {{ $pay->employee->last_name }}</span>
                                <div class="text-[10px] text-neutral-400 mt-0.5">{{ $pay->employee->position->name ?? 'Yok' }}</div>
                            </td>
                            <td class="px-4 py-3 text-xs font-mono">
                                <div>Brüt: ₺{{ number_format($pay->gross_salary, 2) }}</div>
                                <div class="font-bold text-neutral-900 dark:text-white mt-0.5">Net: ₺{{ number_format($pay->net_salary, 2) }}</div>
                            </td>
                            <td class="px-4 py-3 text-xs font-mono">
                                <div>Ek: ₺{{ number_format($pay->bonus + $pay->overtime_amount, 2) }}</div>
                                <div class="text-red-500 mt-0.5">Kesinti: ₺{{ number_format($pay->deductions, 2) }}</div>
                            </td>
                            <td class="px-4 py-3 text-xs">
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold {{ $pay->status === 'Paid' ? 'bg-green-100 text-green-700' : ($pay->status === 'Approved' ? 'bg-blue-100 text-blue-700' : 'bg-neutral-100 text-neutral-700') }}">
                                    {{ $pay->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs space-x-2">
                                @if($pay->status === 'Draft')
                                    <form method="POST" action="{{ route('admin.payroll.approve', $pay->id) }}" class="inline-block">
                                        @csrf
                                        <button type="submit" class="text-blue-600 hover:underline font-bold">Onayla</button>
                                    </form>
                                @elseif($pay->status === 'Approved')
                                    <form method="POST" action="{{ route('admin.payroll.pay', $pay->id) }}" class="inline-block">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:underline font-bold">Ödeme Yap</button>
                                    </form>
                                @else
                                    <span class="text-neutral-400">Ödendi ({{ $pay->payment_date }})</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-xs text-neutral-400">Bordro kaydı bulunmamaktadır.</td>
                        </tr>
                    @endforelse
                </x-slot>
            </x-admin.table.layout>
        </div>

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
