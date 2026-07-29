@extends('layouts.admin')
@section('title', 'License Management')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-neutral-900 dark:text-white">License Management</h1>
            <p class="text-xs text-neutral-500">Manage tenant licenses and active plans</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.platform.hq_central.index') }}" class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-neutral-700 dark:text-neutral-300 rounded-xl text-xs font-bold transition-colors">
                Dashboard
            </a>
            <button onclick="document.getElementById('createLicenseModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition-colors">
                + New License
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-4 rounded-xl text-sm font-bold border border-green-100">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-neutral-100 dark:border-neutral-800 flex gap-4">
            <a href="{{ route('admin.platform.hq_central.licenses.index') }}" class="text-xs font-bold {{ !request()->has('status') ? 'text-indigo-600' : 'text-neutral-500 hover:text-neutral-700' }}">All</a>
            <a href="{{ route('admin.platform.hq_central.licenses.index', ['status' => 'active']) }}" class="text-xs font-bold {{ request('status') == 'active' ? 'text-indigo-600' : 'text-neutral-500 hover:text-neutral-700' }}">Active</a>
            <a href="{{ route('admin.platform.hq_central.licenses.index', ['status' => 'expired']) }}" class="text-xs font-bold {{ request('status') == 'expired' ? 'text-indigo-600' : 'text-neutral-500 hover:text-neutral-700' }}">Expired</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-neutral-600 dark:text-neutral-400">
                <thead class="bg-neutral-50 dark:bg-neutral-800/50 text-neutral-500 font-bold uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="px-6 py-4">License Key</th>
                        <th class="px-6 py-4">Tenant</th>
                        <th class="px-6 py-4">Plan</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Expires At</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    @forelse($licenses as $license)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                            <td class="px-6 py-4 font-mono text-xs font-bold text-indigo-600">
                                {{ $license->license_key }}
                            </td>
                            <td class="px-6 py-4 font-bold text-neutral-900 dark:text-white">
                                {{ $license->tenant->name ?? 'Unknown' }}
                            </td>
                            <td class="px-6 py-4 text-xs font-bold text-neutral-700 dark:text-neutral-300">
                                {{ $license->plan }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded text-[10px] font-bold uppercase 
                                    @if($license->status === 'active') bg-green-100 text-green-700 
                                    @elseif($license->status === 'expired') bg-red-100 text-red-700 
                                    @elseif($license->status === 'suspended') bg-amber-100 text-amber-700
                                    @else bg-neutral-100 text-neutral-700 @endif">
                                    {{ $license->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center font-mono text-xs {{ $license->isExpired() ? 'text-red-500' : 'text-neutral-500' }}">
                                {{ $license->expires_at ? $license->expires_at->format('Y-m-d') : 'Lifetime' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.platform.hq_central.licenses.show', $license->id) }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors">
                                    Manage
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-neutral-500 font-bold">
                                No licenses found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-neutral-100 dark:border-neutral-800">
            {{ $licenses->links() }}
        </div>
    </div>
</div>

<!-- Create Modal -->
<div id="createLicenseModal" class="hidden fixed inset-0 bg-neutral-900/50 z-50 flex items-center justify-center backdrop-blur-sm">
    <div class="bg-white dark:bg-neutral-900 rounded-3xl p-6 w-full max-w-md shadow-2xl border border-neutral-100 dark:border-neutral-800">
        <h3 class="text-lg font-black text-neutral-900 dark:text-white mb-4">Add New License</h3>
        <form action="{{ route('admin.platform.hq_central.licenses.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-1">Tenant</label>
                    <select name="tenant_id" required class="w-full bg-neutral-50 dark:bg-neutral-800 border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 text-neutral-900 dark:text-white">
                        @foreach($tenants as $t)
                            <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->slug }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-1">Plan Name</label>
                    <input type="text" name="plan" placeholder="e.g. Enterprise, Standard" required class="w-full bg-neutral-50 dark:bg-neutral-800 border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 text-neutral-900 dark:text-white">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-1">Start Date</label>
                        <input type="date" name="starts_at" class="w-full bg-neutral-50 dark:bg-neutral-800 border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 text-neutral-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-1">Expiration Date</label>
                        <input type="date" name="expires_at" class="w-full bg-neutral-50 dark:bg-neutral-800 border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 text-neutral-900 dark:text-white">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-1">Status</label>
                    <select name="status" class="w-full bg-neutral-50 dark:bg-neutral-800 border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 text-neutral-900 dark:text-white">
                        <option value="active">Active</option>
                        <option value="pending">Pending</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('createLicenseModal').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-neutral-500 hover:text-neutral-700">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition-colors">Create License</button>
            </div>
        </form>
    </div>
</div>
@endsection
