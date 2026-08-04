@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Sınav Yönetimi</h1>
        <a href="{{ route('admin.exams.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg shadow">
            Yeni Sınav Oluştur
        </a>
    </div>

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {{ session('error') }}
        </div>
    @endif
    
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h3 class="text-gray-500 text-sm font-medium mb-1">Toplam Sınav</h3>
            <p class="text-3xl font-bold text-gray-800">{{ $totalExams }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h3 class="text-gray-500 text-sm font-medium mb-1">Yaklaşan Sınavlar</h3>
            <p class="text-3xl font-bold text-indigo-600">{{ $upcomingExams }}</p>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sınav Adı</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Türü</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($exams as $exam)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $exam->title }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ optional($exam->type)->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $exam->exam_date->format('d.m.Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($exam->status == 'published')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Yayında</span>
                            @elseif($exam->status == 'completed')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Tamamlandı</span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Taslak</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.exams.show', $exam) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Görüntüle</a>
                            <a href="{{ route('admin.exams.results', $exam) }}" class="text-blue-600 hover:text-blue-900 mr-3">Sonuçlar</a>
                            <a href="{{ route('admin.exams.edit', $exam) }}" class="text-green-600 hover:text-green-900">Düzenle</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Kayıtlı sınav bulunamadı.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
            {{ $exams->links() }}
        </div>
    </div>
</div>
@endsection
