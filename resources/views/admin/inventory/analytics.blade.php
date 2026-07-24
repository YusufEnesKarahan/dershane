@extends('layouts.admin')
@section('title', 'Envanter Analitiği')
@section('content')
    <div class="space-y-6">
        
        <!-- Header -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <h1 class="text-lg font-bold text-neutral-900 dark:text-white">Detaylı Stok & Demirbaş Analitiği</h1>
            <p class="text-xs text-neutral-500 mt-1">Kritik stok seviyesindeki ürünler, satın alma trendleri ve en çok kullanılan malzemelerin takibi.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Kritik Stok Listesi -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white text-red-500 flex items-center gap-1.5">
                    <span>⚠️ Kritik Stok Uyarıları</span>
                </h3>
                
                <div class="space-y-3">
                    @forelse($report['critical_items'] as $item)
                        <div class="p-3 bg-red-50 dark:bg-red-950/20 border border-red-100 dark:border-red-900/50 rounded-xl flex items-center justify-between text-xs">
                            <div>
                                <span class="font-bold text-red-800 dark:text-red-300">{{ $item->name }}</span>
                                <div class="text-[10px] text-red-600 dark:text-red-400 mt-0.5">Depo: {{ $item->warehouse->name ?? 'Merkez Depo' }}</div>
                            </div>
                            <span class="font-bold font-mono text-red-800 dark:text-red-300">{{ $item->quantity }} / {{ $item->minimum_quantity }} {{ $item->unit }}</span>
                        </div>
                    @empty
                        <div class="text-center text-xs text-neutral-400 py-6">Kritik seviyede stok bulunmamaktadır.</div>
                    @endforelse
                </div>
            </div>

            <!-- En Çok Kullanılan Ürünler -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">📈 En Çok Sarf Edilen Malzemeler</h3>
                
                <div class="space-y-3">
                    @forelse($report['top_items'] as $item)
                        <div class="p-3 bg-neutral-50 dark:bg-neutral-800/40 border border-neutral-100 dark:border-neutral-800 rounded-xl flex items-center justify-between text-xs">
                            <div>
                                <span class="font-bold text-neutral-800 dark:text-neutral-200">{{ $item->name }}</span>
                                <div class="text-[10px] text-neutral-400 mt-0.5">Kategori: {{ $item->category->name ?? '-' }}</div>
                            </div>
                            <span class="font-bold font-mono text-teal-600">{{ $item->usage_qty ?? 0 }} {{ $item->unit }} Harcandı</span>
                        </div>
                    @empty
                        <div class="text-center text-xs text-neutral-400 py-6">Kullanım verisi bulunmamaktadır.</div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Satın Alma Sipariş Trendleri -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
            <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Satın Alma Sipariş Hacmi</h3>
            
            <div class="space-y-4">
                @forelse($report['order_trends'] as $trend)
                    <div class="space-y-1">
                        <div class="flex justify-between items-center text-xs font-bold">
                            <span class="text-neutral-700 dark:text-neutral-300 font-mono">{{ $trend['label'] }}</span>
                            <span class="text-neutral-500 font-mono">₺{{ number_format($trend['total'], 2) }}</span>
                        </div>
                        <div class="w-full bg-neutral-100 dark:bg-neutral-800 h-3 rounded-full overflow-hidden">
                            <div class="bg-teal-600 h-full rounded-full" style="width: {{ $report['monthly_purchase_sum'] > 0 ? ($trend['total'] / $report['monthly_purchase_sum']) * 100 : 50 }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-xs text-neutral-400 py-6">Geçmiş sipariş verisi bulunmamaktadır.</div>
                @endforelse
            </div>
        </div>

    </div>
@endsection
