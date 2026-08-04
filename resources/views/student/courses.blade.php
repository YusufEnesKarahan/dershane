@extends('layouts.admin')

@section('title', 'Derslerim')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-slate-800 dark:text-white mb-4">Derslerim</h1>
    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
        @if(isset($courses) && $courses->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($courses as $course)
                    <div class="p-4 border rounded-lg dark:border-slate-700">
                        <h3 class="font-bold text-lg text-slate-800 dark:text-white">{{ $course->name }}</h3>
                        <p class="text-sm text-slate-500 mt-1">{{ $course->code ?? '' }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-slate-500">Henüz kayıtlı dersiniz bulunmamaktadır.</p>
        @endif
    </div>
</div>
@endsection
