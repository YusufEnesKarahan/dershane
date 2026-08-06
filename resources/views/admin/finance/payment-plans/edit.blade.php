@extends('layouts.admin')
@section('title', 'Ödeme Planı Güncelle')
@section('content')
    <x-admin.crud.form-layout title="Ödeme Planını Düzenle" description="Öğrencinin ödeme planı detaylarını güncelleyin." backRoute="{{ route('admin.payment-plans.index') }}">
        <x-admin.form.layout action="{{ route('admin.payment-plans.update', $paymentPlan->id) }}" method="POST">
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Student -->
                <x-admin.form.field-group label="Öğrenci" id="student_id" :error="$errors->first('student_id')" required>
                    <select name="student_id" class="w-full text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors" required>
                        <option value="">Öğrenci Seçin...</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id', $paymentPlan->student_id) == $student->id ? 'selected' : '' }}>
                                {{ $student->first_name }} {{ $student->last_name }} ({{ $student->student_number }})
                            </option>
                        @endforeach
                    </select>
                </x-admin.form.field-group>

                <!-- Total Installments -->
                <x-admin.form.field-group label="Taksit Sayısı" id="total_installments" :error="$errors->first('total_installments')" required>
                    <input type="number" min="1" max="24" name="total_installments" value="{{ old('total_installments', $paymentPlan->total_installments) }}" class="w-full text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors" required>
                </x-admin.form.field-group>

                <!-- Installment Amount -->
                <x-admin.form.field-group label="Taksit Tutarı (Aylık)" id="installment_amount" :error="$errors->first('installment_amount')" required>
                    <input type="number" step="0.01" min="0.01" name="installment_amount" value="{{ old('installment_amount', $paymentPlan->installment_amount) }}" class="w-full text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors" required>
                </x-admin.form.field-group>

                <!-- Start Date -->
                <x-admin.form.field-group label="Taksit Başlangıç Tarihi" id="start_date" :error="$errors->first('start_date')" required>
                    <input type="date" name="start_date" value="{{ old('start_date', \Carbon\Carbon::parse($paymentPlan->start_date)->format('Y-m-d')) }}" class="w-full text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors" required>
                </x-admin.form.field-group>
            </div>

            <div class="flex justify-end pt-4 border-t border-slate-200 dark:border-slate-700">
                <x-admin.button type="submit" variant="primary">
                    Güncelle
                </x-admin.button>
            </div>
        </x-admin.form.layout>
    </x-admin.crud.form-layout>
@endsection
