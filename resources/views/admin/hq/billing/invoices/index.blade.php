@extends('layouts.admin')
@section('title', 'Invoices')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-black text-neutral-900 dark:text-white tracking-tight">Invoices</h1>
            <p class="text-sm font-bold text-neutral-500 mt-1">Manage tenant billing invoices.</p>
        </div>
    </div>

    <div class="bg-white dark:bg-neutral-900 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm overflow-hidden">
        <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-800">
            <thead class="bg-neutral-50 dark:bg-neutral-800/50">
                <tr>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-black text-neutral-500 uppercase tracking-wider">Invoice No</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-black text-neutral-500 uppercase tracking-wider">Tenant</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-black text-neutral-500 uppercase tracking-wider">Amount</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-black text-neutral-500 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-black text-neutral-500 uppercase tracking-wider">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800">
                @forelse($invoices as $invoice)
                <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-bold text-neutral-900 dark:text-white font-mono">{{ $invoice->invoice_number }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-bold text-neutral-900 dark:text-white">{{ $invoice->tenant->name ?? 'Unknown' }}</div>
                        <div class="text-[10px] font-bold text-neutral-500">{{ $invoice->subscription->plan->name ?? '' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm font-bold text-neutral-900 dark:text-white">{{ $invoice->amount }} {{ $invoice->currency }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 inline-flex text-[10px] leading-4 font-bold rounded-full 
                            @if($invoice->status === 'paid') bg-green-100 text-green-800
                            @elseif($invoice->status === 'failed') bg-red-100 text-red-800
                            @elseif($invoice->status === 'pending') bg-amber-100 text-amber-800
                            @else bg-neutral-100 text-neutral-800 @endif capitalize">
                            {{ $invoice->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 font-bold">
                        {{ $invoice->issued_at?->format('M d, Y') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-sm font-bold text-neutral-500">
                        No invoices found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($invoices->hasPages())
        <div class="px-6 py-4 border-t border-neutral-200 dark:border-neutral-800">
            {{ $invoices->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
