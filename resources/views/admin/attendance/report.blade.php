@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-slate">Genel Yoklama Raporu</h1>
    </div>

    <!-- Filtreler -->
    <div class="bg-white p-4 rounded-lg shadow-sm mb-6">
        <form action="{{ route('admin.attendance.report') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs text-slate-500 mb-1">Başlangıç Tarihi</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full text-sm border-slate-300 rounded-md">
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">Bitiş Tarihi</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full text-sm border-slate-300 rounded-md">
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">Sınıf</label>
                <select name="classroom_id" class="w-full text-sm border-slate-300 rounded-md">
                    <option value="">Tümü</option>
                    @foreach(\App\Models\Classroom::all() as $class)
                        <option value="{{ $class->id }}" {{ request('classroom_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-primary text-white py-2 px-4 rounded-md text-sm hover:bg-primary/90">Filtrele</button>
            </div>
        </form>
    </div>

    <!-- Özet -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-green-500">
            <h3 class="text-sm font-medium text-slate-500">Genel Katılım Oranı</h3>
            <p class="text-3xl font-bold text-green-600">
                {{ $reportData['total_records'] > 0 ? round(($reportData['present_count'] / $reportData['total_records']) * 100) : 0 }}%
            </p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-yellow-500">
            <h3 class="text-sm font-medium text-slate-500">Geç Kalma Oranı</h3>
            <p class="text-3xl font-bold text-yellow-600">
                {{ $reportData['total_records'] > 0 ? round(($reportData['late_count'] / $reportData['total_records']) * 100) : 0 }}%
            </p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-red-500">
            <h3 class="text-sm font-medium text-slate-500">Devamsızlık Oranı</h3>
            <p class="text-3xl font-bold text-red-600">
                {{ $reportData['total_records'] > 0 ? round(($reportData['absent_count'] / $reportData['total_records']) * 100) : 0 }}%
            </p>
        </div>
    </div>
</div>
@endsection
