@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-onboarding.stepper :currentStep="4" :progress="$progress" />

    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
        <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-1">Adım 4: İlk Sınıf / Şube Tanımlama</h3>
        <p class="text-xs text-slate-500 dark:text-slate-400 mb-6">Öğrencilerin atanacağı ve ders programı uygulanacağı ilk sınıf grubunu oluşturun.</p>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-xl text-emerald-800 dark:text-emerald-300 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if($classrooms->count() > 0)
            <div class="mb-6 bg-slate-50 dark:bg-slate-800/40 rounded-xl p-4 border border-slate-200 dark:border-slate-800">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block mb-2">Mevcut Sınıflar ({{ $classrooms->count() }})</span>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach($classrooms as $c)
                        <div class="flex items-center justify-between text-sm bg-white dark:bg-slate-900 p-3 rounded-lg border border-slate-200 dark:border-slate-800">
                            <div>
                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $c->name }}</span>
                                <span class="text-xs text-slate-500 dark:text-slate-400 ml-2">({{ $c->code }})</span>
                            </div>
                            <span class="text-xs text-slate-500 dark:text-slate-400">Kapasite: {{ $c->capacity }} Öğrenci</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <form action="{{ route('admin.onboarding.createClassroom') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Sınıf Adı</label>
                    <input type="text" name="name" required placeholder="Örn: 12-A YKS Sözel" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Sınıf Kodu</label>
                    <input type="text" name="code" required placeholder="Örn: 12A-SOZ" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-sm uppercase focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Öğrenci Kapasitesi</label>
                    <input type="number" name="capacity" min="1" value="25" required class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('admin.onboarding.teacher') }}" class="px-5 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-medium text-sm rounded-xl transition">
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
