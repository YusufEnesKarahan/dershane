@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-slate">Yoklama Al: {{ $session->classroom->name }}</h1>
        <a href="{{ route('teacher.attendance.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-900">Geri Dön</a>
    </div>

    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <form action="{{ route('teacher.attendance.update', $session) }}" method="POST">
            @csrf
            @method('PUT')
            
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Öğrenci</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase tracking-wider">Var</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase tracking-wider">Yok</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase tracking-wider">Geç</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase tracking-wider">İzinli</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Not</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @foreach($session->classroom->students as $index => $student)
                    @php
                        $existingRecord = $session->records->where('student_id', $student->id)->first();
                        $status = $existingRecord ? $existingRecord->status : 'present';
                        $note = $existingRecord ? $existingRecord->note : '';
                    @endphp
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                            {{ $student->first_name }} {{ $student->last_name }}
                            <input type="hidden" name="records[{{ $index }}][student_id]" value="{{ $student->id }}">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <input type="radio" name="records[{{ $index }}][status]" value="present" {{ $status == 'present' ? 'checked' : '' }} class="h-4 w-4 text-green-600 focus:ring-green-500 border-slate-300">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <input type="radio" name="records[{{ $index }}][status]" value="absent" {{ $status == 'absent' ? 'checked' : '' }} class="h-4 w-4 text-red-600 focus:ring-red-500 border-slate-300">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <input type="radio" name="records[{{ $index }}][status]" value="late" {{ $status == 'late' ? 'checked' : '' }} class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-slate-300">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <input type="radio" name="records[{{ $index }}][status]" value="excused" {{ $status == 'excused' ? 'checked' : '' }} class="h-4 w-4 text-slate-600 focus:ring-slate-500 border-slate-300">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="text" name="records[{{ $index }}][note]" value="{{ $note }}" class="block w-full border-slate-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm" placeholder="İsteğe bağlı">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="px-6 py-4 bg-slate-50 flex justify-end">
                <button type="submit" class="px-4 py-2 bg-primary text-white text-sm font-medium rounded-md hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    Yoklamayı Kaydet
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
