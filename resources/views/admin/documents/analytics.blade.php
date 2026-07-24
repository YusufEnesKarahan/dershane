@extends('layouts.admin')
@section('title', 'Doküman Analitiği')
@section('content')
    <div class="space-y-6">
        
        <!-- Header -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <h1 class="text-lg font-bold text-neutral-900 dark:text-white">Dijital Arşiv Analitiği & Raporları</h1>
            <p class="text-xs text-neutral-500 mt-1">Aylık belge yükleme trendleri, depolama hacmi ve kategoriye göre boyut dağılımı.</p>
        </div>

        <!-- Trendler & Hacim -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Aylık Yükleme Trendi -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">📅 Aylık Belge Yükleme Trendi</h3>
                
                <div class="space-y-3">
                    @forelse($metrics['monthly_uploads'] as $m)
                        <div class="space-y-1">
                            <div class="flex justify-between items-center text-xs font-bold">
                                <span class="font-mono text-neutral-700 dark:text-neutral-300">{{ $m->month }}</span>
                                <span class="font-mono text-teal-600">{{ $m->count }} Doküman ({{ round($m->total_size / (1024 * 1024), 2) }} MB)</span>
                            </div>
                            <div class="w-full bg-neutral-100 dark:bg-neutral-800 h-2.5 rounded-full overflow-hidden">
                                <div class="bg-teal-600 h-full rounded-full" style="width: {{ $metrics['total_documents'] > 0 ? ($m->count / $metrics['total_documents']) * 100 : 50 }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-xs text-neutral-400 py-6">Geçmiş yükleme verisi bulunmamaktadır.</div>
                    @endforelse
                </div>
            </div>

            <!-- Kategoriye Göre Depolama Hacmi -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">💾 Kategoriye Göre Depolama Hacmi</h3>
                
                <div class="space-y-3">
                    @forelse($metrics['category_distribution'] as $cat)
                        <div class="p-3 bg-neutral-50 dark:bg-neutral-800/40 border border-neutral-100 dark:border-neutral-800 rounded-xl flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full" style="background-color: {{ $cat->color }}"></span>
                                <span class="font-bold text-neutral-800 dark:text-neutral-200">{{ $cat->name }}</span>
                            </div>
                            <div class="text-right">
                                <span class="font-bold font-mono text-neutral-900 dark:text-white">{{ round(($cat->total_size ?? 0) / (1024 * 1024), 2) }} MB</span>
                                <div class="text-[10px] text-neutral-400 font-mono">{{ $cat->count }} Belge</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-xs text-neutral-400 py-6">Kategori dağılımı verisi yok.</div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>
@endsection
