@extends('layouts.admin')

@section('title', 'Yoklama Al')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Yoklama Al: {{ $session->classroom->name ?? 'Bilinmeyen Sınıf' }}</h2>
        <a href="{{ route('admin.attendance.index') }}" class="text-gray-500 hover:text-gray-700 font-medium">&larr; Geri Dön</a>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-50 border-l-4 border-green-500 text-green-700">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col justify-center">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Ders Bilgisi</span>
            <span class="text-lg font-bold text-gray-900">{{ $session->course->name ?? '-' }}</span>
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

    <form action="{{ route('admin.attendance.storeBulk', $session->id) }}" method="POST">
        @csrf
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Öğrenci Adı</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Açıklama / Not</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($students as $student)
                        @php
                            $existingRecord = $session->attendances->firstWhere('student_id', $student->id);
                        @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold">
                                        {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $student->first_name }} {{ $student->last_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $student->student_number }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center space-x-4">
                                @foreach($statuses as $status)
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="attendances[{{ $student->id }}][attendance_status_id]" value="{{ $status->id }}" 
                                           {{ ($existingRecord && $existingRecord->attendance_status_id == $status->id) ? 'checked' : ($status->code == 'P' && !$existingRecord ? 'checked' : '') }}
                                           class="form-radio h-4 w-4 {{ $status->code == 'P' ? 'text-green-600' : ($status->code == 'A' ? 'text-red-600' : 'text-yellow-600') }}">
                                    <span class="ml-2 text-sm font-medium {{ $status->code == 'P' ? 'text-green-700' : ($status->code == 'A' ? 'text-red-700' : 'text-yellow-700') }}">{{ $status->name }}</span>
                                </label>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <input type="text" name="attendances[{{ $student->id }}][remarks]" value="{{ $existingRecord->remarks ?? '' }}" placeholder="Opsiyonel not..." class="block w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                            Bu sınıfa kayıtlı öğrenci bulunmuyor.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            @if($students->count() > 0)
            <div class="p-6 bg-gray-50 border-t border-gray-200 flex justify-end">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg shadow font-medium hover:bg-blue-700 transition">Yoklamayı Kaydet</button>
            </div>
            @endif
        </div>
    </form>
</div>
@endsection
