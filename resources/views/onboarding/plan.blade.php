@extends('layouts.onboarding')

@section('content')
<div>
    <div class="mb-4 flex items-center justify-between text-xs text-gray-500 font-semibold uppercase">
        <span>Adım 4 / 5</span>
        <span class="text-indigo-600">Paket Seçimi</span>
    </div>
    
    <form method="POST" action="{{ route('onboarding.plan.store') }}" class="space-y-4">
        @csrf
        <div class="space-y-3">
            <label class="flex items-center p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                <input type="radio" name="plan" value="starter" checked class="h-4 w-4 text-indigo-600 focus:ring-indigo-500">
                <span class="ml-3">
                    <span class="block text-sm font-bold text-gray-900">Starter</span>
                    <span class="block text-xs text-gray-500">200 Öğrenci, 10 Öğretmen, 1 Şube</span>
                </span>
            </label>

            <label class="flex items-center p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                <input type="radio" name="plan" value="professional" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500">
                <span class="ml-3">
                    <span class="block text-sm font-bold text-gray-900">Professional</span>
                    <span class="block text-xs text-gray-500">1000 Öğrenci, 50 Öğretmen, 5 Şube</span>
                </span>
            </label>

            <label class="flex items-center p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                <input type="radio" name="plan" value="enterprise" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500">
                <span class="ml-3">
                    <span class="block text-sm font-bold text-gray-900">Enterprise</span>
                    <span class="block text-xs text-gray-500">Sınırsız Öğrenci, Öğretmen ve Şube</span>
                </span>
            </label>
        </div>

        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mt-4">
            Devam Et
        </button>
    </form>
</div>
@endsection
