@props([
    'headers' => [],
    'stickyHeader' => false,
    'striped' => false,
    'pagination' => null,
])

<div class="w-full overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm bg-white dark:bg-slate-900">
    <table class="w-full border-collapse text-left text-sm font-sans text-slate-800 dark:text-slate-200">
        <thead class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 @if($stickyHeader) sticky top-0 z-10 backdrop-blur-sm @endif">
            <tr>
                @foreach ($headers as $header)
                    <th scope="col" class="px-6 py-3.5 text-xs font-semibold uppercase tracking-wider select-none">
                        {{ $header }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 @if($striped) [&>tr:nth-child(even)]:bg-slate-50/30 dark:[&>tr:nth-child(even)]:bg-slate-800/20 @endif [&>tr]:transition-colors [&>tr]:duration-200 [&>tr:hover]:bg-slate-50/50 dark:[&>tr:hover]:bg-white/[0.02]">
            {{ $slot }}
        </tbody>
    </table>

    @if ($pagination)
        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900">
            {{ $pagination }}
        </div>
    @endif
</div>
