@extends('layouts.admin')
@section('title', 'Deployments & Updates')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-neutral-900 dark:text-white">Active Deployments</h1>
            <p class="text-xs text-neutral-500">Monitor update jobs across the network.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="document.getElementById('deployModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition-colors shadow-lg shadow-indigo-600/30">
                Dispatch Update
            </button>
        </div>
    </div>

    <!-- Dispatch Update Modal -->
    <div id="deployModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-neutral-900/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-neutral-900 rounded-3xl w-full max-w-lg border border-neutral-100 dark:border-neutral-800 shadow-premium">
            <div class="p-6 border-b border-neutral-100 dark:border-neutral-800 flex justify-between items-center">
                <h3 class="text-lg font-black text-neutral-900 dark:text-white">Dispatch New Update</h3>
                <button onclick="document.getElementById('deployModal').classList.add('hidden')" class="text-neutral-500 hover:text-neutral-700 dark:hover:text-neutral-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form action="{{ route('admin.platform.hq_central.updates.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-black text-neutral-700 dark:text-neutral-300 uppercase tracking-wider mb-2">Target Version</label>
                    <select name="version_id" required class="w-full bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-neutral-800 text-neutral-900 dark:text-white text-sm rounded-xl p-3">
                        @foreach(\App\Models\HQVersion::where('status', 'published')->orderByDesc('version')->get() as $ver)
                            <option value="{{ $ver->id }}">{{ $ver->version }} ({{ $ver->channel }})</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-black text-neutral-700 dark:text-neutral-300 uppercase tracking-wider mb-2">Target Scope</label>
                    <select name="target_type" id="target_type" required onchange="toggleTargets()" class="w-full bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-neutral-800 text-neutral-900 dark:text-white text-sm rounded-xl p-3">
                        <option value="single">Single Instance</option>
                        <option value="tenant">All Instances in Tenant</option>
                        <option value="global">Global (All Production)</option>
                    </select>
                </div>

                <div id="instance_select">
                    <label class="block text-xs font-black text-neutral-700 dark:text-neutral-300 uppercase tracking-wider mb-2">Select Instance</label>
                    <select name="system_instance_id" class="w-full bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-neutral-800 text-neutral-900 dark:text-white text-sm rounded-xl p-3">
                        @foreach(\App\Models\HQSystemInstance::where('status', 'online')->get() as $inst)
                            <option value="{{ $inst->id }}">{{ $inst->system_name }} ({{ $inst->system_uuid }})</option>
                        @endforeach
                    </select>
                </div>

                <div id="tenant_select" class="hidden">
                    <label class="block text-xs font-black text-neutral-700 dark:text-neutral-300 uppercase tracking-wider mb-2">Select Tenant</label>
                    <select name="tenant_id" class="w-full bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-neutral-800 text-neutral-900 dark:text-white text-sm rounded-xl p-3">
                        @foreach(\App\Models\HQTenant::all() as $tnt)
                            <option value="{{ $tnt->id }}">{{ $tnt->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="pt-4 flex justify-end">
                    <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-black transition-colors">
                        Start Deployment
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="bg-white dark:bg-neutral-900 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-neutral-50 dark:bg-neutral-950 border-b border-neutral-100 dark:border-neutral-800 text-neutral-500">
                    <tr>
                        <th class="px-6 py-4 font-black text-xs uppercase tracking-wider">Version</th>
                        <th class="px-6 py-4 font-black text-xs uppercase tracking-wider">Target Scope</th>
                        <th class="px-6 py-4 font-black text-xs uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 font-black text-xs uppercase tracking-wider">Progress</th>
                        <th class="px-6 py-4 font-black text-xs uppercase tracking-wider">Dispatched At</th>
                        <th class="px-6 py-4 font-black text-xs uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    @forelse($jobs as $job)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors group">
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.platform.hq_central.versions.show', $job->version) }}" class="font-bold text-indigo-600 hover:text-indigo-800">{{ $job->version->version }}</a>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-bold text-neutral-900 dark:text-white uppercase text-xs">{{ $job->target_type }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider 
                                @if($job->status === 'completed') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                                @elseif($job->status === 'failed' || $job->status === 'cancelled') bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400
                                @elseif($job->status === 'pending' || $job->status === 'scheduled') bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400
                                @else bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 @endif">
                                {{ $job->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-full bg-neutral-200 dark:bg-neutral-700 rounded-full h-2 max-w-[100px]">
                                    <div class="h-2 rounded-full {{ $job->status === 'failed' ? 'bg-red-500' : 'bg-indigo-600' }}" style="width: {{ $job->progress }}%"></div>
                                </div>
                                <span class="text-xs font-bold">{{ $job->progress }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-xs font-bold text-neutral-500">
                            {{ $job->created_at->format('M d, Y H:i') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('admin.platform.hq_central.updates.show', $job) }}" class="p-2 bg-neutral-100 dark:bg-neutral-800 hover:bg-neutral-200 dark:hover:bg-neutral-700 rounded-lg text-neutral-600 dark:text-neutral-400 transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <p class="text-sm font-bold text-neutral-500">No update deployments found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($jobs->hasPages())
        <div class="p-4 border-t border-neutral-100 dark:border-neutral-800">
            {{ $jobs->links() }}
        </div>
        @endif
    </div>
</div>
<script>
function toggleTargets() {
    const val = document.getElementById('target_type').value;
    document.getElementById('instance_select').classList.toggle('hidden', val !== 'single');
    document.getElementById('tenant_select').classList.toggle('hidden', val !== 'tenant');
}
</script>
@endsection
