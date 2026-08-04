@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Paket & Özellik Yönetimi</h1>
            <p class="text-sm text-slate-500 mt-1">V1, V2 ve V3 paketlerini düzenleyin, yeni özellikler atayın.</p>
        </div>
        <a href="{{ route('admin.packages.create') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-all shadow-sm shadow-indigo-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Yeni Paket Ekle
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-sm flex items-center justify-between">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        @foreach($packages as $pkg)
            <div class="bg-white rounded-2xl border {{ $pkg->code === 'V3' ? 'border-indigo-500 ring-2 ring-indigo-500/20' : 'border-slate-200' }} shadow-sm flex flex-col justify-between overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $pkg->code === 'V3' ? 'bg-indigo-100 text-indigo-700' : ($pkg->code === 'V2' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-700') }}">
                            {{ $pkg->code }}
                        </span>
                        <span class="text-xs font-medium {{ $pkg->status === 'active' ? 'text-emerald-600' : 'text-slate-400' }}">
                            ● {{ ucfirst($pkg->status) }}
                        </span>
                    </div>

                    <h3 class="text-xl font-bold text-slate-900 mb-2">{{ $pkg->name }}</h3>
                    <p class="text-sm text-slate-500 mb-6 min-h-[40px]">{{ $pkg->description }}</p>

                    <div class="bg-slate-50 rounded-xl p-4 mb-6">
                        <div class="flex items-baseline justify-between mb-1">
                            <span class="text-xs text-slate-500">Yıllık Fiyat:</span>
                            <span class="text-lg font-bold text-slate-900">₺{{ number_format($pkg->price_yearly, 2) }}</span>
                        </div>
                        <div class="flex items-baseline justify-between">
                            <span class="text-xs text-slate-500">3 Yıllık Fiyat:</span>
                            <span class="text-sm font-semibold text-slate-700">₺{{ number_format($pkg->price_3_year, 2) }}</span>
                        </div>
                    </div>

                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Dahil Özellikler ({{ $pkg->features->count() }})</h4>
                    <ul class="space-y-2 text-sm">
                        @foreach($features as $feat)
                            @php $has = $pkg->features->contains('id', $feat->id); @endphp
                            <li class="flex items-center {{ $has ? 'text-slate-700 font-medium' : 'text-slate-300 line-through' }}">
                                @if($has)
                                    <svg class="w-4 h-4 text-emerald-500 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                @else
                                    <svg class="w-4 h-4 text-slate-300 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                @endif
                                {{ $feat->name }}
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                    <a href="{{ route('admin.packages.edit', $pkg) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                        Düzenle & Özellikler →
                    </a>
                    <form action="{{ route('admin.packages.toggle-status', $pkg) }}" method="POST">
                        @csrf
                        <button type="submit" class="text-xs font-medium text-slate-500 hover:text-slate-700">
                            {{ $pkg->status === 'active' ? 'Pasife Al' : 'Aktif Et' }}
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
