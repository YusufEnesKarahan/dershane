@props([
    'title' => 'Kayıt Bulunamadı',
    'description' => 'Arama kriterlerinize uygun veya sistemde kayıtlı herhangi bir veri bulunamadı.',
    'icon' => 'M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z',
    'actionText' => null,
    'actionRoute' => null
])

<div class="flex flex-col items-center justify-center p-12 text-center bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm">
    <div class="p-4 bg-neutral-50 dark:bg-neutral-800/50 rounded-full mb-4">
        <svg class="w-8 h-8 text-neutral-400 dark:text-neutral-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" />
        </svg>
    </div>
    <h3 class="text-lg font-bold text-neutral-900 dark:text-white mb-1">{{ $title }}</h3>
    <p class="text-sm text-neutral-500 dark:text-neutral-400 max-w-sm mx-auto mb-6">{{ $description }}</p>
    
    @if($actionText && $actionRoute)
        <x-admin.button href="{{ $actionRoute }}" variant="primary" icon="M12 4v16m8-8H4">
            {{ $actionText }}
        </x-admin.button>
    @endif
</div>
