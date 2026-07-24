@extends('layouts.admin')
@section('title', 'Dijital Arşiv Paneli')
@section('content')
    <div class="space-y-6">
        
        <!-- Üst Banner -->
        <div class="bg-gradient-to-r from-teal-900 to-slate-900 p-8 rounded-3xl text-white shadow-premium flex flex-col md:flex-row md:items-center md:justify-between gap-6 border border-teal-950">
            <div>
                <span class="px-2.5 py-1 text-[10px] uppercase font-bold tracking-widest bg-teal-500/20 text-teal-300 rounded-full border border-teal-500/30 font-mono">Digital Archive Suite</span>
                <h1 class="text-2xl font-black mt-2">Dijital Doküman & Arşiv Yönetimi</h1>
                <p class="text-xs text-teal-100 mt-1">Öğrenci, personel, sözleşme, fatura ve kurumsal dijital evrak arşivini güvenle yönetin.</p>
            </div>
            
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.documents.create') }}" class="px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-xs font-bold rounded-xl transition shadow-lg shadow-teal-950 flex items-center gap-1.5 border border-teal-500">
                    Doküman Yükle
                </a>
                <a href="{{ route('admin.documents.index') }}" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-xs font-bold rounded-xl transition border border-slate-700">
                    Tüm Belgeler
                </a>
            </div>
        </div>

        <!-- KPI Kartları -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-neutral-400 uppercase">Toplam Doküman</span>
                    <div class="p-2 bg-teal-50 dark:bg-teal-950 text-teal-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    </div>
                </div>
                <div>
                    <div class="text-3xl font-black text-neutral-900 dark:text-white font-mono">{{ $metrics['total_documents'] }}</div>
                    <p class="text-[11px] text-neutral-500 mt-1">Kayıtlı aktif dijital arşiv belgesi</p>
                </div>
            </div>

            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-neutral-400 uppercase">Toplam Depolama</span>
                    <div class="p-2 bg-blue-50 dark:bg-blue-950 text-blue-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path></svg>
                    </div>
                </div>
                <div>
                    <div class="text-3xl font-black text-neutral-900 dark:text-white font-mono">{{ round($metrics['total_storage_bytes'] / (1024 * 1024), 2) }} MB</div>
                    <p class="text-[11px] text-neutral-500 mt-1">Kullanılan sunucu disk alanı</p>
                </div>
            </div>

            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-neutral-400 uppercase">Aktif Kategoriler</span>
                    <div class="p-2 bg-purple-50 dark:bg-purple-950 text-purple-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                </div>
                <div>
                    <div class="text-3xl font-black text-neutral-900 dark:text-white font-mono">{{ count($metrics['category_distribution']) }}</div>
                    <p class="text-[11px] text-neutral-500 mt-1">Sınıflandırılmış arşiv türü</p>
                </div>
            </div>

            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-neutral-400 uppercase">Son İşlemler</span>
                    <div class="p-2 bg-amber-50 dark:bg-amber-950 text-amber-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div>
                    <div class="text-3xl font-black text-neutral-900 dark:text-white font-mono">{{ count($metrics['recent_logs']) }}</div>
                    <p class="text-[11px] text-neutral-500 mt-1">Güncel indirme & yükleme kaydı</p>
                </div>
            </div>

        </div>

        <!-- Kategori Dağılımı & Son Loglar -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Kategori Dağılımı</h3>
                
                <div class="space-y-4">
                    @forelse($metrics['category_distribution'] as $cat)
                        <div class="space-y-1">
                            <div class="flex justify-between items-center text-xs font-bold">
                                <span class="text-neutral-700 dark:text-neutral-300 flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $cat->color }}"></span>
                                    {{ $cat->name }}
                                </span>
                                <span class="text-neutral-500 font-mono">{{ $cat->count }} Belge</span>
                            </div>
                            <div class="w-full bg-neutral-100 dark:bg-neutral-800 h-2 rounded-full overflow-hidden">
                                <div class="h-full rounded-full" style="width: {{ $metrics['total_documents'] > 0 ? ($cat->count / $metrics['total_documents']) * 100 : 0 }}%; background-color: {{ $cat->color }}"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-xs text-neutral-400 py-6">Kategori verisi bulunmamaktadır.</div>
                    @endforelse
                </div>
            </div>

            <!-- Son Erişim Logları -->
            <div class="lg:col-span-2 bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Son Arşiv Hareketleri (Audit Logs)</h3>
                
                <div class="space-y-3">
                    @forelse($metrics['recent_logs'] as $log)
                        <div class="p-3 bg-neutral-50 dark:bg-neutral-800/40 border border-neutral-100 dark:border-neutral-800 rounded-xl flex items-center justify-between text-xs">
                            <div class="space-y-0.5">
                                <div class="font-bold text-neutral-800 dark:text-neutral-200">
                                    {{ $log->document->title ?? 'Silinmiş Belge' }}
                                </div>
                                <div class="text-[10px] text-neutral-400">
                                    Kullanıcı: {{ $log->user->name ?? 'Sistem' }} | IP: {{ $log->ip_address ?? '127.0.0.1' }}
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase {{ $log->action === 'upload' ? 'bg-green-100 text-green-700' : ($log->action === 'download' ? 'bg-blue-100 text-blue-700' : 'bg-neutral-200 text-neutral-700') }}">
                                    {{ $log->action }}
                                </span>
                                <div class="text-[10px] font-mono text-neutral-400 mt-1">{{ $log->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-xs text-neutral-400 py-6">Henüz bir arşiv kaydı bulunmamaktadır.</div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>
@endsection
