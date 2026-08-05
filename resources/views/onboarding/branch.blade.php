@extends('layouts.onboarding')

@section('content')
<div>
    <div class="mb-4 flex items-center justify-between text-xs text-gray-500 font-semibold uppercase">
        <span>Adım 3 / 5</span>
        <span class="text-indigo-600">İlk Şube Tanımı</span>
    </div>
    
    <form method="POST" action="{{ route('onboarding.branch.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700">Şube Adı</label>
            <input type="text" name="branch_name" required placeholder="Örn: Merkez Şubesi" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Şube Adresi</label>
            <textarea name="address" required rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
        </div>

        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Devam Et
        </button>
    </form>
</div>
@endsection
