@extends('layouts.admin')
@section('title', 'Tenant Management')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-neutral-900 dark:text-white">Tenant Management</h1>
            <p class="text-xs text-neutral-500">Manage connected SaaS organizations</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.platform.hq_central.index') }}" class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-neutral-700 dark:text-neutral-300 rounded-xl text-xs font-bold transition-colors">
                Dashboard
            </a>
            <button onclick="document.getElementById('createTenantModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition-colors">
                + New Tenant
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-4 rounded-xl text-sm font-bold border border-green-100">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-neutral-600 dark:text-neutral-400">
                <thead class="bg-neutral-50 dark:bg-neutral-800/50 text-neutral-500 font-bold uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Name</th>
                        <th class="px-6 py-4">Slug</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Connected Systems</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    @forelse($tenants as $tenant)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-neutral-900 dark:text-white">
                                {{ $tenant->name }}
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-neutral-500">
                                {{ $tenant->slug }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded text-[10px] font-bold uppercase 
                                    @if($tenant->status === 'active') bg-green-100 text-green-700 
                                    @elseif($tenant->status === 'suspended') bg-red-100 text-red-700 
                                    @else bg-neutral-100 text-neutral-700 @endif">
                                    {{ $tenant->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center font-black text-indigo-600">
                                {{ $tenant->instances_count }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button onclick="editTenant({{ $tenant->toJson() }})" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors">
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-neutral-500 font-bold">
                                No tenants found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-neutral-100 dark:border-neutral-800">
            {{ $tenants->links() }}
        </div>
    </div>
</div>

<!-- Create Modal -->
<div id="createTenantModal" class="hidden fixed inset-0 bg-neutral-900/50 z-50 flex items-center justify-center backdrop-blur-sm">
    <div class="bg-white dark:bg-neutral-900 rounded-3xl p-6 w-full max-w-md shadow-2xl border border-neutral-100 dark:border-neutral-800">
        <h3 class="text-lg font-black text-neutral-900 dark:text-white mb-4">Add New Tenant</h3>
        <form action="{{ route('admin.platform.hq_central.tenants.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-1">Tenant Name</label>
                    <input type="text" name="name" required class="w-full bg-neutral-50 dark:bg-neutral-800 border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 text-neutral-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-1">Slug (Unique identifier)</label>
                    <input type="text" name="slug" required class="w-full bg-neutral-50 dark:bg-neutral-800 border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 text-neutral-900 dark:text-white font-mono">
                </div>
                <div>
                    <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-1">Status</label>
                    <select name="status" class="w-full bg-neutral-50 dark:bg-neutral-800 border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 text-neutral-900 dark:text-white">
                        <option value="active">Active</option>
                        <option value="suspended">Suspended</option>
                        <option value="disabled">Disabled</option>
                    </select>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('createTenantModal').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-neutral-500 hover:text-neutral-700">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition-colors">Create Tenant</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editTenantModal" class="hidden fixed inset-0 bg-neutral-900/50 z-50 flex items-center justify-center backdrop-blur-sm">
    <div class="bg-white dark:bg-neutral-900 rounded-3xl p-6 w-full max-w-md shadow-2xl border border-neutral-100 dark:border-neutral-800">
        <h3 class="text-lg font-black text-neutral-900 dark:text-white mb-4">Edit Tenant</h3>
        <form id="editTenantForm" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-1">Tenant Name</label>
                    <input type="text" name="name" id="edit_name" required class="w-full bg-neutral-50 dark:bg-neutral-800 border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 text-neutral-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-1">Slug (Unique identifier)</label>
                    <input type="text" name="slug" id="edit_slug" required class="w-full bg-neutral-50 dark:bg-neutral-800 border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 text-neutral-900 dark:text-white font-mono">
                </div>
                <div>
                    <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-1">Status</label>
                    <select name="status" id="edit_status" class="w-full bg-neutral-50 dark:bg-neutral-800 border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 text-neutral-900 dark:text-white">
                        <option value="active">Active</option>
                        <option value="suspended">Suspended</option>
                        <option value="disabled">Disabled</option>
                    </select>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('editTenantModal').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-neutral-500 hover:text-neutral-700">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition-colors">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function editTenant(tenant) {
    document.getElementById('edit_name').value = tenant.name;
    document.getElementById('edit_slug').value = tenant.slug;
    document.getElementById('edit_status').value = tenant.status;
    document.getElementById('editTenantForm').action = '/admin/platform/hq-central/tenants/' + tenant.id;
    document.getElementById('editTenantModal').classList.remove('hidden');
}
</script>
@endsection
