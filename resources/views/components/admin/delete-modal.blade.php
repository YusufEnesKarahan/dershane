@props(['action', 'title' => 'Silme Onayı', 'message' => 'Bu kaydı silmek istediğinize emin misiniz? Bu işlem geri alınamaz.'])

<div x-data="{ open: false }" class="inline-block">
    <div @click="open = true">
        {{ $trigger ?? '' }}
        @if(!isset($trigger))
            <button type="button" class="text-rose-600 hover:text-rose-900 dark:text-rose-500 dark:hover:text-rose-400 font-medium text-sm focus:outline-none">
                Sil
            </button>
        @endif
    </div>

    <!-- Modal Background -->
    <div x-show="open" 
         style="display: none;"
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true">
         
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-neutral-900/75 backdrop-blur-sm transition-opacity" aria-hidden="true" @click="open = false"></div>

            <!-- This element is to trick the browser into centering the modal contents. -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal Panel -->
            <div x-show="open" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="inline-block align-bottom bg-white dark:bg-neutral-900 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-neutral-100 dark:border-neutral-800">
                 
                <div class="bg-white dark:bg-neutral-900 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-rose-100 dark:bg-rose-900/30 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-rose-600 dark:text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-neutral-900 dark:text-white" id="modal-title">
                                {{ $title }}
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-neutral-500 dark:text-neutral-400">
                                    {{ $message }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-neutral-50 dark:bg-neutral-800/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-3">
                    <form method="POST" action="{{ $action }}">
                        @csrf
                        @method('DELETE')
                        <x-admin.button type="submit" variant="danger" class="w-full sm:w-auto">
                            Evet, Sil
                        </x-admin.button>
                    </form>
                    <x-admin.button type="button" variant="secondary" @click="open = false" class="w-full sm:w-auto mt-3 sm:mt-0">
                        İptal
                    </x-admin.button>
                </div>
            </div>
        </div>
    </div>
</div>
