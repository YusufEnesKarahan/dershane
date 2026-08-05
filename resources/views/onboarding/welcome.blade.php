@extends('layouts.onboarding')

@section('content')
<div class="text-center">
    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 text-indigo-600 mb-4">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
        </svg>
    </div>
    
    <h3 class="text-lg font-medium text-gray-900">Kuruluma Başlayın</h3>
    <p class="mt-2 text-sm text-gray-500">
        Dershanenizi dijitale taşımak ve öğrenci/öğretmen yönetimini kolaylaştırmak için sihirbaz adımlarını takip edin.
    </p>

    <div class="mt-6">
        <a href="{{ route('onboarding.company') }}" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Hadi Başlayalım
        </a>
    </div>
</div>
@endsection
