@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Kurum Paket Bilgisi</h1>
        <p class="text-sm text-slate-500 mt-1">Aktif kurumunuza tanımlı olan paket lisans durumunu ve aktif özellikleri inceleyin.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-sm flex items-center justify-between">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
        <!-- Active Package Info Card -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-6 pb-6 border-b border-slate-100">
                <div>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1">Mevcut Lisanslı Paket</span>
                    <h2 class="text-2xl font-bold text-slate-900">{{ $activePackage ? $activePackage->name : 'V3 — Enterprise (Varsayılan)' }}</h2>
                </div>
                <span class="px-4 py-1.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">
                    Aktif Lisans
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
                <div class="bg-slate-50 rounded-xl p-4">
                    <span class="text-xs text-slate-500 block mb-1">Lisans Tipi</span>
                    <span class="text-base font-semibold text-slate-800">
                        {{ $activeBranchPackage ? ($activeBranchPackage->license_type === 'three_year' ? '3 Yıllık Kurumsal' : 'Yıllık Standart') : 'Sınırsız / Demo' }}
                    </span>
                </div>

                <div class="bg-slate-50 rounded-xl p-4">
                    <span class="text-xs text-slate-500 block mb-1">Başlangıç Tarihi</span>
                    <span class="text-base font-semibold text-slate-800">
                        {{ $activeBranchPackage && $activeBranchPackage->start_date ? $activeBranchPackage->start_date->format('d.m.Y') : 'Süresiz' }}
                    </span>
                </div>

                <div class="bg-slate-50 rounded-xl p-4">
                    <span class="text-xs text-slate-500 block mb-1">Bitiş Tarihi</span>
                    <span class="text-base font-semibold text-slate-800">
                        {{ $activeBranchPackage && $activeBranchPackage->end_date ? $activeBranchPackage->end_date->format('d.m.Y') : 'Süresiz' }}
                    </span>
                </div>
            </div>

            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4">Aktif Kullanılabilir Modüller</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @if($activePackage && $activePackage->features->count() > 0)
                    @foreach($activePackage->features as $feat)
                        <div class="flex items-center p-3 bg-emerald-50/50 border border-emerald-100 rounded-xl text-emerald-900 text-sm">
                            <svg class="w-4 h-4 text-emerald-600 mr-2.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span class="font-medium">{{ $feat->name }}</span>
                        </div>
                    @endforeach
                @else
                    <p class="text-sm text-slate-500">Tüm modüller aktif durumdadır.</p>
                @endif
            </div>
        </div>

        <!-- Package Upgrade / Change Card -->
        @if(auth()->user()->hasRole('Super Admin'))
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between">
            <div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Paket Değiştir / Yükselt</h3>
                <p class="text-xs text-slate-500 mb-6">Şubenin kullandığı paket seviyesini ve lisans süresini güncelleyin.</p>

                <form action="{{ route('admin.branch-package.assign') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="branch_id" value="{{ $branch ? $branch->id : auth()->user()->branch_id }}">

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Yeni Paket Seçin</label>
                        <select name="package_id" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500">
                            @foreach($allPackages as $pkgOption)
                                <option value="{{ $pkgOption->id }}" {{ $activePackage && $activePackage->id === $pkgOption->id ? 'selected' : '' }}>
                                    {{ $pkgOption->name }} ({{ $pkgOption->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Lisans Süresi</label>
                        <select name="license_type" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500">
                            <option value="yearly">Yıllık Lisans (1 Yıl)</option>
                            <option value="three_year">3 Yıllık Avantajlı Lisans</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm mt-4">
                        Paketi Güncelle
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
