@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-onboarding.stepper :currentStep="5" :progress="$progress" />

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h3 class="text-lg font-bold text-slate-900 mb-1">Adım 5: İlk Sınıf / Şube Tanımlama</h3>
        <p class="text-xs text-slate-500 mb-6">Öğrencilerin atanacağı ve ders programı uygulanacağı ilk sınıf grubunu oluşturun.</p>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if($classrooms->count() > 0)
            <div class="mb-6 bg-slate-50 rounded-xl p-4 border border-slate-200">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-2">Mevcut Sınıflar ({{ $classrooms->count() }})</span>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach($classrooms as $c)
                        <div class="flex items-center justify-between text-sm bg-white p-3 rounded-lg border border-slate-200/80">
                            <div>
                                <span class="font-bold text-slate-800">{{ $c->name }}</span>
                                <span class="text-xs text-slate-500 ml-2">({{ $c->code }})</span>
                            </div>
                            <span class="text-xs text-slate-500">Kapasite: {{ $c->capacity }} Öğrenci</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <form action="{{ route('admin.onboarding.createClassroom') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Sınıf Adı</label>
                    <input type="text" name="name" required placeholder="Örn: 12-A YKS Sözel" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Sınıf Kodu</label>
                    <input type="text" name="code" required placeholder="Örn: 12A-SOZ" class="w-full rounded-xl border-slate-200 text-sm uppercase focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Öğrenci Kapasitesi</label>
                    <input type="number" name="capacity" min="1" value="25" required class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <a href="{{ route('admin.onboarding.teacher') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-sm rounded-xl">
                    ← Geri
                </a>
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-xl transition-all shadow-sm">
                    Sınıfı Oluştur ve Kurulumu Tamamla ✓
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
