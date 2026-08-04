@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.exams.index') }}" class="text-indigo-600 hover:text-indigo-800 mr-4 text-xl">&larr;</a>
        <h1 class="text-3xl font-bold text-gray-800">Yeni Sınav Oluştur</h1>
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

    <div class="bg-white shadow rounded-lg p-6">
        <form action="{{ route('admin.exams.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Sınav Adı *</label>
                    <input type="text" name="title" value="{{ old('title') }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Sınav Türü *</label>
                    <select name="exam_type_id" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                        <option value="">Seçiniz</option>
                        @foreach($examTypes as $type)
                            <option value="{{ $type->id }}" {{ old('exam_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4 md:col-span-2">
                    <label class="block text-gray-700 font-semibold mb-2">Açıklama</label>
                    <textarea name="description" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200">{{ old('description') }}</textarea>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Sınıf (Opsiyonel)</label>
                    <select name="classroom_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                        <option value="">Tüm Sınıflar</option>
                        @foreach($classrooms as $classroom)
                            <option value="{{ $classroom->id }}" {{ old('classroom_id') == $classroom->id ? 'selected' : '' }}>{{ $classroom->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Sınav Tarihi *</label>
                    <input type="date" name="exam_date" value="{{ old('exam_date') }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Süre (Dakika) *</label>
                    <input type="number" name="duration_minutes" value="{{ old('duration_minutes', 120) }}" min="1" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Toplam Puan *</label>
                    <input type="number" name="total_score" value="{{ old('total_score', 100) }}" min="1" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <a href="{{ route('admin.exams.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg mr-3">İptal</a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg shadow">Oluştur</button>
            </div>
        </form>
    </div>
</div>
@endsection
