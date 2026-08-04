@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-neutral">Yeni Yoklama Oturumu</h1>
    </div>

    <div class="bg-white shadow-sm rounded-lg p-6">
        <form action="{{ route('admin.attendance.store') }}" method="POST">
            @csrf

            <div class="space-y-6">
                <!-- Sınıf Seçimi -->
                <div>
                    <label for="classroom_id" class="block text-sm font-medium text-neutral-700">Sınıf</label>
                    <select id="classroom_id" name="classroom_id" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-neutral-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md">
                        <option value="">Seçiniz...</option>
                        @foreach(\App\Models\Classroom::all() as $classroom)
                            <option value="{{ $classroom->id }}">{{ $classroom->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Öğretmen Seçimi -->
                <div>
                    <label for="teacher_id" class="block text-sm font-medium text-neutral-700">Öğretmen</label>
                    <select id="teacher_id" name="teacher_id" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-neutral-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md">
                        <option value="">Seçiniz...</option>
                        @foreach(\App\Models\Teacher::all() as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->first_name }} {{ $teacher->last_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Tarih -->
                <div>
                    <label for="session_date" class="block text-sm font-medium text-neutral-700">Tarih</label>
                    <input type="date" name="session_date" id="session_date" value="{{ date('Y-m-d') }}" required class="mt-1 block w-full border-neutral-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm">
                </div>

                <!-- Saat -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-neutral-700">Başlangıç Saati</label>
                        <input type="time" name="start_time" id="start_time" class="mt-1 block w-full border-neutral-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm">
                    </div>
                    <div>
                        <label for="end_time" class="block text-sm font-medium text-neutral-700">Bitiş Saati</label>
                        <input type="time" name="end_time" id="end_time" class="mt-1 block w-full border-neutral-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm">
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('admin.attendance.index') }}" class="bg-white py-2 px-4 border border-neutral-300 rounded-md shadow-sm text-sm font-medium text-neutral-700 hover:bg-neutral-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    İptal
                </a>
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    Oluştur
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
