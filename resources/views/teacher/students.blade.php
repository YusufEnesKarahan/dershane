@extends('layouts.admin')

@section('title', 'Öğrencilerim')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-slate-800 dark:text-white mb-4">Öğrencilerim</h1>
    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
        @if(isset($students) && $students->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">Öğrenci Adı</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">E-posta</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @foreach($students as $student)
                            <tr>
                                <td class="px-4 py-3 text-sm text-slate-800 dark:text-white">{{ $student->user->name ?? $student->first_name }}</td>
                                <td class="px-4 py-3 text-sm text-slate-500">{{ $student->user->email ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $students->links() }}</div>
        @else
            <p class="text-slate-500">Sınıflarınıza kayıtlı öğrenci bulunmamaktadır.</p>
        @endif
    </div>
</div>
@endsection
