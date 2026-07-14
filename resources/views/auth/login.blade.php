@extends('layouts.templates.landing')

@section('template-content')
    <x-section bg="gray" py="24" class="min-h-screen flex items-center justify-center">
        <div class="w-full max-w-md bg-white p-8 sm:p-12 rounded-premium-2xl border border-neutral-100 shadow-premium-sm">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-display font-bold text-neutral">Hoş Geldiniz</h1>
                <p class="text-neutral-500 mt-2">Hesabınıza giriş yapın</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-6" aria-label="Giriş Formu">
                @csrf
                
                <div>
                    <label for="email" class="block text-sm font-medium text-neutral mb-2">E-posta Adresi</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-4 py-3 rounded-xl border {{ $errors->has('email') ? 'border-red-500 focus:ring-red-500/20' : 'border-neutral-200 focus:border-primary focus:ring-primary/20' }} focus:ring-2 outline-none transition-all">
                    @error('email')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-sm font-medium text-neutral">Şifre</label>
                        <a href="{{ route('password.request') }}" class="text-sm text-primary hover:underline">Şifremi Unuttum</a>
                    </div>
                    <input type="password" id="password" name="password" required
                        class="w-full px-4 py-3 rounded-xl border border-neutral-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                </div>

                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-primary focus:ring-primary border-neutral-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-neutral-600">Beni Hatırla</label>
                </div>

                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-premium-sm text-sm font-medium text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
                    Giriş Yap
                </button>
            </form>
        </div>
    </x-section>
@endsection
