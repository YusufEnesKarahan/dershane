<?php

function createView($path, $content) {
    $dir = dirname(__DIR__ . '/resources/views/' . $path);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    file_put_contents(__DIR__ . '/resources/views/' . $path, $content);
}

$views = [
    'auth/login.blade.php' => "@extends('layouts.templates.landing')

@section('template-content')
    <x-section bg=\"gray\" py=\"24\" class=\"min-h-screen flex items-center justify-center\">
        <div class=\"w-full max-w-md bg-white p-8 sm:p-12 rounded-premium-2xl border border-neutral-100 shadow-premium-sm\">
            <div class=\"text-center mb-8\">
                <h1 class=\"text-3xl font-display font-bold text-neutral\">Hoş Geldiniz</h1>
                <p class=\"text-neutral-500 mt-2\">Hesabınıza giriş yapın</p>
            </div>

            <form method=\"POST\" action=\"{{ route('login') }}\" class=\"space-y-6\" aria-label=\"Giriş Formu\">
                @csrf
                
                <div>
                    <label for=\"email\" class=\"block text-sm font-medium text-neutral mb-2\">E-posta Adresi</label>
                    <input type=\"email\" id=\"email\" name=\"email\" value=\"{{ old('email') }}\" required autofocus
                        class=\"w-full px-4 py-3 rounded-xl border {{ \$errors->has('email') ? 'border-red-500 focus:ring-red-500/20' : 'border-neutral-200 focus:border-primary focus:ring-primary/20' }} focus:ring-2 outline-none transition-all\">
                    @error('email')
                        <p class=\"mt-1 text-sm text-red-500\">{{ \$message }}</p>
                    @enderror
                </div>

                <div>
                    <div class=\"flex items-center justify-between mb-2\">
                        <label for=\"password\" class=\"block text-sm font-medium text-neutral\">Şifre</label>
                        <a href=\"{{ route('password.request') }}\" class=\"text-sm text-primary hover:underline\">Şifremi Unuttum</a>
                    </div>
                    <input type=\"password\" id=\"password\" name=\"password\" required
                        class=\"w-full px-4 py-3 rounded-xl border border-neutral-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all\">
                </div>

                <div class=\"flex items-center\">
                    <input id=\"remember\" name=\"remember\" type=\"checkbox\" class=\"h-4 w-4 text-primary focus:ring-primary border-neutral-300 rounded\">
                    <label for=\"remember\" class=\"ml-2 block text-sm text-neutral-600\">Beni Hatırla</label>
                </div>

                <button type=\"submit\" class=\"w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-premium-sm text-sm font-medium text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors\">
                    Giriş Yap
                </button>
            </form>
        </div>
    </x-section>
@endsection
",
    'auth/forgot-password.blade.php' => "@extends('layouts.templates.landing')

@section('template-content')
    <x-section bg=\"gray\" py=\"24\" class=\"min-h-screen flex items-center justify-center\">
        <div class=\"w-full max-w-md bg-white p-8 sm:p-12 rounded-premium-2xl border border-neutral-100 shadow-premium-sm\">
            <div class=\"text-center mb-8\">
                <h1 class=\"text-2xl font-display font-bold text-neutral\">Şifremi Unuttum</h1>
                <p class=\"text-neutral-500 mt-2 text-sm\">E-posta adresinizi girin, size şifre sıfırlama bağlantısı gönderelim.</p>
            </div>

            @if (session('status'))
                <div class=\"mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-600 text-sm\">
                    {{ session('status') }}
                </div>
            @endif

            <form method=\"POST\" action=\"{{ route('password.email') }}\" class=\"space-y-6\">
                @csrf
                
                <div>
                    <label for=\"email\" class=\"block text-sm font-medium text-neutral mb-2\">E-posta Adresi</label>
                    <input type=\"email\" id=\"email\" name=\"email\" value=\"{{ old('email') }}\" required autofocus
                        class=\"w-full px-4 py-3 rounded-xl border {{ \$errors->has('email') ? 'border-red-500 focus:ring-red-500/20' : 'border-neutral-200 focus:border-primary focus:ring-primary/20' }} focus:ring-2 outline-none transition-all\">
                    @error('email')
                        <p class=\"mt-1 text-sm text-red-500\">{{ \$message }}</p>
                    @enderror
                </div>

                <button type=\"submit\" class=\"w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-premium-sm text-sm font-medium text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors\">
                    Sıfırlama Bağlantısı Gönder
                </button>
                
                <div class=\"text-center mt-4\">
                    <a href=\"{{ route('login') }}\" class=\"text-sm text-neutral-500 hover:text-primary transition-colors\">Giriş Ekranına Dön</a>
                </div>
            </form>
        </div>
    </x-section>
@endsection
",
    'auth/reset-password.blade.php' => "@extends('layouts.templates.landing')

@section('template-content')
    <x-section bg=\"gray\" py=\"24\" class=\"min-h-screen flex items-center justify-center\">
        <div class=\"w-full max-w-md bg-white p-8 sm:p-12 rounded-premium-2xl border border-neutral-100 shadow-premium-sm\">
            <div class=\"text-center mb-8\">
                <h1 class=\"text-2xl font-display font-bold text-neutral\">Şifrenizi Sıfırlayın</h1>
                <p class=\"text-neutral-500 mt-2 text-sm\">Lütfen yeni şifrenizi belirleyin.</p>
            </div>

            <form method=\"POST\" action=\"{{ route('password.update') }}\" class=\"space-y-6\">
                @csrf
                <input type=\"hidden\" name=\"token\" value=\"{{ \$request->route('token') }}\">
                
                <div>
                    <label for=\"email\" class=\"block text-sm font-medium text-neutral mb-2\">E-posta Adresi</label>
                    <input type=\"email\" id=\"email\" name=\"email\" value=\"{{ old('email', \$request->email) }}\" required readonly
                        class=\"w-full px-4 py-3 rounded-xl border border-neutral-200 bg-neutral-50 text-neutral-500 outline-none\">
                    @error('email')
                        <p class=\"mt-1 text-sm text-red-500\">{{ \$message }}</p>
                    @enderror
                </div>

                <div>
                    <label for=\"password\" class=\"block text-sm font-medium text-neutral mb-2\">Yeni Şifre</label>
                    <input type=\"password\" id=\"password\" name=\"password\" required autofocus
                        class=\"w-full px-4 py-3 rounded-xl border {{ \$errors->has('password') ? 'border-red-500 focus:ring-red-500/20' : 'border-neutral-200 focus:border-primary focus:ring-primary/20' }} focus:ring-2 outline-none transition-all\">
                    @error('password')
                        <p class=\"mt-1 text-sm text-red-500\">{{ \$message }}</p>
                    @enderror
                </div>

                <div>
                    <label for=\"password_confirmation\" class=\"block text-sm font-medium text-neutral mb-2\">Yeni Şifre (Tekrar)</label>
                    <input type=\"password\" id=\"password_confirmation\" name=\"password_confirmation\" required
                        class=\"w-full px-4 py-3 rounded-xl border border-neutral-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all\">
                </div>

                <button type=\"submit\" class=\"w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-premium-sm text-sm font-medium text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors\">
                    Şifreyi Güncelle
                </button>
            </form>
        </div>
    </x-section>
@endsection
",
    'auth/dashboard-placeholder.blade.php' => "@extends('layouts.templates.landing')

@section('template-content')
    <x-section bg=\"gray\" py=\"24\" class=\"min-h-screen flex items-center justify-center\">
        <div class=\"text-center\">
            <h1 class=\"text-4xl font-display font-bold text-neutral mb-4\">Yönetim Paneli</h1>
            <p class=\"text-neutral-500 mb-8\">Gelecek sprintlerde bu alana Admin Dashboard inşa edilecektir.</p>
            <form method=\"POST\" action=\"{{ route('logout') }}\">
                @csrf
                <button type=\"submit\" class=\"inline-flex justify-center py-2 px-6 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 transition-colors\">
                    Çıkış Yap
                </button>
            </form>
        </div>
    </x-section>
@endsection
",
    'errors/401.blade.php' => "@extends('layouts.templates.landing')
@section('template-content')
    <x-section bg=\"gray\" py=\"24\" class=\"min-h-screen flex flex-col items-center justify-center text-center\">
        <h1 class=\"text-6xl font-display font-bold text-neutral mb-4\">401</h1>
        <p class=\"text-xl text-neutral-600 mb-8\">Yetkisiz Erişim. Lütfen giriş yapın.</p>
        <a href=\"{{ route('login') }}\" class=\"inline-flex px-6 py-3 bg-primary text-white font-medium rounded-xl hover:bg-primary-dark transition\">Giriş Yap</a>
    </x-section>
@endsection
",
    'errors/403.blade.php' => "@extends('layouts.templates.landing')
@section('template-content')
    <x-section bg=\"gray\" py=\"24\" class=\"min-h-screen flex flex-col items-center justify-center text-center\">
        <h1 class=\"text-6xl font-display font-bold text-neutral mb-4\">403</h1>
        <p class=\"text-xl text-neutral-600 mb-8\">Erişim Reddedildi. Bu sayfayı görüntüleme yetkiniz yok.</p>
        <a href=\"{{ url('/') }}\" class=\"inline-flex px-6 py-3 bg-primary text-white font-medium rounded-xl hover:bg-primary-dark transition\">Ana Sayfaya Dön</a>
    </x-section>
@endsection
",
    'errors/419.blade.php' => "@extends('layouts.templates.landing')
@section('template-content')
    <x-section bg=\"gray\" py=\"24\" class=\"min-h-screen flex flex-col items-center justify-center text-center\">
        <h1 class=\"text-6xl font-display font-bold text-neutral mb-4\">419</h1>
        <p class=\"text-xl text-neutral-600 mb-8\">Oturumunuzun süresi doldu. Lütfen tekrar deneyin.</p>
        <a href=\"{{ route('login') }}\" class=\"inline-flex px-6 py-3 bg-primary text-white font-medium rounded-xl hover:bg-primary-dark transition\">Tekrar Giriş Yap</a>
    </x-section>
@endsection
"
];

foreach ($views as $path => $content) {
    createView($path, $content);
}

echo "Auth views created.\n";
