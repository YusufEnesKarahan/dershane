@extends('layouts.admin')
@section('title', 'Kayıt Workflow Pipeline')
@section('content')
    <div class="space-y-6">
        
        <!-- Header -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <h1 class="text-lg font-bold text-neutral-900 dark:text-white">Kayıt Workflow Pipeline (Aşamalar)</h1>
            <p class="text-xs text-neutral-500 mt-1">Başvuruların ön kayıttan kesin kayda kadarki tüm süreç aşamalarını takip edin.</p>
        </div>

        @php
            $stages = [
                'pre_registration' => '1. Ön Kayıt Başvuruları',
                'document_pending' => '2. Evrak Bekleyenler',
                'document_completed' => '3. Evrak Onaylananlar',
                'contract_ready' => '4. Sözleşme İmzası',
                'payment_pending' => '5. Ödeme / Finans',
                'enrolled' => '6. Kesin Kayıtlılar'
            ];
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 overflow-x-auto pb-4">
            @foreach($stages as $stageKey => $stageTitle)
                <div class="bg-neutral-50 dark:bg-neutral-800/40 p-4 rounded-2xl border border-neutral-100 dark:border-neutral-800 space-y-4 min-w-[220px]">
                    
                    @php
                        $stageAdmissions = $admissions->filter(fn($a) => $a->status === $stageKey);
                    @endphp

                    <div class="flex items-center justify-between border-b border-neutral-100 dark:border-neutral-800 pb-2">
                        <span class="text-xs font-bold text-neutral-800 dark:text-neutral-200">{{ $stageTitle }}</span>
                        <span class="px-2 py-0.5 text-[10px] font-bold bg-neutral-200 dark:bg-neutral-800 rounded text-neutral-600 dark:text-neutral-400 font-mono">{{ $stageAdmissions->count() }}</span>
                    </div>

                    <div class="space-y-3">
                        @forelse($stageAdmissions as $adm)
                            <div class="bg-white dark:bg-neutral-900 p-4 rounded-xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-2">
                                <div class="text-xs font-bold text-neutral-900 dark:text-white">
                                    <a href="{{ route('admin.admission.show', $adm->id) }}" class="hover:text-primary transition">
                                        {{ $adm->first_name }} {{ $adm->last_name }}
                                    </a>
                                </div>
                                <div class="text-[10px] text-neutral-400 font-mono">{{ $adm->admission_no }}</div>
                                <div class="text-[10px] text-neutral-600 font-semibold">₺{{ number_format($adm->total_amount, 2) }}</div>
                                
                                <form method="POST" action="{{ route('admin.admission.status.update', $adm->id) }}" class="pt-2 border-t border-neutral-50 dark:border-neutral-800/60 mt-2">
                                    @csrf
                                    <select name="status" onchange="this.form.submit()" class="text-[10px] bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 rounded px-1.5 py-0.5 w-full">
                                        <option value="">Aşama Değiştir</option>
                                        @foreach($stages as $k => $v)
                                            <option value="{{ $k }}">{{ $v }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </div>
                        @empty
                            <div class="text-center text-[10px] text-neutral-400 py-6">Bu aşamada başvuru yok.</div>
                        @endforelse
                    </div>

                </div>
            @endforeach
        </div>

    </div>
@endsection
