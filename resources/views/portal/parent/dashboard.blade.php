@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-3xl font-bold mb-6">Veli Portalı</h1>

    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Hoş Geldiniz, {{ $guardian->guardian_name }}</h2>
    </div>

    <!-- Notifications -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h3 class="text-2xl font-semibold mb-4 flex items-center justify-between">
            Bildirimler & Duyurular
            @if(auth()->user()->unreadNotifications->count() > 0)
                <span class="bg-red-500 text-white text-sm px-2 py-1 rounded-full">{{ auth()->user()->unreadNotifications->count() }} Yeni</span>
            @endif
        </h3>
        @if(auth()->user()->notifications->count() > 0)
            <ul class="divide-y divide-slate-200">
                @foreach(auth()->user()->notifications->take(5) as $notification)
                    <li class="py-4 {{ !$notification->isRead() ? 'bg-blue-50/50 -mx-6 px-6' : '' }}">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-bold text-slate-900">{{ $notification->data['title'] ?? 'Bildirim' }}</h4>
                                <p class="text-slate-600 mt-1">{{ $notification->data['message'] ?? '' }}</p>
                            </div>
                            <span class="text-xs text-slate-500">{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                        @if(!$notification->isRead())
                            <form action="{{ route('parent.notifications.read', $notification->id) }}" method="POST" class="mt-2">
                                @csrf
                                <button type="submit" class="text-xs text-blue-600 font-semibold hover:text-blue-800">Okundu Olarak İşaretle</button>
                            </form>
                        @endif
                    </li>
                @endforeach
            </ul>
            <div class="mt-4 text-right">
                <a href="{{ route('parent.notifications.index') }}" class="text-blue-600 text-sm font-semibold hover:underline">Tümünü Gör &rarr;</a>
            </div>
        @else
            <p class="text-slate-500 text-center py-4">Henüz bir bildiriminiz bulunmuyor.</p>
        @endif
    </div>

    @foreach($childrenData as $data)
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h3 class="text-2xl font-semibold mb-2">Öğrenci: {{ $data['student']->first_name }} {{ $data['student']->last_name }}</h3>
        <p class="text-slate-600 mb-4">Öğrenci No: {{ $data['student']->student_number }}</p>
        
        <h4 class="text-lg font-semibold mb-2">Devam Durumu</h4>
        <div class="grid grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-100 p-4 rounded text-center">
                <span class="block text-2xl font-bold">{{ $data['attendance_stats']['total'] }}</span>
                <span class="text-sm">Toplam</span>
            </div>
            <div class="bg-green-100 p-4 rounded text-center">
                <span class="block text-2xl font-bold">{{ $data['attendance_stats']['present'] }}</span>
                <span class="text-sm">Mevcut</span>
            </div>
            <div class="bg-red-100 p-4 rounded text-center">
                <span class="block text-2xl font-bold">{{ $data['attendance_stats']['absent'] }}</span>
                <span class="text-sm">Yok</span>
            </div>
            <div class="bg-yellow-100 p-4 rounded text-center">
                <span class="block text-2xl font-bold">{{ $data['attendance_stats']['excused'] }}</span>
                <span class="text-sm">İzinli</span>
            </div>
        </div>

        <h4 class="text-lg font-semibold mb-2">Son Yoklamalar</h4>
        <table class="w-full text-left border-collapse">
            <thead>
                <tr>
                    <th class="border-b py-2">Tarih</th>
                    <th class="border-b py-2">Ders</th>
                    <th class="border-b py-2">Sınıf</th>
                    <th class="border-b py-2">Durum</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['recent_attendance'] as $attendance)
                <tr>
                    <td class="border-b py-2">{{ $attendance->session->session_date ?? 'Tarih Yok' }}</td>
                    <td class="border-b py-2">{{ optional($attendance->session->course)->name ?? 'Ders Yok' }}</td>
                    <td class="border-b py-2">{{ optional($attendance->session->classroom)->name ?? 'Sınıf Yok' }}</td>
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
        
        <!-- Exam Results -->
        <h4 class="text-lg font-semibold mt-6 mb-2">Sınav Sonuçları</h4>
        @if(isset($data['exam_results']) && count($data['exam_results']) > 0)
            <div class="overflow-x-auto mb-4">
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
                        @foreach($data['exam_results'] as $result)
                        <tr>
                            <td class="border-b py-2">{{ optional($result->exam)->exam_date ? $result->exam->exam_date->format('d.m.Y') : '-' }}</td>
                            <td class="border-b py-2 font-medium">{{ optional($result->exam)->title }}</td>
                            <td class="border-b py-2">{{ optional(optional($result->exam)->type)->name ?? '-' }}</td>
                            <td class="border-b py-2 font-bold text-blue-600">{{ $result->score }}</td>
                            <td class="border-b py-2">
                                @if($result->rank)
                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-semibold">{{ $result->rank }}. Sıra</span>
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
            <p class="text-slate-500 text-sm mb-4">Açıklanan sınav sonucu bulunmuyor.</p>
        @endif
    </div>
    @endforeach

</div>
@endsection
