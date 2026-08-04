@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.exams.show', $exam) }}" class="text-indigo-600 hover:text-indigo-800 mr-4 text-xl">&larr;</a>
        <h1 class="text-3xl font-bold text-gray-800">{{ $exam->title }} - Sonuç Girişi</h1>
    </div>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <form action="{{ route('admin.exams.results.store', $exam) }}" method="POST">
            @csrf
            
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Öğrenci No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ad Soyad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Puan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notlar (Opsiyonel)</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($students as $index => $student)
                        @php
                            $existing = $existingResults->get($student->id);
                        @endphp
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900">{{ $student->student_number ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $student->first_name }} {{ $student->last_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="hidden" name="results[{{ $index }}][student_id]" value="{{ $student->id }}">
                                <input type="number" step="0.01" name="results[{{ $index }}][score]" value="{{ old('results.'.$index.'.score', $existing ? $existing->score : 0) }}" required class="w-24 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="text" name="results[{{ $index }}][notes]" value="{{ old('results.'.$index.'.notes', $existing ? $existing->notes : '') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">Bu sınav/sınıf için kayıtlı öğrenci bulunamadı.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg shadow">Sonuçları Kaydet ve Sırala</button>
            </div>
        </form>
    </div>
</div>
@endsection
