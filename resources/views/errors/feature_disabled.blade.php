<!DOCTYPE html>
<html lang="tr" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Özellik Kısıtlaması</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-slate-900 bg-slate-50 flex items-center justify-center p-4">
    <div class="max-w-md w-full text-center bg-white p-8 rounded-2xl shadow-xl border border-slate-200/80">
        <div class="w-16 h-16 bg-amber-50 text-amber-500 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H10m0-6V7a2 2 0 012-2h0a2 2 0 012 2v4m-6 4h8a2 2 0 002-2v-6a2 2 0 00-2-2H8a2 2 0 00-2 2v6a2 2 0 002 2z"/>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-slate-800 mb-2">Paket Özellik Kısıtlaması</h2>
        <p class="text-slate-600 mb-6">{{ $message ?? 'Bu özellik kullandığınız pakette aktif değil.' }}</p>
        <div class="bg-amber-50/60 border border-amber-200/60 rounded-xl p-4 mb-6 text-sm text-amber-900 text-left">
            <span class="font-semibold block mb-1">Paketinizi Yükseltin</span>
            Bu modülü (<code>{{ $featureCode ?? 'modül' }}</code>) kullanabilmek için lütfen üst pakete geçiş yapın veya yöneticinizle iletişime geçin.
        </div>
        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('admin.dashboard') }}" 
           class="inline-flex items-center justify-center px-5 py-2.5 bg-slate-900 text-white font-medium rounded-xl hover:bg-slate-800 transition-colors">
            Geri Dön
        </a>
    </div>
</body>
</html>
