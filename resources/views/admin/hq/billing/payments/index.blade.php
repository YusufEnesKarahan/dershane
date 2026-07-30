@extends('layouts.admin')
@section('title', 'Payments')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-black text-neutral-900 dark:text-white tracking-tight">Payment History</h1>
            <p class="text-sm font-bold text-neutral-500 mt-1">Monitor subscription payments across the platform.</p>
        </div>
    </div>

    <div class="bg-white dark:bg-neutral-900 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm overflow-hidden">
        <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-800">
            <thead class="bg-neutral-50 dark:bg-neutral-800/50">
                <tr>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-black text-neutral-500 uppercase tracking-wider">Transaction ID</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-black text-neutral-500 uppercase tracking-wider">Invoice</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-black text-neutral-500 uppercase tracking-wider">Provider</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-black text-neutral-500 uppercase tracking-wider">Amount</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-black text-neutral-500 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-black text-neutral-500 uppercase tracking-wider">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800">
                @forelse($payments as $payment)
                <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-bold text-neutral-900 dark:text-white font-mono">{{ $payment->transaction_id ?? 'N/A' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-bold text-neutral-900 dark:text-white font-mono">{{ $payment->invoice->invoice_number ?? 'Unknown' }}</div>
                        <div class="text-[10px] font-bold text-neutral-500">{{ $payment->invoice->tenant->name ?? '' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 inline-flex text-[10px] leading-4 font-bold rounded-full bg-slate-100 text-slate-800 uppercase">
                            {{ $payment->provider }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm font-bold text-neutral-900 dark:text-white">{{ $payment->amount }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 inline-flex text-[10px] leading-4 font-bold rounded-full 
                            @if($payment->status === 'successful') bg-green-100 text-green-800
                            @elseif($payment->status === 'failed') bg-red-100 text-red-800
                            @else bg-neutral-100 text-neutral-800 @endif capitalize">
                            {{ $payment->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 font-bold">
                        {{ $payment->paid_at?->format('M d, Y H:i') ?? 'N/A' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-sm font-bold text-neutral-500">
                        No payments found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($payments->hasPages())
        <div class="px-6 py-4 border-t border-neutral-200 dark:border-neutral-800">
            {{ $payments->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
