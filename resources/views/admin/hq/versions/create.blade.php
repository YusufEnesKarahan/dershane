@extends('layouts.admin')
@section('title', 'Publish Version')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-neutral-900 dark:text-white">Publish New Version</h1>
            <p class="text-xs text-neutral-500">Create a new software release for ERP instances.</p>
        </div>
        <a href="{{ route('admin.platform.hq_central.versions.index') }}" class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-neutral-700 dark:text-neutral-300 rounded-xl text-xs font-bold transition-colors">
            Cancel
        </a>
    </div>

    <form action="{{ route('admin.platform.hq_central.versions.store') }}" method="POST" class="bg-white dark:bg-neutral-900 p-8 rounded-3xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label class="block text-xs font-black text-neutral-700 dark:text-neutral-300 uppercase tracking-wider">Version String</label>
                <input type="text" name="version" required placeholder="e.g. 1.0.4" class="w-full bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-neutral-800 text-neutral-900 dark:text-white text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block p-3">
            </div>
            
            <div class="space-y-2">
                <label class="block text-xs font-black text-neutral-700 dark:text-neutral-300 uppercase tracking-wider">Channel</label>
                <select name="channel" class="w-full bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-neutral-800 text-neutral-900 dark:text-white text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block p-3">
                    <option value="stable">Stable</option>
                    <option value="beta">Beta</option>
                    <option value="alpha">Alpha</option>
                </select>
            </div>

            <div class="space-y-2">
                <label class="block text-xs font-black text-neutral-700 dark:text-neutral-300 uppercase tracking-wider">Min Supported Version</label>
                <input type="text" name="minimum_supported_version" placeholder="e.g. 1.0.0" class="w-full bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-neutral-800 text-neutral-900 dark:text-white text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block p-3">
            </div>

            <div class="space-y-2 flex flex-col justify-center pt-6">
                <label class="flex items-center cursor-pointer group">
                    <div class="relative flex items-center justify-center">
                        <input type="hidden" name="is_mandatory" value="0">
                        <input type="checkbox" name="is_mandatory" value="1" class="peer sr-only">
                        <div class="w-10 h-6 bg-neutral-200 dark:bg-neutral-700 rounded-full peer-checked:bg-red-500 transition-colors duration-300"></div>
                        <div class="absolute left-1 w-4 h-4 bg-white rounded-full transition-transform duration-300 peer-checked:translate-x-4"></div>
                    </div>
                    <span class="ml-3 text-sm font-bold text-neutral-700 dark:text-neutral-300 group-hover:text-neutral-900 dark:group-hover:text-white transition-colors">
                        Mandatory Update
                    </span>
                </label>
            </div>
        </div>

        <div class="space-y-2">
            <label class="block text-xs font-black text-neutral-700 dark:text-neutral-300 uppercase tracking-wider">Release Notes</label>
            <textarea name="release_notes" rows="5" class="w-full bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-neutral-800 text-neutral-900 dark:text-white text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block p-3" placeholder="What's new in this release?"></textarea>
        </div>

        <div class="pt-4 border-t border-neutral-100 dark:border-neutral-800 flex justify-end gap-3">
            <button type="submit" name="action" value="draft" class="px-6 py-3 bg-neutral-100 hover:bg-neutral-200 text-neutral-700 rounded-xl text-sm font-black transition-colors">
                Save as Draft
            </button>
            <button type="submit" name="action" value="publish" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-black transition-colors shadow-lg shadow-indigo-600/30">
                Publish Release
            </button>
        </div>
    </form>
</div>
@endsection
