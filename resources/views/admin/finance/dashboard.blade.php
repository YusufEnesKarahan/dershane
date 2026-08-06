@extends('layouts.admin')

@section('title', 'Finans Gösterge Paneli')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white flex items-center gap-3">
                <i class="fas fa-chart-line text-emerald-600"></i> Finans & Tahsilat Gösterge Paneli
            </h1>
            <p class="text-sm text-slate-500 mt-1">Nakit akışı, fatura durumları ve 12 aylık tahsilat trendlerini izleyin.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.invoices.create') }}" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm transition-colors shadow-sm flex items-center gap-2">
                <i class="fas fa-file-invoice"></i> Yeni Fatura Kes
            </a>
            <a href="{{ route('admin.pre-registrations.create') }}" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-sm transition-colors shadow-sm flex items-center gap-2">
                <i class="fas fa-user-plus"></i> Ön Kayıt Al
            </a>
        </div>
    </div>

    <!-- Özet Metrik Kartları (6 Kart) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Toplam Tahsilat</div>
            <div class="text-xl font-black text-emerald-600 mt-1">{{ number_format($metrics['total_collected'], 2, ',', '.') }} TL</div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Bekleyen Tahsilat</div>
            <div class="text-xl font-black text-amber-600 mt-1">{{ number_format($metrics['pending_amount'], 2, ',', '.') }} TL</div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Bu Ay Tahsilat</div>
            <div class="text-xl font-black text-blue-600 mt-1">{{ number_format($metrics['this_month_collected'], 2, ',', '.') }} TL</div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Bugün Tahsilat</div>
            <div class="text-xl font-black text-blue-600 mt-1">{{ number_format($metrics['today_collected'], 2, ',', '.') }} TL</div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Açık Fatura</div>
            <div class="text-xl font-black text-blue-600 mt-1">{{ $metrics['open_invoices_count'] }} Adet</div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Geciken Fatura</div>
            <div class="text-xl font-black text-rose-600 mt-1">{{ $metrics['overdue_invoices_count'] }} Adet</div>
        </div>
    </div>

    <!-- Trend Grafikleri Paneli -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- 1. Son 12 Ay Tahsilat Trendi -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm space-y-4">
            <h3 class="text-base font-black text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-coins text-emerald-600"></i> Son 12 Ay Tahsilat Grafiği
            </h3>
            <div class="h-64 flex items-end justify-between gap-2 pt-8 px-2 border-b border-slate-100 dark:border-slate-800">
                @php
                    $maxVal = max(array_merge([1000], $metrics['chart_collections']));
                @endphp
                @foreach($metrics['chart_months'] as $idx => $m)
                    @php
                        $val = $metrics['chart_collections'][$idx] ?? 0;
                        $height = $maxVal > 0 ? round(($val / $maxVal) * 100) : 5;
                    @endphp
                    <div class="flex-1 flex flex-col items-center gap-2 group relative">
                        <div class="w-full bg-emerald-500 hover:bg-emerald-600 rounded-t-lg transition-all" style="height: {{ max(6, $height) }}%;">
                            <div class="opacity-0 group-hover:opacity-100 absolute -top-8 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[10px] py-1 px-2 rounded shadow whitespace-nowrap z-10 transition-opacity">
                                {{ number_format($val, 0, ',', '.') }} TL
                            </div>
                        </div>
                        <span class="text-[10px] text-slate-400 font-bold truncate w-full text-center">{{ $m }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- 2. Ön Kayıt vs Kesin Kayıt Trendi -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm space-y-4">
            <h3 class="text-base font-black text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-users-cog text-blue-600"></i> Ön Kayıt vs Kesin Kayıt Dönüşüm Trendi
            </h3>
            <div class="h-64 flex items-end justify-between gap-2 pt-8 px-2 border-b border-slate-100 dark:border-slate-800">
                @php
                    $maxReg = max(array_merge([10], $metrics['chart_pre_regs']));
                @endphp
                @foreach($metrics['chart_months'] as $idx => $m)
                    @php
                        $preVal = $metrics['chart_pre_regs'][$idx] ?? 0;
                        $convVal = $metrics['chart_converted'][$idx] ?? 0;
                        $hPre = $maxReg > 0 ? round(($preVal / $maxReg) * 100) : 5;
                        $hConv = $maxReg > 0 ? round(($convVal / $maxReg) * 100) : 5;
                    @endphp
                    <div class="flex-1 flex flex-col items-center gap-1 group relative">
                        <div class="w-full flex items-end justify-center gap-1" style="height: 100%;">
                            <div class="w-1/2 bg-blue-400 rounded-t" style="height: {{ max(6, $hPre) }}%;" title="Ön Kayıt: {{ $preVal }}"></div>
                            <div class="w-1/2 bg-emerald-500 rounded-t" style="height: {{ max(6, $hConv) }}%;" title="Kesin Kayıt: {{ $convVal }}"></div>
                        </div>
                        <span class="text-[10px] text-slate-400 font-bold truncate w-full text-center">{{ $m }}</span>
                    </div>
                @endforeach
            </div>
            <div class="flex justify-center items-center gap-6 text-xs font-bold pt-2">
                <div class="flex items-center gap-2"><span class="w-3 h-3 bg-blue-400 rounded"></span> Ön Kayıt Sayısı</div>
                <div class="flex items-center gap-2"><span class="w-3 h-3 bg-emerald-500 rounded"></span> Kesin Kayıt Dönüşümü</div>
            </div>
        </div>
    </div>

    <!-- Son Tahsilatlar & Son Faturalar -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Son Tahsilatlar -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm p-6 space-y-4">
            <h3 class="text-base font-black text-slate-900 dark:text-white">Son Alınan Tahsilatlar</h3>
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($recentPayments as $pay)
                    <div class="py-3 flex items-center justify-between">
                        <div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white">{{ $pay->student?->first_name }} {{ $pay->student?->last_name }}</div>
                            <div class="text-xs text-slate-400">{{ $pay->payment_date?->format('d.m.Y H:i') }} | Tür: {{ $pay->payment_method }}</div>
                        </div>
                        <div class="text-right font-black text-emerald-600 text-base">
                            +{{ number_format($pay->amount, 2, ',', '.') }} TL
                        </div>
                    </div>
                @empty
                    <div class="py-4 text-xs text-slate-500 italic text-center">Henüz tahsilat kaydı bulunmuyor.</div>
                @endforelse
            </div>
        </div>

        <!-- Son Faturalar -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm p-6 space-y-4">
            <h3 class="text-base font-black text-slate-900 dark:text-white">Son Kesilen Faturalar</h3>
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($recentInvoices as $inv)
                    <div class="py-3 flex items-center justify-between">
                        <div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white">{{ $inv->student?->first_name }} {{ $inv->student?->last_name }}</div>
                            <div class="text-xs text-slate-400">No: {{ $inv->invoice_number }} | Vade: {{ $inv->due_date?->format('d.m.Y') }}</div>
                        </div>
                        <div class="text-right">
                            <div class="font-black text-slate-900 dark:text-white text-base">{{ number_format($inv->total_amount, 2, ',', '.') }} TL</div>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-700">{{ $inv->status }}</span>
                        </div>
                    </div>
                @empty
                    <div class="py-4 text-xs text-slate-500 italic text-center">Henüz fatura kaydı bulunmuyor.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
