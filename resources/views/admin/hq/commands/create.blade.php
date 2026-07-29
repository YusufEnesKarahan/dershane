@extends('layouts.admin')
@section('title', 'Dispatch Command')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-neutral-900 dark:text-white">Dispatch Command</h1>
            <p class="text-xs text-neutral-500">Send a remote command to connected ERPs.</p>
        </div>
        <a href="{{ route('admin.hq.commands.index') }}" class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-neutral-700 dark:text-neutral-300 rounded-xl text-xs font-bold transition-colors">
            Cancel
        </a>
    </div>

    <form action="{{ route('admin.hq.commands.store') }}" method="POST" class="bg-white dark:bg-neutral-900 p-8 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-6">
        @csrf

        <div class="space-y-4">
            <label class="block text-xs font-black text-neutral-700 dark:text-neutral-300 uppercase tracking-wider">Target Type</label>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <label class="flex items-center p-4 border border-neutral-200 dark:border-neutral-800 rounded-xl cursor-pointer hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                    <input type="radio" name="target_type" value="instance" class="text-indigo-600 focus:ring-indigo-500" checked onchange="toggleTargetFields()">
                    <span class="ml-3 text-sm font-bold text-neutral-900 dark:text-white">Single Instance</span>
                </label>
                <label class="flex items-center p-4 border border-neutral-200 dark:border-neutral-800 rounded-xl cursor-pointer hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                    <input type="radio" name="target_type" value="tenant" class="text-indigo-600 focus:ring-indigo-500" onchange="toggleTargetFields()">
                    <span class="ml-3 text-sm font-bold text-neutral-900 dark:text-white">All in Tenant</span>
                </label>
                <label class="flex items-center p-4 border border-neutral-200 dark:border-neutral-800 rounded-xl cursor-pointer hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                    <input type="radio" name="target_type" value="all" class="text-indigo-600 focus:ring-indigo-500" onchange="toggleTargetFields()">
                    <span class="ml-3 text-sm font-bold text-neutral-900 dark:text-white">All Instances</span>
                </label>
            </div>
        </div>

        <div id="target_id_container" class="space-y-2">
            <label class="block text-xs font-black text-neutral-700 dark:text-neutral-300 uppercase tracking-wider">Target ID</label>
            <select name="target_id" id="target_id" class="w-full bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-neutral-800 text-neutral-900 dark:text-white text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block p-3">
                <optgroup label="Instances" id="opt_instances">
                    @foreach($instances as $instance)
                        <option value="{{ $instance->id }}">{{ $instance->system_name }} ({{ $instance->tenant->name ?? 'No Tenant' }})</option>
                    @endforeach
                </optgroup>
                <optgroup label="Tenants" id="opt_tenants" class="hidden">
                    @foreach($tenants as $tenant)
                        <option value="{{ $tenant->id }}">{{ $tenant->name }}</option>
                    @endforeach
                </optgroup>
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label class="block text-xs font-black text-neutral-700 dark:text-neutral-300 uppercase tracking-wider">Command Type</label>
                <select name="command_type" class="w-full bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-neutral-800 text-neutral-900 dark:text-white text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block p-3">
                    @foreach($commandTypes as $type)
                        <option value="{{ $type->value }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="space-y-2">
                <label class="block text-xs font-black text-neutral-700 dark:text-neutral-300 uppercase tracking-wider">Priority (Higher runs first)</label>
                <input type="number" name="priority" value="0" class="w-full bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-neutral-800 text-neutral-900 dark:text-white text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block p-3">
            </div>
        </div>

        <div class="space-y-2">
            <label class="block text-xs font-black text-neutral-700 dark:text-neutral-300 uppercase tracking-wider">JSON Payload (Optional)</label>
            <textarea name="payload_json" rows="4" class="w-full bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-neutral-800 text-neutral-900 dark:text-white text-sm font-mono rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block p-3" placeholder="{}"></textarea>
            <p class="text-[10px] text-neutral-500">Must be valid JSON if provided.</p>
        </div>

        <div class="pt-4 border-t border-neutral-100 dark:border-neutral-800 flex justify-end">
            <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-black transition-colors shadow-lg shadow-indigo-600/30">
                Dispatch Command(s)
            </button>
        </div>
    </form>
</div>

<script>
function toggleTargetFields() {
    const type = document.querySelector('input[name="target_type"]:checked').value;
    const container = document.getElementById('target_id_container');
    const optInstances = document.getElementById('opt_instances');
    const optTenants = document.getElementById('opt_tenants');
    
    if (type === 'all') {
        container.classList.add('hidden');
    } else {
        container.classList.remove('hidden');
        if (type === 'instance') {
            optInstances.classList.remove('hidden');
            optTenants.classList.add('hidden');
        } else {
            optInstances.classList.add('hidden');
            optTenants.classList.remove('hidden');
        }
    }
}
</script>
@endsection
