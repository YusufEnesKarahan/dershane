@extends('admin.layouts.app')

@section('title', 'Backup Policies')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.platform.hq_central.backups.index') }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    &larr; Dashboard
                </a>
                <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">Backup Policies</h2>
            </div>
            
            <a href="{{ route('admin.platform.hq_central.backups.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm transition">
                + New Policy
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 text-sm">
                        <th class="p-4 font-medium border-b dark:border-gray-700">Name</th>
                        <th class="p-4 font-medium border-b dark:border-gray-700">Type</th>
                        <th class="p-4 font-medium border-b dark:border-gray-700">Frequency</th>
                        <th class="p-4 font-medium border-b dark:border-gray-700">Target</th>
                        <th class="p-4 font-medium border-b dark:border-gray-700">Status</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y dark:divide-gray-700">
                    @forelse($policies as $policy)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                        <td class="p-4 font-medium text-gray-900 dark:text-gray-200">
                            {{ $policy->name }}
                        </td>
                        <td class="p-4 text-gray-600 dark:text-gray-400 uppercase text-xs font-semibold">
                            {{ $policy->backup_type }}
                        </td>
                        <td class="p-4 text-gray-600 dark:text-gray-400 capitalize">
                            {{ $policy->frequency }} (Retain {{ $policy->retention_days }}d)
                        </td>
                        <td class="p-4 text-gray-600 dark:text-gray-400">
                            @if($policy->systemInstance)
                                Instance: {{ $policy->systemInstance->system_name }}
                            @elseif($policy->tenant)
                                Tenant: {{ $policy->tenant->name }}
                            @else
                                Global
                            @endif
                        </td>
                        <td class="p-4">
                            @if($policy->is_active)
                                <span class="px-2 py-1 bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 rounded-full text-xs font-medium">Active</span>
                            @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 rounded-full text-xs font-medium">Disabled</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-gray-500">No policies found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            @if($policies->hasPages())
                <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $policies->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
