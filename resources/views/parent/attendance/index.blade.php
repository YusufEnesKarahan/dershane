@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <h1 class="text-2xl font-bold text-neutral">Öğrenci Yoklama Durumu</h1>
        
        @if($students && $students->count() > 1)
        <div class="mt-4 md:mt-0">
            <form action="{{ route('parent.attendance.index') }}" method="GET" class="flex items-center">
                <label for="student_id" class="mr-2 text-sm text-neutral-600">Öğrenci Seçin:</label>
                <select name="student_id" id="student_id" onchange="this.form.submit()" class="border-neutral-300 rounded-md shadow-sm focus:ring-primary focus:border-primary text-sm">
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ $selectedStudent->id == $student->id ? 'selected' : '' }}>
                            {{ $student->first_name }} {{ $student->last_name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
        @endif
    </div>

    @if($selectedStudent)
    <!-- Özet Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-primary">
            <h3 class="text-sm font-medium text-neutral-500">Toplam Ders</h3>
            <p class="text-2xl font-bold text-neutral">{{ $summary['total'] ?? 0 }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-green-500">
            <h3 class="text-sm font-medium text-neutral-500">Katılım (Var)</h3>
            <p class="text-2xl font-bold text-green-600">{{ $summary['present'] ?? 0 }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-red-500">
            <h3 class="text-sm font-medium text-neutral-500">Devamsızlık (Yok)</h3>
            <p class="text-2xl font-bold text-red-600">{{ $summary['absent'] ?? 0 }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-yellow-500">
            <h3 class="text-sm font-medium text-neutral-500">Geç Kalma</h3>
            <p class="text-2xl font-bold text-yellow-600">{{ $summary['late'] ?? 0 }}</p>
        </div>
    </div>

    <!-- Yoklama Kayıtları Listesi -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-neutral-200">
            <h3 class="text-lg font-medium text-neutral-900">Geçmiş Yoklamalar ({{ $selectedStudent->first_name }})</h3>
        </div>
        <table class="min-w-full divide-y divide-neutral-200">
            <thead class="bg-neutral-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Tarih</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Ders / Sınıf</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Durum</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-neutral-200">
                @forelse($records as $record)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-900">{{ $record->session->session_date->format('d.m.Y') ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">{{ $record->session->classroom->name ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($record->status == 'present')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Var</span>
                        @elseif($record->status == 'absent')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Yok</span>
                        @elseif($record->status == 'late')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Geç</span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">İzinli</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-6 py-4 text-center text-sm text-neutral-500">Henüz yoklama kaydı bulunmamaktadır.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="px-6 py-4 border-t border-neutral-200">
            {{ $records->links() }}
        </div>
    </div>
    @endif
</div>
@endsection
