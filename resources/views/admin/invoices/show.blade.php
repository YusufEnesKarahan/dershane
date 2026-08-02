@extends('layouts.admin')
@section('title', 'Fatura Detay & Tahsilat Alma')
@section('content')
    <x-admin.crud.index-layout title="Fatura Detayı: {{ $invoice->invoice_number }}" description="Öğrenci: {{ $invoice->student->full_name }} — Toplam Tutar: ₺{{ number_format($invoice->total_amount, 2) }}">
        <x-slot name="actions">
            <a href="{{ route('admin.invoices.index') }}" class="px-4 py-2 bg-neutral-100 text-neutral-700 text-xs font-semibold rounded-xl hover:bg-neutral-200 transition">
                Fatura Listesine Dön
            </a>
        </x-slot>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sol Panel: Tahsilat Alma Formu -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Tahsilat Kaydı İşle</h3>
                
                <x-admin.form.layout :action="route('admin.payments.store')" method="POST">
                    <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                    
                    <x-admin.form.field-group label="Makbuz No (Benzersiz)" id="payment_number">
                        <input type="text" name="payment_number" required value="PAY-{{ date('Y') }}-{{ rand(100,999) }}" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5">
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Ödeme Yöntemi" id="payment_method_id">
                        <select name="payment_method_id" required class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5">
                            @foreach($paymentMethods as $pm)
                                <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                            @endforeach
                        </select>
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Tahsilat Tutarı (₺)" id="amount">
                        <input type="number" step="0.01" name="amount" required value="{{ max(0, $invoice->total_amount - $invoice->paid_amount) }}" class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5 font-bold text-green-600">
                    </x-admin.form.field-group>

                    <x-admin.form.field-group label="Notlar / Açıklama" id="notes">
                        <textarea name="notes" rows="2" placeholder="Nakit ödeme alındı..." class="w-full text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-3 py-2.5"></textarea>
                    </x-admin.form.field-group>

                    <div class="pt-4">
                        <x-admin.button type="submit" variant="primary" class="w-full">
                            Tahsilatı Kaydet & Borcu Düş
                        </x-admin.button>
                    </div>

                </x-admin.form.layout>
            </div>

            <!-- Sağ Panel: Fatura Detayı ve Yapılan Ödemeler -->
            <div class="lg:col-span-2 bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-6">
                
                <div>
                    <h3 class="text-sm font-bold text-neutral-900 dark:text-white mb-4">Fatura Hizmet Kalemleri</h3>
                    <x-admin.table.layout>
                        <x-slot name="head">
                            <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Açıklama</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Adet</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Birim Fiyat</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Toplam</th>
                        </x-slot>
                        <x-slot name="body">
                            @foreach($invoice->items as $item)
                                <tr>
                                    <td class="px-4 py-3 text-xs font-bold text-neutral-900">{{ $item->description }}</td>
                                    <td class="px-4 py-3 text-xs">{{ $item->quantity }}</td>
                                    <td class="px-4 py-3 text-xs">₺{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="px-4 py-3 text-xs font-bold text-primary">₺{{ number_format($item->total_price, 2) }}</td>
                                </tr>
                            @endforeach
                        </x-slot>
                    </x-admin.table.layout>
                </div>

                <div>
                    <h3 class="text-sm font-bold text-neutral-900 dark:text-white mb-4">Yapılan Tahsilatlar Geçmişi</h3>
                    <x-admin.table.layout>
                        <x-slot name="head">
                            <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Makbuz No</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Yöntem</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Tarih</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Tutar</th>
                        </x-slot>
                        <x-slot name="body">
                            @forelse($invoice->payments as $pay)
                                <tr>
                                    <td class="px-4 py-3 text-xs font-bold text-neutral-900">{{ $pay->payment_number }}</td>
                                    <td class="px-4 py-3 text-xs text-neutral-500">{{ $pay->paymentMethod->name ?? 'Nakit' }}</td>
                                    <td class="px-4 py-3 text-xs text-neutral-500 font-mono">{{ \Carbon\Carbon::parse($pay->payment_date)->format('d.m.Y H:i') }}</td>
                                    <td class="px-4 py-3 text-xs font-bold text-green-600">₺{{ number_format($pay->amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-xs text-neutral-400">Bu fatura için henüz tahsilat yapılmamıştır.</td>
                                </tr>
                            @endforelse
                        </x-slot>
                    </x-admin.table.layout>
                </div>

            </div>

        </div>
    </x-admin.crud.index-layout>
@endsection
