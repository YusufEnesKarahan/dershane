@extends('layouts.admin')

@section('title', 'Yoklama Raporları')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Yoklama Raporları</h2>
        <a href="{{ route('admin.attendance.index') }}" class="text-gray-500 hover:text-gray-700 font-medium">&larr; Yoklama Yönetimi</a>
    </div>

    <!-- Filters -->
    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <form action="{{ route('admin.attendance.report') }}" method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700">Başlangıç Tarihi</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="mt-1 block rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Bitiş Tarihi</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="mt-1 block rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Sınıf</label>
                <select name="classroom_id" class="mt-1 block rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Tümü</option>
                    @foreach($classrooms as $c)
                        <option value="{{ $c->id }}" {{ request('classroom_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition shadow">Filtrele</button>
            </div>
        </form>
    </div>

    <!-- Summary Widgets -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col items-center justify-center">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Katılım Oranı</span>
            <span class="text-3xl font-bold text-indigo-600">{{ $summary['attendance_rate'] }}%</span>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col items-center justify-center">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Toplam Kayıt</span>
            <span class="text-3xl font-bold text-gray-900">{{ $summary['total'] }}</span>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col items-center justify-center">
            <span class="text-xs font-semibold text-green-600 uppercase tracking-wider mb-2">Mevcut (P)</span>
            <span class="text-3xl font-bold text-green-600">{{ $summary['present'] }}</span>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col items-center justify-center">
            <span class="text-xs font-semibold text-red-600 uppercase tracking-wider mb-2">Yok (A)</span>
            <span class="text-3xl font-bold text-red-600">{{ $summary['absent'] }}</span>
        </div>
    </div>

    <!-- Details Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800">Detaylı Kayıtlar</h3>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Öğrenci</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ders</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($records as $record)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ \Carbon\Carbon::parse($record->session->session_date)->format('d.m.Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $record->student->first_name }} {{ $record->student->last_name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $record->session->course->name ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $record->status->code == 'P' ? 'bg-green-100 text-green-800' : ($record->status->code == 'A' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ $record->status->name }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                        Seçilen kriterlere uygun yoklama kaydı bulunamadı.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
