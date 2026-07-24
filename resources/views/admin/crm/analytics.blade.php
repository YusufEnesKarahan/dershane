@extends('layouts.admin')
@section('title', 'CRM Satış & Başarı Analitiği')
@section('content')
    <div class="space-y-6">
        
        <!-- Üst Başlık -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <h1 class="text-lg font-bold text-neutral-900 dark:text-white">CRM Başarı & Dönüşüm Analitiği</h1>
            <p class="text-xs text-neutral-500 mt-1">Aday kaynakları verimliliği, şube kayıt başarı oranları ve genel satış hunisi dağılımı.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Aday Kaynakları Dağılımı -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Lead Kaynakları Grafiği</h3>
                
                <div class="space-y-4">
                    @forelse($analytics['source_distribution'] as $source)
                        <div>
                            <div class="flex items-center justify-between text-xs font-bold text-neutral-600 mb-1">
                                <span>{{ $source->name }}</span>
                                <span class="text-primary font-mono">{{ $source->total }} Aday</span>
                            </div>
                            <div class="w-full bg-neutral-100 dark:bg-neutral-800 h-2 rounded-full overflow-hidden">
                                @php
                                    $srcPercent = $analytics['total_leads'] > 0 ? ($source->total / $analytics['total_leads']) * 100 : 0;
                                @endphp
                                <div class="bg-primary h-full" style="width: {{ $srcPercent }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-xs text-neutral-400 py-6">Kayıtlı veri bulunamadı.</div>
                    @endforelse
                </div>
            </div>

            <!-- Şube Kayıt Başarı Oranları -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Şubelere Göre Kayda Dönüşüm</h3>
                
                <div class="space-y-4">
                    @forelse($analytics['branch_performance'] as $branch)
                        <div>
                            <div class="flex items-center justify-between text-xs font-bold text-neutral-600 mb-1">
                                <span>{{ $branch->name }}</span>
                                <span class="text-green-600 font-mono">{{ $branch->converted_leads }} / {{ $branch->total_leads }} Kayıt</span>
                            </div>
                            <div class="w-full bg-neutral-100 dark:bg-neutral-800 h-2 rounded-full overflow-hidden">
                                @php
                                    $brPercent = $branch->total_leads > 0 ? ($branch->converted_leads / $branch->total_leads) * 100 : 0;
                                @endphp
                                <div class="bg-green-500 h-full" style="width: {{ $brPercent }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-xs text-neutral-400 py-6">Kayıtlı veri bulunamadı.</div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>
@endsection
