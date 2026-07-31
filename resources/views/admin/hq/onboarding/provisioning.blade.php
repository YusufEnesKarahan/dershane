@extends('layouts.admin')

@section('title', 'Provisioning Tasks')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Provisioning Tasks</h1>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completed At</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach(\App\Models\HQProvisioningTask::orderBy('created_at', 'desc')->paginate(15) as $task)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $task->tenant ? $task->tenant->name : 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $task->task_type }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $task->status === 'completed' ? 'green' : ($task->status === 'failed' ? 'red' : 'yellow') }}-100 text-{{ $task->status === 'completed' ? 'green' : ($task->status === 'failed' ? 'red' : 'yellow') }}-800">
                            {{ $task->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $task->completed_at ? $task->completed_at->diffForHumans() : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
