@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Marketplace Extensions</h1>
        <p class="text-gray-600 mt-2">Discover and manage integrations installed on your workspace.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($extensions ?? [] as $installation)
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6 flex flex-col h-full">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">{{ $installation->extension->name ?? 'Unknown Extension' }}</h3>
                    <p class="text-sm text-gray-500">v{{ $installation->version->version ?? '1.0' }}</p>
                </div>
            </div>
            <p class="text-sm text-gray-600 flex-grow mb-6">
                {{ $installation->extension->description ?? 'Enhance your workspace with this powerful integration.' }}
            </p>
            <div class="flex justify-between items-center mt-auto border-t pt-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Installed
                </span>
                <button class="text-sm text-gray-600 hover:text-gray-900 font-medium">Configure</button>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="bg-white rounded-lg shadow p-8 text-center border-dashed border-2 border-gray-300">
                <h3 class="mt-2 text-sm font-medium text-gray-900">No Extensions Installed</h3>
                <p class="mt-1 text-sm text-gray-500">Visit the Marketplace to browse available integrations.</p>
                <div class="mt-6">
                    <button class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        Browse Marketplace
                    </button>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
