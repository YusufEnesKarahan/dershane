@extends('layouts.admin')

@section('title', 'Onboarding & Provisioning Platform')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Onboarding & Provisioning</h1>
        <div class="space-x-2">
            <a href="{{ route('admin.hq.onboarding.flows') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Active Flows</a>
            <a href="{{ route('admin.hq.onboarding.provisioning') }}" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">Provisioning Tasks</a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-700">Active Flows</h3>
            <p class="text-3xl font-bold text-blue-600 mt-2">{{ \App\Models\HQOnboardingFlow::where('status', 'in_progress')->count() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-700">Pending Tasks</h3>
            <p class="text-3xl font-bold text-orange-600 mt-2">{{ \App\Models\HQProvisioningTask::where('status', 'pending')->count() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-700">Completed This Week</h3>
            <p class="text-3xl font-bold text-green-600 mt-2">{{ \App\Models\HQOnboardingFlow::where('status', 'completed')->where('completed_at', '>=', now()->startOfWeek())->count() }}</p>
        </div>
    </div>
</div>
@endsection
