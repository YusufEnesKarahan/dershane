@extends('layouts.admin')

@section('title', 'Onboarding Flows')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Onboarding Flows</h1>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Step</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Started</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach(\App\Models\HQOnboardingFlow::orderBy('created_at', 'desc')->paginate(15) as $flow)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $flow->tenant ? $flow->tenant->name : 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $flow->current_step }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $flow->status === 'completed' ? 'green' : ($flow->status === 'in_progress' ? 'blue' : 'gray') }}-100 text-{{ $flow->status === 'completed' ? 'green' : ($flow->status === 'in_progress' ? 'blue' : 'gray') }}-800">
                            {{ $flow->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $flow->created_at->diffForHumans() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
