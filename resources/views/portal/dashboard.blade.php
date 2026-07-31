@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Customer Portal Dashboard</h1>
        <p class="text-gray-600 mt-2">Manage your subscription, extensions, and usage from one place.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Subscription Status -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Subscription</h3>
            <p class="text-2xl font-bold text-green-600">{{ $subscription['status'] ?? 'None' }}</p>
            <p class="text-sm text-gray-500 mt-1">Plan: {{ $subscription['plan_name'] ?? 'N/A' }}</p>
        </div>

        <!-- API Keys -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">API Keys</h3>
            <p class="text-2xl font-bold text-blue-600">{{ $apiKeysCount ?? 0 }}</p>
            <a href="{{ url('/portal/api-keys') }}" class="text-sm text-blue-500 hover:underline mt-1 block">Manage Keys &rarr;</a>
        </div>

        <!-- Open Support Tickets -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Support Tickets</h3>
            <p class="text-2xl font-bold text-orange-600">{{ $openTicketsCount ?? 0 }}</p>
            <a href="{{ url('/portal/support') }}" class="text-sm text-blue-500 hover:underline mt-1 block">View Tickets &rarr;</a>
        </div>

        <!-- Unread Notifications -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Notifications</h3>
            <p class="text-2xl font-bold text-red-600">{{ $unreadNotificationsCount ?? 0 }}</p>
            <a href="{{ url('/portal/notifications') }}" class="text-sm text-blue-500 hover:underline mt-1 block">View Inbox &rarr;</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Recent Activity</h3>
            </div>
            <ul class="divide-y divide-gray-200 p-4">
                @forelse($recent_activity ?? [] as $activity)
                    <li class="py-3">
                        <p class="text-sm font-medium text-gray-900">{{ $activity['action'] }}</p>
                        <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($activity['created_at'])->diffForHumans() }}</p>
                    </li>
                @empty
                    <li class="py-3 text-sm text-gray-500">No recent activity.</li>
                @endforelse
            </ul>
        </div>
        
        <!-- Entitlements -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Features & Entitlements</h3>
            </div>
            <ul class="divide-y divide-gray-200 p-4">
                @forelse($entitlements_summary ?? [] as $feature => $hasAccess)
                    <li class="py-3 flex justify-between items-center">
                        <span class="text-sm text-gray-700">{{ str_replace('_', ' ', Str::title($feature)) }}</span>
                        @if($hasAccess)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Enabled</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Locked</span>
                        @endif
                    </li>
                @empty
                    <li class="py-3 text-sm text-gray-500">No entitlements available.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
