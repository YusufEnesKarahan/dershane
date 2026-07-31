@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Notifications</h1>
            <p class="text-gray-600 mt-2">Stay updated on important system alerts and activities.</p>
        </div>
        <button class="text-sm text-blue-600 hover:underline">Mark all as read</button>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <ul class="divide-y divide-gray-200">
            @forelse($notifications ?? [] as $notification)
            <li class="{{ is_null($notification->read_at) ? 'bg-blue-50' : 'bg-white' }}">
                <a href="#" class="block hover:bg-gray-50">
                    <div class="px-4 py-4 sm:px-6">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-blue-600 truncate">
                                {{ $notification->title }}
                            </p>
                            <div class="ml-2 flex-shrink-0 flex">
                                @if($notification->type === 'critical')
                                    <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Critical</p>
                                @elseif($notification->type === 'warning')
                                    <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Warning</p>
                                @else
                                    <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Info</p>
                                @endif
                            </div>
                        </div>
                        <div class="mt-2 sm:flex sm:justify-between">
                            <div class="sm:flex">
                                <p class="flex items-center text-sm text-gray-500">
                                    {{ Str::limit($notification->message, 100) }}
                                </p>
                            </div>
                            <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                <p>
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    </div>
                </a>
            </li>
            @empty
            <li>
                <div class="px-4 py-8 text-center text-gray-500">
                    No notifications available.
                </div>
            </li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
