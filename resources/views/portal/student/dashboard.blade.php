@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-3xl font-bold mb-6">Öğrenci Portalı</h1>

    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Profil: {{ $student->first_name }} {{ $student->last_name }}</h2>
        <p class="text-gray-600">Öğrenci No: {{ $student->student_number }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Notifications -->
        <div class="bg-white shadow rounded-lg p-6 md:col-span-2">
            <h3 class="text-2xl font-semibold mb-4 flex items-center justify-between">
                Bildirimler & Duyurular
                @if(auth()->user()->unreadNotifications->count() > 0)
                    <span class="bg-red-500 text-white text-sm px-2 py-1 rounded-full">{{ auth()->user()->unreadNotifications->count() }} Yeni</span>
                @endif
            </h3>
            @if(auth()->user()->notifications->count() > 0)
                <ul class="divide-y divide-gray-200">
                    @foreach(auth()->user()->notifications->take(5) as $notification)
                        <li class="py-4 {{ !$notification->isRead() ? 'bg-indigo-50/50 -mx-6 px-6' : '' }}">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-bold text-gray-900">{{ $notification->data['title'] ?? 'Bildirim' }}</h4>
                                    <p class="text-gray-600 mt-1">{{ $notification->data['message'] ?? '' }}</p>
                                </div>
                                <span class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</span>
                            </div>
                            @if(!$notification->isRead())
                                <form action="{{ route('student.notifications.read', $notification->id) }}" method="POST" class="mt-2">
                                    @csrf
                                    <button type="submit" class="text-xs text-indigo-600 font-semibold hover:text-indigo-800">Okundu Olarak İşaretle</button>
                                </form>
                            @endif
                        </li>
                    @endforeach
                </ul>
                <div class="mt-4 text-right">
                    <a href="{{ route('student.notifications.index') }}" class="text-indigo-600 text-sm font-semibold hover:underline">Tümünü Gör &rarr;</a>
                </div>
            @else
                <p class="text-gray-500 text-center py-4">Henüz bir bildiriminiz bulunmuyor.</p>
            @endif
        </div>

        <!-- Schedule -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-2xl font-semibold mb-4">Ders Programı & Kayıtlar</h3>
            <ul class="divide-y divide-gray-200">
                @foreach($schedule as $item)
                <li class="py-3">
                    <div class="flex justify-between">
                        <span class="font-medium">{{ $item['name'] }}</span>
                        <span class="text-sm text-gray-500">{{ $item['type'] }}</span>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>

        <!-- Attendance -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-2xl font-semibold mb-4">Devam Durumu</h3>
            
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="bg-blue-100 p-4 rounded text-center">
                    <span class="block text-2xl font-bold">{{ $attendanceStats['total'] }}</span>
                    <span class="text-sm">Toplam</span>
                </div>
                <div class="bg-green-100 p-4 rounded text-center">
                    <span class="block text-2xl font-bold">{{ $attendanceStats['present'] }}</span>
                    <span class="text-sm">Mevcut</span>
                </div>
                <div class="bg-red-100 p-4 rounded text-center">
                    <span class="block text-2xl font-bold">{{ $attendanceStats['absent'] }}</span>
                    <span class="text-sm">Yok</span>
                </div>
                <div class="bg-yellow-100 p-4 rounded text-center">
                    <span class="block text-2xl font-bold">{{ $attendanceStats['excused'] }}</span>
                    <span class="text-sm">İzinli</span>
                </div>
            </div>

            <h4 class="text-lg font-semibold mb-2">Son Yoklamalar</h4>
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr>
                        <th class="border-b py-2">Tarih</th>
                        <th class="border-b py-2">Ders</th>
                        <th class="border-b py-2">Durum</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentAttendance as $attendance)
                    <tr>
                        <td class="border-b py-2">{{ $attendance->session->session_date ?? 'Tarih Yok' }}</td>
                        <td class="border-b py-2">{{ optional($attendance->session->course)->name ?? 'Ders Yok' }}</td>
                        <td class="border-b py-2">
                            @if(in_array($attendance->attendance_status_id, ['P', 'Present', 'var', '1']))
                                <span class="text-green-600 font-semibold">Mevcut</span>
                            @elseif(in_array($attendance->attendance_status_id, ['A', 'Absent', 'yok', '2']))
                                <span class="text-red-600 font-semibold">Yok</span>
                            @elseif(in_array($attendance->attendance_status_id, ['L', 'Late', 'gec', '3']))
                                <span class="text-yellow-600 font-semibold">Geç</span>
                            @else
                                <span class="text-blue-600 font-semibold">İzinli</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Exam Results -->
        <div class="bg-white shadow rounded-lg p-6 md:col-span-2">
            <h3 class="text-2xl font-semibold mb-4">Sınav Sonuçları</h3>
            @if(isset($examResults) && $examResults->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr>
                                <th class="border-b py-2">Sınav Tarihi</th>
                                <th class="border-b py-2">Sınav Adı</th>
                                <th class="border-b py-2">Türü</th>
                                <th class="border-b py-2">Puan</th>
                                <th class="border-b py-2">Sıralama</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($examResults as $result)
                            <tr>
                                <td class="border-b py-3">{{ optional($result->exam)->exam_date ? $result->exam->exam_date->format('d.m.Y') : '-' }}</td>
                                <td class="border-b py-3 font-medium">{{ optional($result->exam)->title }}</td>
                                <td class="border-b py-3">{{ optional(optional($result->exam)->type)->name ?? '-' }}</td>
                                <td class="border-b py-3 font-bold text-indigo-600">{{ $result->score }}</td>
                                <td class="border-b py-3">
                                    @if($result->rank)
                                        <span class="bg-indigo-100 text-indigo-800 px-2 py-1 rounded-full text-xs font-semibold">{{ $result->rank }}. Sıra</span>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-center py-4">Henüz açıklanan bir sınav sonucunuz bulunmuyor.</p>
            @endif
        </div>
    </div>
</div>
@endsection
