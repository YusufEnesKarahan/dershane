@if (session('success') || session('error') || session('warning') || session('info') || $errors->any())
    <div class="fixed top-4 right-4 z-50 flex flex-col gap-2 max-w-md w-full pointer-events-none p-2 sm:p-0">
        
        @if (session('success'))
            <div x-data="{ show: true }" 
                 x-init="setTimeout(() => show = false, 5000)" 
                 x-show="show" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-y-[-10px]"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform translate-y-[-10px]"
                 class="pointer-events-auto shadow-xl">
                <x-alert type="success" :dismissible="true">
                    {{ session('success') }}
                </x-alert>
            </div>
        @endif

        @if (session('error'))
            <div x-data="{ show: true }" 
                 x-init="setTimeout(() => show = false, 7000)" 
                 x-show="show" 
                 x-transition
                 class="pointer-events-auto shadow-xl">
                <x-alert type="danger" :dismissible="true">
                    {{ session('error') }}
                </x-alert>
            </div>
        @endif

        @if (session('warning'))
            <div x-data="{ show: true }" 
                 x-init="setTimeout(() => show = false, 6000)" 
                 x-show="show" 
                 x-transition
                 class="pointer-events-auto shadow-xl">
                <x-alert type="warning" :dismissible="true">
                    {{ session('warning') }}
                </x-alert>
            </div>
        @endif

        @if (session('info'))
            <div x-data="{ show: true }" 
                 x-init="setTimeout(() => show = false, 5000)" 
                 x-show="show" 
                 x-transition
                 class="pointer-events-auto shadow-xl">
                <x-alert type="info" :dismissible="true">
                    {{ session('info') }}
                </x-alert>
            </div>
        @endif

        @if ($errors->any())
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-transition
                 class="pointer-events-auto shadow-xl">
                <x-alert type="danger" :dismissible="true">
                    <ul class="list-disc pl-4 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-alert>
            </div>
        @endif

    </div>
@endif
