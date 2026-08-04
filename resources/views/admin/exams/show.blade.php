@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center">
            <a href="{{ route('admin.exams.index') }}" class="text-indigo-600 hover:text-indigo-800 mr-4 text-xl">&larr;</a>
            <h1 class="text-3xl font-bold text-gray-800">{{ $exam->title }}</h1>
        </div>
        <div>
            <a href="{{ route('admin.exams.results', $exam) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow mr-2">Sonuç Girişi</a>
            <a href="{{ route('admin.exams.edit', $exam) }}" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg shadow">Düzenle</a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white shadow rounded-lg p-6 md:col-span-2">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">Sınav Bilgileri</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500 font-semibold">Sınav Türü</p>
                    <p class="text-lg text-gray-900">{{ optional($exam->type)->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-semibold">Sınıf</p>
                    <p class="text-lg text-gray-900">{{ optional($exam->classroom)->name ?? 'Tüm Sınıflar' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-semibold">Tarih</p>
                    <p class="text-lg text-gray-900">{{ $exam->exam_date->format('d.m.Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-semibold">Süre</p>
                    <p class="text-lg text-gray-900">{{ $exam->duration_minutes }} Dakika</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-semibold">Toplam Puan</p>
                    <p class="text-lg text-gray-900">{{ $exam->total_score }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-semibold">Durum</p>
                    <p class="text-lg text-gray-900">
                        @if($exam->status == 'published')
                            <span class="text-green-600 font-bold">Yayında</span>
                        @elseif($exam->status == 'completed')
                            <span class="text-blue-600 font-bold">Tamamlandı</span>
                        @else
                            <span class="text-gray-600 font-bold">Taslak</span>
                        @endif
                    </p>
                </div>
            </div>
            @if($exam->description)
                <div class="mt-4">
                    <p class="text-sm text-gray-500 font-semibold">Açıklama</p>
                    <p class="text-gray-900">{{ $exam->description }}</p>
                </div>
            @endif
        </div>
        
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">İstatistikler</h2>
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-500 font-semibold">Katılan Öğrenci</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_students'] }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-semibold">Ortalama Puan</p>
                    <p class="text-2xl font-bold text-indigo-600">{{ number_format($stats['average_score'], 2) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-semibold">En Yüksek Puan</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($stats['highest_score'], 2) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-semibold">En Düşük Puan</p>
                    <p class="text-2xl font-bold text-red-600">{{ number_format($stats['lowest_score'], 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Sonuç Tablosu (Sıralı)</h2>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sıra</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Öğrenci</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Puan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notlar</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($exam->results->sortBy('rank') as $result)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-900">{{ $result->rank ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900">{{ optional($result->student)->first_name }} {{ optional($result->student)->last_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-indigo-600">{{ $result->score }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $result->notes ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">Henüz sonuç girilmemiş.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
