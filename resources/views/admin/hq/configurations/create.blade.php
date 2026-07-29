@extends('admin.layouts.app')

@section('title', 'Create Configuration Profile')

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="flex items-center mb-6">
            <a href="{{ route('admin.platform.hq_central.configurations.index') }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 mr-4">
                &larr; Back
            </a>
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">Create Configuration Profile</h2>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <form action="{{ route('admin.platform.hq_central.configurations.store') }}" method="POST" class="p-6 space-y-6">
                @csrf
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Profile Name</label>
                    <input type="text" name="name" class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-900 dark:text-gray-100" required placeholder="e.g. Production Core Database">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Scope</label>
                    <select name="scope" id="scope-selector" class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-900 dark:text-gray-100" required onchange="toggleScopeTargets()">
                        <option value="global">Global (All Systems)</option>
                        <option value="tenant">Tenant (Specific SaaS Client)</option>
                        <option value="instance">Instance (Specific ERP Node)</option>
                    </select>
                </div>

                <div id="tenant-selector" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Select Tenant</label>
                    <select name="tenant_id" class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
                        <option value="">-- Choose Tenant --</option>
                        @foreach($tenants as $tenant)
                            <option value="{{ $tenant->id }}">{{ $tenant->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="instance-selector" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Select Instance</label>
                    <select name="system_instance_id" class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
                        <option value="">-- Choose Instance --</option>
                        @foreach($instances as $instance)
                            <option value="{{ $instance->id }}">{{ $instance->system_name }} ({{ $instance->tenant->name ?? 'No Tenant' }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Environment (Optional)</label>
                    <input type="text" name="environment" class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-900 dark:text-gray-100" placeholder="e.g. production, staging">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                    <textarea name="description" rows="3" class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-900 dark:text-gray-100"></textarea>
                </div>

                <div class="flex justify-end pt-4 border-t dark:border-gray-700">
                    <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition font-medium">Create Profile</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleScopeTargets() {
    const scope = document.getElementById('scope-selector').value;
    const tenantSelector = document.getElementById('tenant-selector');
    const instanceSelector = document.getElementById('instance-selector');

    if (scope === 'tenant') {
        tenantSelector.classList.remove('hidden');
        instanceSelector.classList.add('hidden');
    } else if (scope === 'instance') {
        instanceSelector.classList.remove('hidden');
        tenantSelector.classList.add('hidden');
    } else {
        tenantSelector.classList.add('hidden');
        instanceSelector.classList.add('hidden');
    }
}
</script>
@endsection
