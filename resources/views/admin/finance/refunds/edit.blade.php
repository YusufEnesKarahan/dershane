@extends('layouts.admin')
@section('title', 'İade Güncelle')
@section('content')
    <x-admin.crud.form-layout title="İade Talebini Düzenle" description="Oluşturulmuş iade talebi bilgilerini güncelleyin." backRoute="{{ route('admin.refunds.index') }}">
        <x-admin.form.layout action="{{ route('admin.refunds.update', $refund->id) }}" method="POST">
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Payment -->
                <div class="md:col-span-2">
                    <x-admin.form.field-group label="İlişkili Tahsilat (Ödeme)" id="payment_id" :error="$errors->first('payment_id')" required>
                        <select name="payment_id" class="w-full text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors" required>
                            <option value="">Tahsilat Seçin...</option>
                            @foreach($payments as $payment)
                                <option value="{{ $payment->id }}" {{ old('payment_id', $refund->payment_id) == $payment->id ? 'selected' : '' }}>
                                    {{ optional($payment->student)->first_name }} {{ optional($payment->student)->last_name }} - Ödeme #{{ $payment->payment_number ?? $payment->id }} - ({{ number_format($payment->amount, 2) }} TL)
                                </option>
                            @endforeach
                        </select>
                    </x-admin.form.field-group>
                </div>

                <!-- Amount -->
                <x-admin.form.field-group label="İade Tutarı (TL)" id="amount" :error="$errors->first('amount')" required>
                    <input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount', $refund->amount) }}" class="w-full text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors" required>
                </x-admin.form.field-group>

                <!-- Refund Date -->
                <x-admin.form.field-group label="İade Tarihi" id="refund_date" :error="$errors->first('refund_date')" required>
                    <input type="date" name="refund_date" value="{{ old('refund_date', \Carbon\Carbon::parse($refund->refund_date)->format('Y-m-d')) }}" class="w-full text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors" required>
                </x-admin.form.field-group>

                <!-- Reason -->
                <div class="md:col-span-2">
                    <x-admin.form.field-group label="İade Gerekçesi" id="reason" :error="$errors->first('reason')" required>
                        <textarea name="reason" rows="3" class="w-full text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors resize-none" required>{{ old('reason', $refund->reason) }}</textarea>
                    </x-admin.form.field-group>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-slate-200 dark:border-slate-700">
                <x-admin.button type="submit" variant="primary">
                    Güncelle
                </x-admin.button>
            </div>
        </x-admin.form.layout>
    </x-admin.crud.form-layout>
@endsection
