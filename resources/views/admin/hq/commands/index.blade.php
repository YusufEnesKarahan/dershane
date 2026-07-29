@extends('layouts.admin')
@section('title', 'HQ Remote Commands')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-neutral-900 dark:text-white">Remote Commands</h1>
            <p class="text-xs text-neutral-500">Orchestrate and monitor commands sent to connected ERPs.</p>
        </div>
        <a href="{{ route('admin.hq.commands.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition-colors">
            Dispatch Command
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <h3 class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-3">Pending</h3>
            <p class="text-xl font-black text-amber-600">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <h3 class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-3">Completed</h3>
            <p class="text-xl font-black text-green-600">{{ $stats['completed'] }}</p>
        </div>
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <h3 class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-3">Failed</h3>
            <p class="text-xl font-black text-red-600">{{ $stats['failed'] }}</p>
        </div>
    </div>

    <!-- List -->
    <div class="bg-white dark:bg-neutral-900 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-neutral-50 dark:bg-neutral-800/50">
                        <th class="py-4 px-6 text-[10px] font-black text-neutral-500 uppercase tracking-wider">ID</th>
                        <th class="py-4 px-6 text-[10px] font-black text-neutral-500 uppercase tracking-wider">Target</th>
                        <th class="py-4 px-6 text-[10px] font-black text-neutral-500 uppercase tracking-wider">Command</th>
                        <th class="py-4 px-6 text-[10px] font-black text-neutral-500 uppercase tracking-wider">Status</th>
                        <th class="py-4 px-6 text-[10px] font-black text-neutral-500 uppercase tracking-wider">Date</th>
                        <th class="py-4 px-6 text-[10px] font-black text-neutral-500 uppercase tracking-wider text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    @forelse($commands as $command)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                            <td class="py-4 px-6 text-xs text-neutral-900 dark:text-neutral-300">#{{ $command->id }}</td>
                            <td class="py-4 px-6 text-xs text-neutral-900 dark:text-neutral-300">
                                @if($command->instance)
                                    <span class="font-bold">{{ $command->instance->system_name }}</span>
                                    <span class="block text-[10px] text-neutral-500">{{ $command->instance->tenant->name ?? 'No Tenant' }}</span>
                                @else
                                    <span class="text-neutral-400">Unknown Instance</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-xs font-mono font-bold text-neutral-700 dark:text-neutral-400">
                                {{ $command->command_type }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2 py-1 text-[10px] font-black rounded-lg uppercase tracking-wider
                                    {{ $command->status === 'completed' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : '' }}
                                    {{ $command->status === 'failed' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : '' }}
                                    {{ $command->status === 'pending' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' : '' }}
                                    {{ !in_array($command->status, ['completed', 'failed', 'pending']) ? 'bg-neutral-100 text-neutral-700 dark:bg-neutral-800 dark:text-neutral-400' : '' }}
                                ">
                                    {{ $command->status }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-[10px] text-neutral-500">
                                {{ $command->created_at->format('Y-m-d H:i') }}
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('admin.hq.commands.show', $command) }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 transition-colors">Details</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-xs text-neutral-500">No remote commands found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-neutral-100 dark:border-neutral-800">
            {{ $commands->links() }}
        </div>
    </div>
</div>
@endsection
