@props([
    'headers' => [],
    'stickyHeader' => false,
    'striped' => false,
    'pagination' => null,
])

<div class="w-full overflow-x-auto rounded-xl border border-neutral-200 dark:border-neutral-800 shadow-sm bg-white dark:bg-neutral-900">
    <table class="w-full border-collapse text-left text-sm font-sans text-neutral-800 dark:text-neutral-200">
        <thead class="bg-neutral-50 dark:bg-neutral-800/80 border-b border-neutral-200 dark:border-neutral-800 text-neutral-600 dark:text-neutral-400 @if($stickyHeader) sticky top-0 z-10 backdrop-blur-sm @endif">
            <tr>
                @foreach ($headers as $header)
                    <th scope="col" class="px-6 py-3.5 text-xs font-semibold uppercase tracking-wider select-none">
                        {{ $header }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800 @if($striped) [&>tr:nth-child(even)]:bg-neutral-50/50 dark:[&>tr:nth-child(even)]:bg-neutral-850/30 @endif [&>tr]:transition-colors [&>tr]:duration-150 [&>tr:hover]:bg-neutral-50/80 dark:[&>tr:hover]:bg-neutral-800/60">
            {{ $slot }}
        </tbody>
    </table>

    @if ($pagination)
        <div class="px-6 py-4 border-t border-neutral-200 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-900">
            {{ $pagination }}
        </div>
    @endif
</div>
