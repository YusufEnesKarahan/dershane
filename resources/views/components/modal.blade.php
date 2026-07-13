@props([
    'id',
    'title' => null,
])

<div x-data="{ open: false }" 
     x-show="open" 
     @open-modal.window="if ($event.detail.id === '{{ $id }}') open = true"
     @close-modal.window="if ($event.detail.id === '{{ $id }}') open = false"
     class="fixed z-10 inset-0 overflow-y-auto" 
     style="display: none;">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="open = false">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            @if ($title)
                <div class="mb-4">
                    <h3 class="text-lg font-medium text-gray-900">{{ $title }}</h3>
                </div>
            @endif

            <div class="mt-2">
                {{ $slot }}
            </div>

            <div class="mt-5 sm:mt-6 flex justify-end space-x-2">
                <button type="button" @click="open = false" class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:text-sm">
                    Kapat
                </button>
            </div>
        </div>
    </div>
</div>
