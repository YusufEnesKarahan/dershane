@props(['title', 'description' => null])
<div class="space-y-6">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-display font-bold text-neutral-900 dark:text-white">{{ $title }}</h1>
            @if($description)
                <p class="mt-2 text-sm text-neutral-600 dark:text-neutral-400">{{ $description }}</p>
            @endif
        </div>
        <div class="mt-4 sm:mt-0 flex gap-3">
            {{ $actions ?? '' }}
        </div>
    </div>
    {{ $slot }}
</div>