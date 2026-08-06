@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900">Subscription & Billing</h1>
        <p class="text-slate-600 mt-2">Manage your current plan, billing cycles, and invoices.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
        <!-- Current Plan -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-slate-700 mb-4 border-b pb-2">Current Plan</h3>
            @if($subscription)
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <p class="text-2xl font-bold text-slate-900">{{ $subscription['plan_name'] }}</p>
                        <p class="text-sm text-slate-500">Status: <span class="font-semibold text-green-600">{{ ucfirst($subscription['status']) }}</span></p>
                    </div>
                    <button class="bg-blue-100 text-blue-700 px-4 py-2 rounded font-semibold hover:bg-blue-200">Upgrade</button>
                </div>
                <div class="text-sm text-slate-600">
                    <p>Started: {{ \Carbon\Carbon::parse($subscription['starts_at'])->format('M d, Y') }}</p>
                    <p>Renews/Expires: {{ $subscription['expires_at'] ? \Carbon\Carbon::parse($subscription['expires_at'])->format('M d, Y') : 'Auto-renewing' }}</p>
                </div>
            @else
                <p class="text-slate-500">You do not have an active subscription.</p>
                <button class="mt-4 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow">View Plans</button>
            @endif
        </div>
        
        <!-- Payment Methods (Placeholder) -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-slate-700 mb-4 border-b pb-2">Payment Method</h3>
            <div class="flex items-center space-x-4">
                <div class="bg-slate-100 p-3 rounded">
                    <svg class="w-8 h-8 text-slate-500" fill="currentColor" viewBox="0 0 24 24"><path d="M20 4H4C2.89 4 2.01 4.89 2.01 6L2 18C2 19.11 2.89 20 4 20H20C21.11 20 22 19.11 22 18V6C22 4.89 21.11 4 20 4ZM20 18H4V12H20V18ZM20 8H4V6H20V8Z"/></svg>
                </div>
                <div>
                    <p class="text-slate-900 font-medium">Visa ending in **** 4242</p>
                    <p class="text-sm text-slate-500">Expires 12/28</p>
                </div>
            </div>
            <button class="mt-4 text-sm text-blue-600 hover:underline">Update Payment Method</button>
        </div>
    </div>

    <!-- Invoices -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200">
            <h3 class="text-lg font-semibold text-slate-800">Billing History</h3>
        </div>
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Invoice ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Receipt</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-200">
                @forelse($invoices ?? [] as $invoice)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">#{{ substr($invoice->uuid, 0, 8) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $invoice->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">${{ number_format($invoice->amount, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($invoice->status === 'paid')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Paid</span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ ucfirst($invoice->status) }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="#" class="text-blue-600 hover:text-blue-900">Download</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-sm text-slate-500">No invoices found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
