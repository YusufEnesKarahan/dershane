@props(['title', 'value', 'icon' => 'chart-bar', 'trend' => null])
<div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
    <div class="flex items-center justify-between">
        <div class="text-neutral-500 dark:text-neutral-400 text-sm font-medium">{{ $title }}</div>
        <div class="p-2 bg-primary/10 text-primary rounded-lg shrink-0">
            <!-- Icon placeholder -->
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="12" r="10"/></svg>
        </div>
    </div>
    <div class="mt-4 flex items-baseline gap-2">
        <div class="text-3xl font-display font-bold text-neutral-900 dark:text-white">{{ $value }}</div>
        @if($trend)
            <span class="text-sm font-medium {{ $trend > 0 ? 'text-green-500' : 'text-red-500' }}">
                {{ $trend > 0 ? '+' : '' }}{{ $trend }}%
            </span>
        @endif
    </div>
</div>