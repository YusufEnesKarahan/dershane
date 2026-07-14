@extends('layouts.templates.landing')

@section('template-content')
    <x-section bg="gray" py="24" class="min-h-screen flex items-center justify-center">
        <div class="text-center">
            <h1 class="text-4xl font-display font-bold text-neutral mb-4">Yönetim Paneli</h1>
            <p class="text-neutral-500 mb-8">Gelecek sprintlerde bu alana Admin Dashboard inşa edilecektir.</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="inline-flex justify-center py-2 px-6 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 transition-colors">
                    Çıkış Yap
                </button>
            </form>
        </div>
    </x-section>
@endsection
