@extends('layouts.admin')
@section('title', 'Devamsızlık Analitiği & Risk Raporu')
@section('content')
    <x-admin.crud.index-layout title="Devamsızlık Analitiği & Riskli Öğrenciler" description="Devamsızlık oranlarını takip edin ve %15 eşiğini aşan öğrencileri tespit edin.">
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Toplam İşlenen Oturum</h4>
                <div class="text-2xl font-bold">{{ $summary['total_sessions'] }} Oturum</div>
            </div>
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Genel Katılım Oranı</h4>
                <div class="text-2xl font-bold text-green-600">%{{ $summary['overall_attendance_rate'] }}</div>
            </div>
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">İşlenen Yoklama İmzası</h4>
                <div class="text-2xl font-bold">{{ $summary['total_attendances'] }} Kayıt</div>
            </div>
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Riskli Öğrenci Sayısı</h4>
                <div class="text-2xl font-bold text-red-600">{{ $summary['risk_students_count'] }} Öğrenci</div>
            </div>
        </div>

        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 shadow-premium-sm space-y-4">
            <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Riskli Öğrenciler Listesi (%15 Üzeri Devamsızlık)</h3>
            <x-admin.table.layout>
                <x-slot name="head">
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Öğrenci No</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Ad Soyad</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Şube / Sınıf</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Devamsız / Toplam</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Devamsızlık Oranı</th>
                </x-slot>
                <x-slot name="body">
                    @forelse($riskStudents as $student)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40">
                            <td class="px-4 py-3 text-xs font-bold text-neutral-900">{{ $student->student_number }}</td>
                            <td class="px-4 py-3 text-xs font-semibold text-neutral-900 dark:text-white">{{ $student->full_name }}</td>
                            <td class="px-4 py-3 text-xs text-neutral-500">{{ $student->branch->name ?? '--' }} / {{ $student->classroom->name ?? '--' }}</td>
                            <td class="px-4 py-3 text-xs font-medium text-neutral-800">{{ $student->absent_count }} / {{ $student->total_sessions }} Oturum</td>
                            <td class="px-4 py-3 text-xs">
                                <span class="px-2.5 py-1 text-[10px] font-bold bg-red-100 text-red-800 rounded-full">
                                    %{{ $student->absence_rate }} Devamsız
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-xs text-neutral-400">Risk sınırında devamsızlığı bulunan öğrenci bulunmamaktadır.</td>
                        </tr>
                    @endforelse
                </x-slot>
            </x-admin.table.layout>
        </div>

    </x-admin.crud.index-layout>
@endsection
