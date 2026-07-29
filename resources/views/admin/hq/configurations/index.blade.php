@extends('admin.layouts.app')

@section('title', 'HQ Configuration Management')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">Configuration Profiles</h2>
            <a href="{{ route('admin.platform.hq_central.configurations.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm transition">
                + New Profile
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <form action="{{ route('admin.platform.hq_central.configurations.index') }}" method="GET" class="flex items-center space-x-4">
                    <select name="scope" class="form-select rounded-md shadow-sm bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm">
                        <option value="">All Scopes</option>
                        <option value="global" {{ request('scope') == 'global' ? 'selected' : '' }}>Global</option>
                        <option value="tenant" {{ request('scope') == 'tenant' ? 'selected' : '' }}>Tenant</option>
                        <option value="instance" {{ request('scope') == 'instance' ? 'selected' : '' }}>Instance</option>
                    </select>
                    <button type="submit" class="px-3 py-1.5 bg-gray-600 text-white rounded-md text-sm hover:bg-gray-700 transition">Filter</button>
                    @if(request()->has('scope'))
                        <a href="{{ route('admin.platform.hq_central.configurations.index') }}" class="text-sm text-indigo-500 hover:underline">Clear Filter</a>
                    @endif
                </form>
            </div>
            
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 text-sm">
                        <th class="p-4 font-medium border-b dark:border-gray-700">Name</th>
                        <th class="p-4 font-medium border-b dark:border-gray-700">Scope</th>
                        <th class="p-4 font-medium border-b dark:border-gray-700">Target</th>
                        <th class="p-4 font-medium border-b dark:border-gray-700">Environment</th>
                        <th class="p-4 font-medium border-b dark:border-gray-700">Status</th>
                        <th class="p-4 font-medium border-b dark:border-gray-700 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-gray-700 dark:text-gray-300 divide-y dark:divide-gray-700">
                    @forelse($profiles as $profile)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                        <td class="p-4 font-medium">
                            <a href="{{ route('admin.platform.hq_central.configurations.show', $profile) }}" class="text-indigo-500 hover:underline">
                                {{ $profile->name }}
                            </a>
                        </td>
                        <td class="p-4 uppercase text-xs font-semibold">{{ $profile->scope }}</td>
                        <td class="p-4">
                            @if($profile->scope === 'global')
                                <span class="text-gray-400 text-xs">ALL</span>
                            @elseif($profile->scope === 'tenant')
                                {{ $profile->tenant->name ?? 'Unknown Tenant' }}
                            @elseif($profile->scope === 'instance')
                                {{ $profile->systemInstance->system_name ?? 'Unknown Instance' }}
                            @endif
                        </td>
                        <td class="p-4">{{ $profile->environment ?? 'Any' }}</td>
                        <td class="p-4">
                            <span class="px-2 py-1 bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 rounded-full text-xs font-medium">
                                {{ $profile->status }}
                            </span>
                        </td>
                        <td class="p-4 text-right">
                            <a href="{{ route('admin.platform.hq_central.configurations.history', $profile) }}" class="text-gray-500 hover:text-indigo-500 transition text-xs mr-3">
                                History
                            </a>
                            <a href="{{ route('admin.platform.hq_central.configurations.show', $profile) }}" class="px-3 py-1 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded text-xs transition">
                                Manage
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-500 dark:text-gray-400">
                            No configuration profiles found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            @if($profiles->hasPages())
                <div class="p-4 border-t dark:border-gray-700">
                    {{ $profiles->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
