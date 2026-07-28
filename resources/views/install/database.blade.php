<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veritabanı Kurulumu - Dershane ERP Kurulumu</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-xl w-full bg-white dark:bg-slate-800 shadow-xl rounded-3xl overflow-hidden border border-slate-100 dark:border-slate-700">
        <div class="bg-gradient-to-r from-indigo-900 to-slate-900 p-8 text-white">
            <div class="flex justify-between items-center">
                <h1 class="text-xl font-bold">Adım 2: Veritabanı ve Şema Yapılandırması</h1>
                <span class="text-xs px-2.5 py-1 bg-indigo-500/20 text-indigo-300 rounded-full border border-indigo-500/30">Veritabanı</span>
            </div>
            <p class="text-xs text-slate-300 mt-2">Dershane ERP tabloları ve varsayılan sistem rolleri veritabanına yüklenecektir.</p>
        </div>
        <div class="p-8 space-y-6">
            @if(session('error'))
                <div class="p-4 bg-red-50 border border-red-200 text-red-600 rounded-xl text-xs font-semibold">
                    {{ session('error') }}
                </div>
            @endif

            <div class="p-4 bg-slate-50 dark:bg-slate-800/40 rounded-xl border border-slate-100 dark:border-slate-700/50">
                <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 mb-2">Veritabanı Bağlantısı:</h3>
                <div class="grid grid-cols-2 gap-2 text-xs text-slate-500 dark:text-slate-400">
                    <div>Bağlantı Türü (Driver):</div>
                    <div class="font-semibold text-slate-700 dark:text-slate-300 capitalize">{{ config('database.default') }}</div>
                    <div>Veritabanı Adı:</div>
                    <div class="font-semibold text-slate-700 dark:text-slate-300 break-all">
                        {{ config('database.connections.' . config('database.default') . '.database') }}
                    </div>
                </div>
            </div>

            <div class="text-xs text-slate-500 leading-relaxed">
                <p><strong>Önemli:</strong> "Tabloları Oluştur ve Seed Et" butonuna tıkladığınızda, veritabanı tabloları migrate edilecek ve Spatie/RBAC rolleri sisteme tanımlanacaktır. Bu işlem veritabanı boyutuna bağlı olarak birkaç saniye sürebilir.</p>
            </div>

            <form action="{{ route('install.migrate') }}" method="POST">
                @csrf
                <div class="pt-4 flex justify-between items-center">
                    <a href="{{ route('install.requirements') }}" class="text-xs text-slate-500 hover:underline">Geri Dön</a>
                    <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg transition-all text-sm flex items-center gap-2">
                        Tabloları Oluştur ve Seed Et
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
