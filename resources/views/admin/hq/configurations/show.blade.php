@extends('admin.layouts.app')

@section('title', 'Manage Configuration Profile')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.platform.hq_central.configurations.index') }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    &larr; Back
                </a>
                <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">
                    Profile: {{ $configuration->name }}
                </h2>
                <span class="px-2 py-1 bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 rounded text-xs uppercase font-bold">{{ $configuration->scope }}</span>
            </div>
            
            <div class="flex space-x-3">
                <a href="{{ route('admin.platform.hq_central.configurations.history', $configuration) }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm transition">
                    View History
                </a>
                <form action="{{ route('admin.platform.hq_central.configurations.version', $configuration) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm transition" onclick="return confirm('Create a new version snapshot?')">
                        Create Version Snapshot
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Items List -->
            <div class="md:col-span-2 bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="px-4 py-3 border-b dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-800/50">
                    <h3 class="font-medium text-gray-700 dark:text-gray-300">Configuration Items</h3>
                    <span class="text-xs text-gray-500">Current version: {{ $configuration->versions()->max('version') ?? 'None' }}</span>
                </div>
                
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-xs text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/30">
                            <th class="p-3 font-medium">Key</th>
                            <th class="p-3 font-medium">Value</th>
                            <th class="p-3 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y dark:divide-gray-700 text-sm">
                        @forelse($configuration->items as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                            <td class="p-3 font-mono text-xs text-gray-800 dark:text-gray-200">
                                {{ $item->key }}
                                @if($item->is_sensitive)
                                    <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">Sensitive</span>
                                @endif
                                <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">{{ $item->type }}</span>
                            </td>
                            <td class="p-3">
                                @if($item->is_sensitive)
                                    <span class="text-gray-400 italic">****************</span>
                                @else
                                    <span class="text-gray-600 dark:text-gray-400 font-mono text-xs">{{ Str::limit($item->value, 50) }}</span>
                                @endif
                            </td>
                            <td class="p-3 text-right">
                                <form action="{{ route('admin.platform.hq_central.configurations.items.destroy', [$configuration, $item->id]) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 text-xs" onclick="return confirm('Delete this item?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="p-6 text-center text-gray-500">No items configured yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Add Item Form -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg h-fit">
                <div class="px-4 py-3 border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <h3 class="font-medium text-gray-700 dark:text-gray-300">Add / Update Item</h3>
                </div>
                <form action="{{ route('admin.platform.hq_central.configurations.items.store', $configuration) }}" method="POST" class="p-4 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Key Name</label>
                        <input type="text" name="key" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm p-2" required placeholder="e.g. SMTP_PASSWORD">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Value</label>
                        <textarea name="value" rows="3" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm p-2" placeholder="Value..."></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                            <select name="type" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm p-2">
                                <option value="string">String</option>
                                <option value="integer">Integer</option>
                                <option value="boolean">Boolean</option>
                                <option value="json">JSON</option>
                                <option value="encrypted">Encrypted</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Sort Order</label>
                            <input type="number" name="sort_order" value="0" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm p-2">
                        </div>
                    </div>
                    <div class="flex items-center mt-2">
                        <input type="checkbox" name="is_sensitive" id="is_sensitive" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <label for="is_sensitive" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                            Is Sensitive (Mask in UI, Encrypt)
                        </label>
                    </div>
                    <button type="submit" class="w-full mt-4 py-2 bg-gray-800 dark:bg-gray-700 hover:bg-gray-900 dark:hover:bg-gray-600 text-white rounded transition text-sm font-medium">Save Item</button>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection
