@extends('admin.layouts.app')

@section('title', 'Rollback Configuration')

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="flex items-center mb-6">
            <a href="{{ route('admin.platform.hq_central.configurations.history', $configuration) }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 mr-4">
                &larr; Back
            </a>
            <h2 class="text-2xl font-semibold text-red-600 dark:text-red-400">Rollback Profile: {{ $configuration->name }}</h2>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden border border-red-200 dark:border-red-900/50">
            <div class="p-6">
                <div class="mb-4 text-gray-700 dark:text-gray-300">
                    You are about to rollback this configuration profile to <strong>Version {{ $targetVersion->version }}</strong>.
                </div>
                <div class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700/50 rounded text-yellow-800 dark:text-yellow-400 text-sm">
                    <strong>Warning:</strong> This will delete all current configuration items and replace them with the exact state from Version {{ $targetVersion->version }}. A new version will automatically be created to track this rollback action in the history.
                </div>
                
                <form action="{{ route('admin.platform.hq_central.configurations.rollback', [$configuration, $targetVersion->version]) }}" method="POST">
                    @csrf
                    <div class="flex items-center justify-end space-x-4">
                        <a href="{{ route('admin.platform.hq_central.configurations.history', $configuration) }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-medium">Cancel</a>
                        <button type="submit" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded font-medium transition">
                            Confirm Rollback
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
