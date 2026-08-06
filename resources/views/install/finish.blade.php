<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kurulum Tamamlandı - Dershane ERP Kurulumu</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-xl w-full bg-white dark:bg-slate-800 shadow-xl rounded-3xl overflow-hidden border border-slate-100 dark:border-slate-700">
        <div class="bg-gradient-to-r from-emerald-850 from-green-700 to-slate-900 p-8 text-white text-center">
            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 border border-white/30 shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h1 class="text-2xl font-black">Tebrikler! Kurulum Tamamlandı</h1>
            <p class="text-xs text-slate-200 mt-2">Dershane ERP SaaS platformu kullanıma hazır.</p>
        </div>
        <div class="p-8 space-y-6">
            <div class="space-y-4">
                <div class="p-4 bg-slate-50 dark:bg-slate-800/40 rounded-xl border border-slate-100 dark:border-slate-700/50">
                    <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 mb-2">Kurulum Özeti</h3>
                    <ul class="space-y-2 text-xs text-slate-600 dark:text-slate-400">
                        <li class="flex items-center gap-1.5 text-green-600 dark:text-green-400 font-semibold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            Veritabanı tabloları kuruldu ve seed edildi.
                        </li>
                        <li class="flex items-center gap-1.5 text-green-600 dark:text-green-400 font-semibold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            Super Admin kullanıcısı oluşturuldu.
                        </li>
                        <li class="flex items-center gap-1.5 text-green-600 dark:text-green-400 font-semibold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            Varsayılan kurum şubesi açıldı.
                        </li>
                        <li class="flex items-center gap-1.5 text-green-600 dark:text-green-400 font-semibold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            Aktif SaaS lisansı oluşturuldu.
                        </li>
                    </ul>
                </div>
                
                <div class="text-xs text-slate-500 leading-relaxed bg-amber-50 dark:bg-amber-950/20 p-4 rounded-xl border border-amber-200/50 dark:border-amber-800/30">
                    <p><strong>Güvenlik Uyarısı:</strong> Kurulum işlemleri başarıyla kilitlendi. Yeniden kurulum yapmak güvenlik gerekçesiyle engellenmiştir.</p>
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <a href="{{ route('login') }}" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg transition-all text-sm flex items-center gap-2">
                    Yönetim Paneline Git
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
