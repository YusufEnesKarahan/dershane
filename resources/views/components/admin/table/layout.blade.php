<div class="overflow-hidden bg-white dark:bg-neutral-900 shadow-premium-sm ring-1 ring-neutral-200 dark:ring-neutral-800 sm:rounded-2xl">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-800">
            <thead class="bg-neutral-50 dark:bg-neutral-900/50">
                {{ $head }}
            </thead>
            <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800 bg-white dark:bg-neutral-900">
                {{ $body }}
            </tbody>
        </table>
    </div>
    @if(isset($pagination))
        <div class="border-t border-neutral-200 dark:border-neutral-800 px-4 py-3 sm:px-6">
            {{ $pagination }}
        </div>
    @endif
</div>