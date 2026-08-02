@if (session('success') || session('error') || session('warning') || session('info') || $errors->any())
    <div class="fixed top-4 right-4 z-50 flex flex-col gap-2 max-w-sm w-full pointer-events-none">
        
        @if (session('success'))
            <div class="pointer-events-auto shadow-lg">
                <x-alert type="success" :dismissible="true">
                    {{ session('success') }}
                </x-alert>
            </div>
        @endif

        @if (session('error'))
            <div class="pointer-events-auto shadow-lg">
                <x-alert type="danger" :dismissible="true">
                    {{ session('error') }}
                </x-alert>
            </div>
        @endif

        @if (session('warning'))
            <div class="pointer-events-auto shadow-lg">
                <x-alert type="warning" :dismissible="true">
                    {{ session('warning') }}
                </x-alert>
            </div>
        @endif

        @if (session('info'))
            <div class="pointer-events-auto shadow-lg">
                <x-alert type="info" :dismissible="true">
                    {{ session('info') }}
                </x-alert>
            </div>
        @endif

        @if ($errors->any())
            <div class="pointer-events-auto shadow-lg">
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
