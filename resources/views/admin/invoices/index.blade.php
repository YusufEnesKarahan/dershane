@extends('layouts.admin')
@section('title', 'Finans & Fatura Yönetimi')
@section('content')
    <x-admin.crud.index-layout title="Fatura & Tahsilat Yönetimi" description="Öğrenci faturalarını kesin, taksit ve borç hesaplarını takip edin, tahsilatları yönetin.">
        <x-slot name="actions">
            <a href="{{ route('admin.invoices.dashboard') }}" class="px-4 py-2 bg-neutral-100 text-neutral-700 text-xs font-semibold rounded-xl hover:bg-neutral-200 transition">
                Finans Dashboard
            </a>
        </x-slot>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Panel: Yeni Fatura Kes -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Yeni Öğrenci Faturası Oluştur</h3>
                
                <x-admin.form.layout :action="route('admin.invoices.store')" method="POST">
                    
                    <x-admin.form.field-group label="Fatura No (Benzersiz)" id="invoice_number">
                        <input type="text" name="invoice_number" required value="INV-{{ date('Y') }}-001" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Öğrenci" id="student_id">
                        <select name="student_id" required class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                            @foreach($students as $s)
                                <option value="{{ $s->id }}">{{ $s->full_name }} ({{ $s->student_number }})</option>
                            @endforeach
                        </select>
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Fatura Hizmet Kalemi" id="description">
                        <input type="text" name="description" required value="2026-2027 Eğitim Dönem Kayıt Ücreti" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                    </x-admin.form.field-group>

                    <div class="grid grid-cols-2 gap-3">
                        <x-admin.form.field-group label="Düzenlenme Tarihi" id="issue_date">
                            <input type="date" name="issue_date" required value="{{ date('Y-m-d') }}" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                        </x-admin.form.field-group>

                        <x-admin.form.field-group label="Son Ödeme Tarihi" id="due_date">
                            <input type="date" name="due_date" required value="{{ date('Y-m-d', strtotime('+30 days')) }}" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2">
                        </x-admin.form.field-group>
                    </div>

                    <x-admin.form.field-group label="Toplam Tutar (₺)" id="amount">
                        <input type="number" step="0.01" name="amount" required value="25000.00" class="w-full text-sm bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2 font-bold text-primary">
                    </x-admin.form.field-group>

                    <div class="pt-4">
                        <button type="submit" class="w-full py-2.5 bg-primary text-white text-xs font-semibold rounded-xl hover:bg-primary-dark transition shadow-sm">
                            Faturayı Kes & Borç Hesabı Aç
                        </button>
                    </div>

                </x-admin.form.layout>
            </div>

            <!-- Sağ Panel: Kesilen Faturalar -->
            <div class="lg:col-span-2 bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Kesilen Faturalar</h3>
                
                <x-admin.table.layout>
                    <x-slot name="head">
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Fatura No</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Öğrenci / Şube</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Vade</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Tutar / Tahsilat</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Durum</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">İşlem</th>
                    </x-slot>
                    <x-slot name="body">
                        @forelse($invoices as $inv)
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40">
                                <td class="px-4 py-3 text-xs font-bold text-neutral-900">
                                    {{ $inv->invoice_number }}
                                </td>
                                <td class="px-4 py-3 text-xs font-semibold text-neutral-800">
                                    {{ $inv->student->full_name }}
                                    <div class="text-[10px] text-neutral-400 font-normal">{{ $inv->student->branch->name ?? '--' }}</div>
                                </td>
                                <td class="px-4 py-3 text-xs text-neutral-500 font-mono">
                                    {{ \Carbon\Carbon::parse($inv->due_date)->format('d.m.Y') }}
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    <div class="font-bold text-neutral-900">₺{{ number_format($inv->total_amount, 2) }}</div>
                                    <div class="text-[10px] text-green-600 font-bold">Ödenen: ₺{{ number_format($inv->paid_amount, 2) }}</div>
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full {{ $inv->status === 'Paid' ? 'bg-green-100 text-green-800' : ($inv->status === 'Partial' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $inv->status === 'Paid' ? 'Ödendi' : ($inv->status === 'Partial' ? 'Kısmi Ödeme' : 'Bekliyor') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    <a href="{{ route('admin.invoices.show', $inv->id) }}" class="px-3 py-1 bg-primary/10 text-primary text-[11px] font-bold rounded-lg hover:bg-primary/20 transition">
                                        Detay & Tahsilat
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-xs text-neutral-400">Henüz fatura kaydı bulunmamaktadır.</td>
                            </tr>
                        @endforelse
                    </x-slot>
                </x-admin.table.layout>
            </div>

        </div>
    </x-admin.crud.index-layout>
@endsection
