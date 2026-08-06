@extends('layouts.onboarding')

@section('content')
<div class="text-center">
    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 text-green-600 mb-4">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
    </div>
    
    <h3 class="text-lg font-medium text-slate-900">Kurulum Tamamlanıyor!</h3>
    <p class="mt-2 text-sm text-slate-500">
        Dershane bilgileriniz ve şubeniz başarıyla yapılandırıldı.
    </p>

    <form method="POST" action="{{ route('onboarding.complete') }}" class="mt-6 space-y-4">
        @csrf
        
        <div class="bg-slate-50 p-4 rounded-lg text-left">
            <label class="flex items-start cursor-pointer">
                <input type="checkbox" name="seed_demo" value="1" checked class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-slate-300 rounded mt-0.5">
                <span class="ml-3 text-sm text-slate-700">
                    <span class="font-semibold block">Demo Veri Yükle (Önerilen)</span>
                    <span class="text-xs text-slate-500 block">Sistemi hemen deneyebilmeniz için 10 öğrenci, 3 öğretmen, 3 sınıf ve 5 kurs örnek olarak yüklenir.</span>
                </span>
            </label>
        </div>

        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            Dershaneyi Oluştur ve Giriş Yap
        </button>
    </form>
</div>
@endsection
