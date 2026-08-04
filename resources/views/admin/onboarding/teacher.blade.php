@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-onboarding.stepper :currentStep="4" :progress="$progress" />

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h3 class="text-lg font-bold text-slate-900 mb-1">Adım 4: İlk Öğretmen Kaydı</h3>
        <p class="text-xs text-slate-500 mb-6">Sistemde ders ve yoklama takibi yapılabilmesi için ilk kadro öğretmeninizi ekleyin.</p>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if($teachers->count() > 0)
            <div class="mb-6 bg-slate-50 rounded-xl p-4 border border-slate-200">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-2">Eklenen Öğretmenler ({{ $teachers->count() }})</span>
                <div class="space-y-2">
                    @foreach($teachers as $t)
                        <div class="flex items-center justify-between text-sm bg-white p-3 rounded-lg border border-slate-200/80">
                            <div>
                                <span class="font-bold text-slate-800">{{ $t->user->name ?? 'Öğretmen' }}</span>
                                <span class="text-xs text-slate-500 ml-2">({{ $t->branch_subject }})</span>
                            </div>
                            <span class="text-xs text-slate-500">{{ $t->user->email ?? '' }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <form action="{{ route('admin.onboarding.createTeacher') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Ad</label>
                    <input type="text" name="first_name" required placeholder="Ahmet" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Soyad</label>
                    <input type="text" name="last_name" required placeholder="Yılmaz" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">E-posta Adresi</label>
                    <input type="email" name="email" required placeholder="ahmet.yilmaz@dershane.com" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Telefon Numarası</label>
                    <input type="text" name="phone" placeholder="0532 000 00 00" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Branş / Ders</label>
                    <input type="text" name="branch_subject" placeholder="Örn: Matematik, Fizik, Türkçe" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <a href="{{ route('admin.onboarding.package') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-sm rounded-xl">
                    ← Geri
                </a>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl transition-all shadow-sm">
                    Öğretmeni Kaydet ve Devam Et →
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
