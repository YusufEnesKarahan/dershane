<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dershane ERP - Kurulum Sihirbazı</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-xl w-full bg-white dark:bg-slate-800 shadow-xl rounded-3xl overflow-hidden border border-slate-100 dark:border-slate-700">
        <div class="bg-gradient-to-r from-blue-900 to-slate-900 p-8 text-white text-center">
            <span class="px-2.5 py-1 text-[10px] uppercase font-bold tracking-widest bg-blue-500/20 text-blue-300 rounded-full border border-blue-500/30">Dershane ERP v5.9</span>
            <h1 class="text-3xl font-black mt-3">Kurulum Sihirbazına Hoş Geldiniz</h1>
            <p class="text-xs text-slate-300 mt-2">Dershane ERP sisteminizi dakikalar içinde hazır hale getirelim.</p>
        </div>
        <div class="p-8 space-y-6">
            <div class="text-sm leading-relaxed text-slate-600 dark:text-slate-300">
                <p class="mb-4">Bu kurulum sihirbazı, Dershane ERP uygulamasını production ortamı için hazır hale getirecektir. Sihirbaz şu adımları gerçekleştirecektir:</p>
                <ul class="list-disc pl-5 space-y-2">
                    <li>Sistem gereksinimleri ve yazma yetkileri kontrolleri</li>
                    <li>Veritabanı tablolarının oluşturulması (Migration) ve temel rollerin tanımlanması</li>
                    <li>İlk Super Admin kullanıcısının oluşturulması</li>
                    <li>Varsayılan kurum/şube ve SaaS lisansının aktifleştirilmesi</li>
                </ul>
            </div>
            <div class="pt-4 flex justify-end">
                <a href="{{ route('install.requirements') }}" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg transition-all text-sm flex items-center gap-2">
                    Kuruluma Başla
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
