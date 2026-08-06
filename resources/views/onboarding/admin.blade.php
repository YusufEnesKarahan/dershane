@extends('layouts.onboarding')

@section('content')
<div>
    <div class="mb-4 flex items-center justify-between text-xs text-slate-500 font-semibold uppercase">
        <span>Adım 2 / 5</span>
        <span class="text-blue-600">Yönetici Hesabı</span>
    </div>
    
    <form method="POST" action="{{ route('onboarding.admin.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-slate-700">Yönetici Adı Soyadı</label>
            <input type="text" name="name" required class="mt-1 block w-full border-slate-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700">Yönetici E-Posta</label>
            <input type="email" name="email" required class="mt-1 block w-full border-slate-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700">Güvenli Şifre</label>
            <input type="password" name="password" required class="mt-1 block w-full border-slate-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        </div>

        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            Devam Et
        </button>
    </form>
</div>
@endsection
