@extends('layouts.templates.landing')

@section('template-content')
    <x-section bg="gray" py="24" class="min-h-screen flex items-center justify-center">
        <div class="w-full max-w-md bg-white p-8 sm:p-12 rounded-premium-2xl border border-neutral-100 shadow-premium-sm">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-display font-bold text-neutral">Şifremi Unuttum</h1>
                <p class="text-neutral-500 mt-2 text-sm">E-posta adresinizi girin, size şifre sıfırlama bağlantısı gönderelim.</p>
            </div>

            @if (session('status'))
                <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-600 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                @csrf
                
                <div>
                    <label for="email" class="block text-sm font-medium text-neutral mb-2">E-posta Adresi</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-4 py-3 rounded-xl border {{ $errors->has('email') ? 'border-red-500 focus:ring-red-500/20' : 'border-neutral-200 focus:border-primary focus:ring-primary/20' }} focus:ring-2 outline-none transition-all">
                    @error('email')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-premium-sm text-sm font-medium text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
                    Sıfırlama Bağlantısı Gönder
                </button>
                
                <div class="text-center mt-4">
                    <a href="{{ route('login') }}" class="text-sm text-neutral-500 hover:text-primary transition-colors">Giriş Ekranına Dön</a>
                </div>
            </form>
        </div>
    </x-section>
@endsection
