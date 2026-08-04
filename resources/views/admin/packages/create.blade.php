@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <a href="{{ route('admin.packages.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-700">← Paket Listesine Dön</a>
        <h1 class="text-2xl font-bold text-slate-900 mt-2">Yeni Paket Oluştur</h1>
    </div>

    <form action="{{ route('admin.packages.store') }}" method="POST" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Paket Adı</label>
                <input type="text" name="name" required placeholder="Örn: Paket V4 — Pro Plus" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Paket Kodu</label>
                <input type="text" name="code" required placeholder="Örn: V4" class="w-full rounded-xl border-slate-200 text-sm uppercase focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Açıklama</label>
            <textarea name="description" rows="2" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Yıllık Fiyat (₺)</label>
                <input type="number" step="0.01" name="price_yearly" required value="0" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">3 Yıllık Fiyat (₺)</label>
                <input type="number" step="0.01" name="price_3_year" required value="0" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Durum</label>
                <select name="status" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="active">Aktif</option>
                    <option value="inactive">Pasif</option>
                </select>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100">
            <h3 class="text-sm font-bold text-slate-900 mb-4">Özellik Seçimi</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($features as $feat)
                    <label class="flex items-start space-x-3 p-3 bg-slate-50 rounded-xl border border-slate-200/60 cursor-pointer hover:bg-slate-100/60">
                        <input type="checkbox" name="features[]" value="{{ $feat->id }}" class="mt-1 rounded text-indigo-600 focus:ring-indigo-500">
                        <div>
                            <span class="block text-sm font-semibold text-slate-800">{{ $feat->name }} (<code>{{ $feat->code }}</code>)</span>
                            <span class="block text-xs text-slate-500">{{ $feat->description }}</span>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end space-x-4 pt-4 border-t border-slate-100">
            <a href="{{ route('admin.packages.index') }}" class="px-5 py-2.5 bg-slate-100 text-slate-700 text-sm font-medium rounded-xl hover:bg-slate-200">İptal</a>
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 shadow-sm">Paketi Kaydet</button>
        </div>
    </form>
</div>
@endsection
