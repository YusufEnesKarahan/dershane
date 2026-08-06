<div class="overflow-hidden bg-white dark:bg-slate-900 shadow-sm ring-1 ring-slate-200 dark:ring-slate-800 rounded-xl">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
            <thead class="bg-slate-50/80 dark:bg-slate-800/50">
                <tr>
                    {{ $head }}
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50 bg-white dark:bg-slate-900">
                {{ $body }}
            </tbody>
        </table>
    </div>
    @if(isset($pagination))
        <div class="border-t border-slate-200 dark:border-slate-800 px-4 py-3 sm:px-6 bg-slate-50/50 dark:bg-slate-800/30">
            {{ $pagination }}
        </div>
    @endif
</div>