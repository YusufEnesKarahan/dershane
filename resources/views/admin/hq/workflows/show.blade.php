@extends('layouts.admin')
@section('title', 'Workflow: ' . $workflow->name)

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-black text-neutral-900 dark:text-white tracking-tight">{{ $workflow->name }}</h1>
            <p class="text-sm font-bold text-neutral-500 mt-1">Trigger: <span class="text-indigo-600">{{ $workflow->trigger_event }}</span></p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.platform.hq_central.workflows.index') }}" class="px-4 py-2 bg-white dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300 font-bold text-sm rounded-xl border border-neutral-200 dark:border-neutral-700 hover:bg-neutral-50 dark:hover:bg-neutral-700 transition-colors">
                Back to Workflows
            </a>
            <!-- Simple manual trigger for demonstration if needed -->
            <button onclick="alert('Manual trigger feature in API')" class="px-4 py-2 bg-primary text-white font-bold text-sm rounded-xl shadow-premium-sm hover:bg-primary-600 transition-colors">
                Run Manually
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-neutral-900 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm p-6">
                <h3 class="text-lg font-black text-neutral-900 dark:text-white mb-4">Workflow Steps</h3>
                <div class="space-y-4 relative before:absolute before:inset-y-0 before:left-4 before:w-0.5 before:bg-neutral-200 dark:before:bg-neutral-800">
                    @foreach($workflow->steps as $index => $step)
                        <div class="relative flex gap-4">
                            <div class="h-8 w-8 rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-400 font-black text-sm flex items-center justify-center shrink-0 z-10">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1 bg-neutral-50 dark:bg-neutral-800/50 p-4 rounded-xl border border-neutral-200 dark:border-neutral-800">
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="font-black text-neutral-900 dark:text-white">{{ $step->name }}</h4>
                                    <span class="px-2 py-0.5 bg-neutral-200 dark:bg-neutral-700 text-neutral-700 dark:text-neutral-300 font-bold text-[10px] rounded uppercase">
                                        {{ $step->type }}
                                    </span>
                                </div>
                                <pre class="text-xs text-neutral-500 bg-neutral-100 dark:bg-neutral-900 p-2 rounded-lg overflow-x-auto"><code>{{ json_encode($step->config, JSON_PRETTY_PRINT) }}</code></pre>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white dark:bg-neutral-900 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm p-6">
                <h3 class="text-lg font-black text-neutral-900 dark:text-white mb-4">Recent Executions</h3>
                <div class="space-y-3">
                    @forelse($recentRuns as $run)
                        <div class="p-3 bg-neutral-50 dark:bg-neutral-800/50 rounded-xl border border-neutral-100 dark:border-neutral-800 flex justify-between items-center">
                            <div>
                                <div class="text-xs font-black text-neutral-900 dark:text-white">Run #{{ $run->id }}</div>
                                <div class="text-[10px] font-bold text-neutral-500">{{ $run->created_at->diffForHumans() }}</div>
                            </div>
                            <div>
                                @if($run->status === 'completed')
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
                                @elseif($run->status === 'failed')
                                    <span class="w-2 h-2 rounded-full bg-red-500 inline-block" title="{{ $run->error_message }}"></span>
                                @else
                                    <span class="w-2 h-2 rounded-full bg-amber-500 inline-block"></span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-sm font-bold text-neutral-500 text-center">No runs yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
