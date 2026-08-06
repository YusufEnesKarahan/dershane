@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-onboarding.stepper :currentStep="3" :progress="$progress" />

    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
        <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-1">Adım 3: İlk Öğretmen Kaydı</h3>
        <p class="text-xs text-slate-500 dark:text-slate-400 mb-6">Sistemde ders ve yoklama takibi yapılabilmesi için ilk kadro öğretmeninizi ekleyin.</p>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-xl text-emerald-800 dark:text-emerald-300 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if($teachers->count() > 0)
            <div class="mb-6 bg-slate-50 dark:bg-slate-800/40 rounded-xl p-4 border border-slate-200 dark:border-slate-800">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block mb-2">Eklenen Öğretmenler ({{ $teachers->count() }})</span>
                <div class="space-y-2">
                    @foreach($teachers as $t)
                        <div class="flex items-center justify-between text-sm bg-white dark:bg-slate-900 p-3 rounded-lg border border-slate-200 dark:border-slate-800">
                            <div>
                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $t->user->name ?? 'Öğretmen' }}</span>
                                <span class="text-xs text-slate-500 dark:text-slate-400 ml-2">({{ $t->specialties }})</span>
                            </div>
                            <span class="text-xs text-slate-500 dark:text-slate-400">{{ $t->user->email ?? '' }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <form action="{{ route('admin.onboarding.createTeacher') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Ad</label>
                    <input type="text" name="first_name" required placeholder="Ahmet" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Soyad</label>
                    <input type="text" name="last_name" required placeholder="Yılmaz" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">E-posta Adresi</label>
                    <input type="email" name="email" required placeholder="ahmet.yilmaz@dershane.com" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Telefon Numarası</label>
                    <input type="text" name="phone" placeholder="0532 000 00 00" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Branş / Ders</label>
                    <input type="text" name="branch_subject" placeholder="Örn: Matematik, Fizik, Türkçe" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('admin.onboarding.academic-year') }}" class="px-5 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-medium text-sm rounded-xl transition">
                    ← Geri
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition-all shadow-sm">
                    Öğretmeni Kaydet ve Devam Et →
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
