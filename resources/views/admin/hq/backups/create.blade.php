@extends('admin.layouts.app')

@section('title', 'Create Backup Policy')

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="flex items-center mb-6">
            <a href="{{ route('admin.platform.hq_central.backups.policies') }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 mr-4">
                &larr; Back
            </a>
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">Create Backup Policy</h2>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <form action="{{ route('admin.platform.hq_central.backups.store') }}" method="POST" class="p-6 space-y-6">
                @csrf
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Policy Name</label>
                    <input type="text" name="name" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm" required placeholder="e.g. Nightly Full Database Backup">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Target Tenant (Optional)</label>
                        <select name="tenant_id" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm">
                            <option value="">-- All Tenants / Global --</option>
                            @foreach($tenants as $tenant)
                                <option value="{{ $tenant->id }}">{{ $tenant->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Target Instance (Optional)</label>
                        <select name="system_instance_id" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm">
                            <option value="">-- All Instances / Global --</option>
                            @foreach($instances as $instance)
                                <option value="{{ $instance->id }}">{{ $instance->system_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Backup Type</label>
                        <select name="backup_type" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm">
                            <option value="database">Database Only</option>
                            <option value="files">Files Only</option>
                            <option value="full">Full (Database + Files)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Frequency</label>
                        <select name="frequency" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm">
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Retention (Days)</label>
                    <input type="number" name="retention_days" value="7" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm" required>
                    <p class="text-xs text-gray-500 mt-1">Backups older than this will be automatically deleted from the remote instance.</p>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" checked class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="is_active" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">Policy is Active</label>
                </div>

                <div class="flex justify-end pt-4 border-t dark:border-gray-700">
                    <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition font-medium">Create Policy</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
