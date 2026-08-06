@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900">Usage Metrics</h1>
        <p class="text-slate-600 mt-2">Monitor your resource consumption across the platform.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @forelse($usage ?? [] as $record)
        <div class="bg-white rounded-lg shadow p-6 border-t-4 border-blue-500">
            <h3 class="text-lg font-semibold text-slate-700 mb-2">{{ ucfirst(str_replace('_', ' ', $record->metric_name)) }}</h3>
            <div class="flex items-end space-x-2">
                <span class="text-3xl font-bold text-slate-900">{{ $record->value }}</span>
                <span class="text-sm text-slate-500 mb-1">units used</span>
            </div>
            
            <div class="mt-4 w-full bg-slate-200 rounded-full h-2.5">
                <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ min(100, ($record->value / max(1, $record->limit ?? 100)) * 100) }}%"></div>
            </div>
            <p class="text-xs text-slate-500 mt-2">Period: {{ $record->period }}</p>
        </div>
        @empty
        <div class="col-span-full">
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-slate-900">No Usage Data</h3>
                <p class="mt-1 text-sm text-slate-500">Usage metrics will appear here once you start consuming resources.</p>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
