@extends('admin.layouts.app')

@section('title', 'Configuration History')

@section('content')
<div class="py-12">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <div class="flex items-center mb-6">
            <a href="{{ route('admin.platform.hq_central.configurations.show', $configuration) }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 mr-4">
                &larr; Back to Profile
            </a>
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">Version History: {{ $configuration->name }}</h2>
        </div>

        <div class="space-y-6">
            @forelse($versions as $version)
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Version {{ $version->version }}</h3>
                        <p class="text-xs text-gray-500 mt-1">Created on {{ $version->created_at->format('M d, Y H:i:s') }} by {{ $version->creator->name ?? 'System' }}</p>
                    </div>
                    <div>
                        @if($loop->first)
                            <span class="px-3 py-1 bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 rounded-full text-xs font-semibold">Current Active</span>
                        @else
                            <a href="{{ route('admin.platform.hq_central.configurations.rollback.form', [$configuration, $version->version]) }}" class="px-3 py-1.5 bg-yellow-100 text-yellow-800 hover:bg-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-500 dark:hover:bg-yellow-900/50 rounded text-xs font-medium transition">
                                Rollback to v{{ $version->version }}
                            </a>
                        @endif
                    </div>
                </div>
                
                @if($version->notes)
                <div class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-blue-50/50 dark:bg-blue-900/10">
                    <p class="text-sm text-gray-700 dark:text-gray-300 italic">"{{ $version->notes }}"</p>
                </div>
                @endif
                
                <div class="p-6">
                    <h4 class="text-sm font-medium text-gray-500 mb-3 uppercase tracking-wider">Snapshot State</h4>
                    <div class="bg-gray-900 rounded p-4 overflow-x-auto">
                        <pre class="text-xs text-green-400 font-mono">
@foreach($version->configuration_snapshot as $item)
"{{ $item['key'] }}": @if($item['is_sensitive'])"****************" @else"{{ Str::limit($item['value'], 100) }}" @endif <span class="text-gray-500">// {{ $item['type'] }}</span>
@endforeach
                        </pre>
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-8 text-center">
                <p class="text-gray-500 dark:text-gray-400">No versions captured yet.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
