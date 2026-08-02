<div class="overflow-hidden bg-white dark:bg-neutral-900 shadow-sm ring-1 ring-neutral-200 dark:ring-neutral-800 rounded-2xl">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-800">
            <thead class="bg-neutral-50/80 dark:bg-neutral-900/80 backdrop-blur-sm">
                <tr>
                    {{ $head }}
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800/50 bg-white dark:bg-neutral-900">
                {{ $body }}
            </tbody>
        </table>
    </div>
    @if(isset($pagination))
        <div class="border-t border-neutral-200 dark:border-neutral-800 px-4 py-3 sm:px-6 bg-neutral-50/50 dark:bg-neutral-900/50">
            {{ $pagination }}
        </div>
    @endif
</div>