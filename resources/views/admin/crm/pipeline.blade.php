@extends('layouts.admin')
@section('title', 'CRM Satış Pipeline (Kanban)')
@section('content')
    <div class="space-y-6">
        
        <!-- Üst Başlık -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm flex items-center justify-between">
            <div>
                <h1 class="text-lg font-bold text-neutral-900 dark:text-white">Satış Pipeline Board</h1>
                <p class="text-xs text-neutral-500 mt-1">Aday öğrencilerin aşama süreçlerini takip edin ve durumlarını güncelleyin.</p>
            </div>
        </div>

        <!-- Kanban Board Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 overflow-x-auto pb-4">
            @foreach($board as $column)
                <div class="bg-neutral-50 dark:bg-neutral-800/40 p-4 rounded-2xl border border-neutral-100 dark:border-neutral-800 space-y-4 min-w-[200px]">
                    
                    <!-- Sütun Başlığı -->
                    <div class="flex items-center justify-between border-b border-neutral-100 dark:border-neutral-800 pb-2">
                        <span class="text-xs font-bold text-neutral-800 dark:text-neutral-200">{{ $column['status']->name }}</span>
                        <span class="px-2 py-0.5 text-[10px] font-bold bg-neutral-200 dark:bg-neutral-800 rounded text-neutral-600 dark:text-neutral-400 font-mono">{{ $column['leads']->count() }}</span>
                    </div>

                    <!-- Leads Kartları -->
                    <div class="space-y-3">
                        @forelse($column['leads'] as $lead)
                            <div class="bg-white dark:bg-neutral-900 p-4 rounded-xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-2">
                                <div class="text-xs font-bold text-neutral-900 dark:text-white">
                                    <a href="{{ route('admin.leads.show', $lead->id) }}" class="hover:text-primary transition">
                                        {{ $lead->first_name }} {{ $lead->last_name }}
                                    </a>
                                </div>
                                <div class="text-[10px] text-neutral-400 mt-0.5 font-mono">{{ $lead->phone }}</div>
                                
                                @if($lead->advisor)
                                    <div class="text-[10px] text-indigo-500 font-semibold bg-indigo-50 px-2 py-0.5 rounded w-fit">
                                        {{ $lead->advisor->name }}
                                    </div>
                                @endif

                                <!-- Durum Değiştirme Formu (Drag/Drop Mocking) -->
                                <form method="POST" action="{{ route('admin.crm.pipeline.move') }}" class="pt-2 border-t border-neutral-50 dark:border-neutral-800/60 mt-2">
                                    @csrf
                                    <input type="hidden" name="lead_id" value="{{ $lead->id }}">
                                    <div class="flex gap-1">
                                        <select name="lead_status_id" onchange="this.form.submit()" class="text-[10px] bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 rounded px-1.5 py-0.5 w-full">
                                            <option value="">Aşama Değiştir</option>
                                            @foreach($board as $col)
                                                <option value="{{ $col['status']->id }}">{{ $col['status']->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </form>
                            </div>
                        @empty
                            <div class="text-center text-[10px] text-neutral-400 py-6">Bu aşamada aday bulunmamaktadır.</div>
                        @endforelse
                    </div>

                </div>
            @endforeach
        </div>

    </div>
@endsection
