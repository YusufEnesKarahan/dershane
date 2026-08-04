@extends('layouts.admin')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-onboarding.stepper :currentStep="3" :progress="$progress" />

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-8">
        <h3 class="text-lg font-bold text-slate-900 mb-1">Adım 3: Lisans Paketi Seçimi</h3>
        <p class="text-xs text-slate-500 mb-6">Kurumunuzda aktif hale getirmek istediğiniz satış paketini seçin.</p>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.onboarding.selectPackage') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                @foreach($packages as $pkg)
                    @php $selected = $activePackage && $activePackage->id === $pkg->id; @endphp
                    <label class="relative bg-white rounded-2xl border-2 {{ $selected ? 'border-indigo-600 ring-2 ring-indigo-500/20' : 'border-slate-200 hover:border-slate-300' }} p-6 cursor-pointer flex flex-col justify-between transition-all">
                        <input type="radio" name="package_id" value="{{ $pkg->id }}" {{ $selected || $loop->first ? 'checked' : '' }} class="sr-only">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700">
                                    {{ $pkg->code }}
                                </span>
                            </div>

                            <h4 class="text-lg font-bold text-slate-900 mb-1">{{ $pkg->name }}</h4>
                            <p class="text-xs text-slate-500 mb-4">{{ $pkg->description }}</p>

                            <div class="bg-slate-50 rounded-xl p-3 mb-4">
                                <span class="text-xs text-slate-500 block">Yıllık Lisans:</span>
                                <span class="text-base font-bold text-slate-900">₺{{ number_format($pkg->price_yearly, 2) }}</span>
                            </div>

                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-2">Özellikler ({{ $pkg->features->count() }}):</span>
                            <ul class="space-y-1 text-xs text-slate-600">
                                @foreach($pkg->features as $feat)
                                    <li class="flex items-center">
                                        <svg class="w-3.5 h-3.5 text-emerald-500 mr-1.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        {{ $feat->name }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </label>
                @endforeach
            </div>

            <div class="bg-slate-50 rounded-xl p-4 mb-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-800">Lisans Periyodu</label>
                    <span class="text-xs text-slate-500">3 yıllık lisanslarda avantajlı fiyat uygulanır.</span>
                </div>
                <select name="license_type" class="rounded-xl border-slate-200 text-sm focus:ring-indigo-500 font-medium">
                    <option value="yearly">Yıllık Lisans (1 Yıl)</option>
                    <option value="three_year">3 Yıllık Avantajlı Lisans</option>
                </select>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <a href="{{ route('admin.onboarding.academic-year') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-sm rounded-xl">
                    ← Geri
                </a>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl transition-all shadow-sm">
                    Paketi Onayla ve Devam Et →
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
