@extends('layouts.admin')
@section('title', 'İK Analitik Raporları')
@section('content')
    <div class="space-y-6">
        
        <!-- Header -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <h1 class="text-lg font-bold text-neutral-900 dark:text-white">İnsan Kaynakları & İş Gücü Analitiği</h1>
            <p class="text-xs text-neutral-500 mt-1">Devamsızlık oranları, mesai saatleri, fazla mesailer, izin oranları, masraf toplamları ve bordro maliyet analizleri.</p>
        </div>

        <!-- Analitik Özet Kartları -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-2">
                <span class="text-xs font-bold text-neutral-400 uppercase">Aylık Toplam Çalışma</span>
                <div class="text-3xl font-black text-neutral-900 dark:text-white font-mono">{{ $report['total_worked_hours'] }} Sa</div>
                <p class="text-[10px] text-neutral-500">Mevcut ay içinde girilen toplam mesai saati</p>
            </div>

            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-2">
                <span class="text-xs font-bold text-neutral-400 uppercase">Aylık Fazla Mesai</span>
                <div class="text-3xl font-black text-neutral-900 dark:text-white font-mono text-green-500">{{ $report['total_overtime_hours'] }} Sa</div>
                <p class="text-[10px] text-neutral-500">Standart 8 saat üzeri yapılan fazla mesailer</p>
            </div>

            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-2">
                <span class="text-xs font-bold text-neutral-400 uppercase">Devamsızlık Oranı</span>
                <div class="text-3xl font-black text-neutral-900 dark:text-white font-mono text-amber-500">%{{ $report['absences_rate'] }}</div>
                <p class="text-[10px] text-neutral-500">Aktif çalışanların aylık devamsızlık yüzdesi</p>
            </div>

            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-2">
                <span class="text-xs font-bold text-neutral-400 uppercase">Aylık Toplam Masraf</span>
                <div class="text-3xl font-black text-neutral-900 dark:text-white font-mono text-violet-500">₺{{ number_format($report['total_expenses_collected'], 2) }}</div>
                <p class="text-[10px] text-neutral-500">Onaylanmış aylık personel masrafları</p>
            </div>

        </div>

        <!-- Maaş Trendi Geçmişi -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
            <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Maaş & Maliyet Trendi (Son 6 Dönem)</h3>
            
            <div class="space-y-4">
                @foreach($report['salary_chart'] as $chart)
                    <div class="space-y-1">
                        <div class="flex justify-between items-center text-xs font-bold">
                            <span class="text-neutral-700 dark:text-neutral-300 font-mono">{{ $chart['label'] }}</span>
                            <span class="text-neutral-500 font-mono">Net: ₺{{ number_format($chart['net'], 2) }} | Brüt: ₺{{ number_format($chart['gross'], 2) }}</span>
                        </div>
                        <div class="w-full bg-neutral-100 dark:bg-neutral-800 h-3 rounded-full overflow-hidden flex">
                            @if($chart['gross'] > 0)
                                <div class="bg-indigo-600 h-full" style="width: {{ ($chart['net'] / $chart['gross']) * 100 }}%"></div>
                                <div class="bg-amber-500 h-full" style="width: {{ (($chart['gross'] - $chart['net']) / $chart['gross']) * 100 }}%"></div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="flex items-center gap-4 text-[10px] text-neutral-500 font-bold justify-end pt-2">
                <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 bg-indigo-600 rounded"></span> Net Maaş Ödemeleri</div>
                <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 bg-amber-500 rounded"></span> Vergi & SGK Kesintileri</div>
            </div>
        </div>

    </div>
@endsection
