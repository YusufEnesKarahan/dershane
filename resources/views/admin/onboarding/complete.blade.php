@extends('layouts.admin')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4 py-12">
    <div class="max-w-xl w-full text-center bg-white p-8 sm:p-12 rounded-3xl shadow-xl border border-slate-100">
        <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <span class="px-4 py-1.5 rounded-full text-xs font-extrabold bg-emerald-50 text-emerald-700 tracking-wider uppercase mb-3 inline-block">
            Kurulum Tamamlandı
        </span>

        <h1 class="text-3xl font-extrabold text-slate-900 mb-3">Tebrikler! Kurumunuz Hazır.</h1>
        <p class="text-slate-600 mb-8 text-sm leading-relaxed">
            Kurum profiliniz, akademik yılınız, lisans paketiniz, ilk öğretmeniniz ve sınıfınız başarıyla sistemde aktifleştirildi. Artık dershanenizi yönetmeye başlayabilirsiniz.
        </p>

        <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-4 mb-8 text-left space-y-2 text-xs">
            <div class="flex items-center justify-between text-slate-700 font-medium">
                <span>✓ Kurum Bilgileri</span>
                <span class="text-emerald-600 font-bold">Tamamlandı</span>
            </div>
            <div class="flex items-center justify-between text-slate-700 font-medium">
                <span>✓ Akademik Yıl & Dönem</span>
                <span class="text-emerald-600 font-bold">Tamamlandı</span>
            </div>
            <div class="flex items-center justify-between text-slate-700 font-medium">
                <span>✓ Lisans Paketi Tanımlaması</span>
                <span class="text-emerald-600 font-bold">Tamamlandı</span>
            </div>
            <div class="flex items-center justify-between text-slate-700 font-medium">
                <span>✓ İlk Öğretmen Kaydı</span>
                <span class="text-emerald-600 font-bold">Tamamlandı</span>
            </div>
            <div class="flex items-center justify-between text-slate-700 font-medium">
                <span>✓ İlk Sınıf Oluşturma</span>
                <span class="text-emerald-600 font-bold">Tamamlandı</span>
            </div>
        </div>

        <a href="{{ route('admin.reporting.dashboard') }}" 
           class="inline-flex items-center justify-center px-8 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-base rounded-2xl transition-all shadow-lg shadow-indigo-200 hover:shadow-indigo-300 transform hover:-translate-y-0.5">
            Yönetim Paneline Git →
        </a>
    </div>
</div>
@endsection
