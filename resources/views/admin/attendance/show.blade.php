@extends('layouts.admin')

@section('title', 'Yoklama Detayı')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Yoklama Detayı</h2>
        <a href="{{ route('admin.attendance.index') }}" class="text-gray-500 hover:text-gray-700 font-medium">&larr; Geri Dön</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col justify-center">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Ders & Sınıf</span>
            <span class="text-lg font-bold text-gray-900">{{ $session->course->name ?? '-' }} ({{ $session->classroom->name ?? '-' }})</span>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col justify-center">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Öğretmen</span>
            <span class="text-lg font-bold text-gray-900">{{ $session->teacher->user->name ?? '-' }}</span>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col justify-center">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Zaman</span>
            <span class="text-lg font-bold text-gray-900">
                {{ \Carbon\Carbon::parse($session->session_date)->format('d.m.Y') }} | 
                {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
            </span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Öğrenci Yoklama Listesi</h3>
            @can('attendance.update')
            <a href="{{ route('admin.attendance.take', $session->id) }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">Düzenle</a>
            @endcan
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Öğrenci Adı</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Açıklama / Not</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($session->attendances as $record)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $record->student->first_name }} {{ $record->student->last_name }}</div>
                        <div class="text-sm text-gray-500">{{ $record->student->student_number }}</div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $record->status->code == 'P' ? 'bg-green-100 text-green-800' : ($record->status->code == 'A' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ $record->status->name }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-700">{{ $record->remarks ?? '-' }}</div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                        Bu oturum için yoklama kaydı bulunamadı.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
