@extends('layouts.admin')

@section('title', 'Öğrencilerim (Çocuklarım)')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-slate-800 dark:text-white mb-4">Öğrencilerim (Çocuklarım)</h1>
    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
        @if(isset($children) && $children->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($children as $child)
                    <div class="p-4 border rounded-lg dark:border-slate-700">
                        <h3 class="font-bold text-lg text-slate-800 dark:text-white">{{ $child->user->name ?? ($child->first_name . ' ' . $child->last_name) }}</h3>
                        <p class="text-sm text-slate-500 mt-1">Öğrenci No: {{ $child->student_number ?? '-' }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-slate-500">Kayıtlı öğrenciniz bulunmamaktadır.</p>
        @endif
    </div>
</div>
@endsection
