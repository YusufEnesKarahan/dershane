@extends('layouts.admin')
@section('title', 'Öğretmen Paneli')
@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-emerald-900 to-slate-900 p-8 rounded-3xl text-white shadow-premium flex flex-col md:flex-row md:items-center md:justify-between gap-6 border border-emerald-950">
        <div>
            <span class="px-2.5 py-1 text-[10px] uppercase font-bold tracking-widest bg-emerald-500/20 text-emerald-300 rounded-full border border-emerald-500/30">Öğretmen Paneli</span>
            <h1 class="text-2xl font-black mt-2">Derslerim ve Öğrencilerim</h1>
            <p class="text-xs text-slate-300 mt-1">Sınıflarınızı yönetebilir, yoklama alabilirsiniz.</p>
        </div>
    </div>
</div>
@endsection
