<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yönetici Tanımlama - Dershane ERP Kurulumu</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-xl w-full bg-white dark:bg-slate-800 shadow-xl rounded-3xl overflow-hidden border border-slate-100 dark:border-slate-700">
        <div class="bg-gradient-to-r from-blue-900 to-slate-900 p-8 text-white">
            <div class="flex justify-between items-center">
                <h1 class="text-xl font-bold">Adım 3: İlk Yapılandırma & Yönetici Hesabı</h1>
                <span class="text-xs px-2.5 py-1 bg-blue-500/20 text-blue-300 rounded-full border border-blue-500/30">Super Admin & Kurum</span>
            </div>
            <p class="text-xs text-slate-300 mt-2">Sistemdeki en yetkili kullanıcı (Super Admin) ve varsayılan kurum şubesi tanımlanır.</p>
        </div>
        <form action="{{ route('install.storeAdmin') }}" method="POST" class="p-8 space-y-4">
            @csrf
            
            @if($errors->any())
                <div class="p-4 bg-red-50 border border-red-200 text-red-600 rounded-xl text-xs">
                    <ul class="list-disc pl-4 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="space-y-4">
                <h3 class="text-sm font-bold text-blue-900 dark:text-blue-400 border-b pb-2 border-slate-100 dark:border-slate-700">Kurum Bilgisi</h3>
                <div>
                    <label for="branch_name" class="block text-xs font-bold text-slate-500 uppercase mb-1">Varsayılan Şube Adı</label>
                    <input type="text" name="branch_name" id="branch_name" required value="{{ old('branch_name', 'Merkez Şube') }}" 
                        class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-800 dark:border-slate-700">
                </div>

                <h3 class="text-sm font-bold text-blue-900 dark:text-blue-400 border-b pb-2 border-slate-100 dark:border-slate-700 pt-2">Super Admin Kullanıcı Bilgileri</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-xs font-bold text-slate-500 uppercase mb-1">Ad Soyad</label>
                        <input type="text" name="name" id="name" required value="{{ old('name') }}" placeholder="örn. Ahmet Yılmaz"
                            class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-800 dark:border-slate-700">
                    </div>
                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-500 uppercase mb-1">E-Posta Adresi</label>
                        <input type="email" name="email" id="email" required value="{{ old('email') }}" placeholder="örn. admin@dershane.com"
                            class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-800 dark:border-slate-700">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-500 uppercase mb-1">Şifre</label>
                        <input type="password" name="password" id="password" required placeholder="En az 8 karakter"
                            class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-800 dark:border-slate-700">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-slate-500 uppercase mb-1">Şifre Tekrar</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="Şifrenizi onaylayın"
                            class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-800 dark:border-slate-700">
                    </div>
                </div>
            </div>

            <div class="pt-6 flex justify-between items-center">
                <a href="{{ route('install.database') }}" class="text-xs text-slate-500 hover:underline">Geri Dön</a>
                <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg transition-all text-sm flex items-center gap-2">
                    Kurulumu Tamamla
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </button>
            </div>
        </form>
    </div>
</body>
</html>
