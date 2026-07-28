<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Gereksinimleri - Dershane ERP Kurulumu</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-xl w-full bg-white dark:bg-slate-800 shadow-xl rounded-3xl overflow-hidden border border-slate-100 dark:border-slate-700">
        <div class="bg-gradient-to-r from-indigo-900 to-slate-900 p-8 text-white">
            <div class="flex justify-between items-center">
                <h1 class="text-xl font-bold">Adım 1: Gereksinim Kontrolü</h1>
                <span class="text-xs px-2.5 py-1 bg-indigo-500/20 text-indigo-300 rounded-full border border-indigo-500/30">Gereksinimler</span>
            </div>
            <p class="text-xs text-slate-300 mt-2">Dershane ERP'nin çalışması için gereken sunucu yapılandırmaları.</p>
        </div>
        <div class="p-8 space-y-6">
            <div class="space-y-4">
                @php $allSatisfied = true; @endphp
                @foreach($requirements as $key => $req)
                    @php if(!$req['satisfied']) $allSatisfied = false; @endphp
                    <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800/40 rounded-xl border border-slate-100 dark:border-slate-700/50">
                        <div>
                            <span class="text-sm font-semibold block text-slate-700 dark:text-slate-200">{{ $req['name'] }}</span>
                            <span class="text-xs text-slate-400">Mevcut: {{ $req['current'] }}</span>
                        </div>
                        <div>
                            @if($req['satisfied'])
                                <span class="px-3 py-1 bg-green-50 dark:bg-green-950/20 text-green-600 dark:text-green-400 text-xs font-bold rounded-lg border border-green-200 dark:border-green-800/30 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    Uyumlu
                                </span>
                            @else
                                <span class="px-3 py-1 bg-red-50 dark:bg-red-950/20 text-red-600 dark:text-red-400 text-xs font-bold rounded-lg border border-red-200 dark:border-red-800/30 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    Hata
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pt-4 flex justify-between items-center">
                <a href="{{ route('install.welcome') }}" class="text-xs text-slate-500 hover:underline">Geri Dön</a>
                
                @if($allSatisfied)
                    <a href="{{ route('install.database') }}" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg transition-all text-sm flex items-center gap-2">
                        Devam Et
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                @else
                    <button disabled class="px-6 py-3 bg-slate-300 text-slate-500 dark:bg-slate-700 dark:text-slate-400 font-bold rounded-xl text-sm flex items-center gap-2 cursor-not-allowed">
                        Eksikleri Giderin
                    </button>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
