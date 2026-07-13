@props([
    'headers' => [],
])

<div class="w-full overflow-x-auto rounded-premium-xl border border-neutral-100 shadow-premium-md bg-white">
    <table class="w-full border-collapse text-left text-sm font-sans text-neutral">
        <thead class="bg-neutral-50/70 border-b border-neutral-100 font-medium text-neutral/60">
            <tr>
                @foreach ($headers as $header)
                    <th scope="col" class="px-6 py-4 text-xs font-semibold uppercase tracking-wider select-none">
                        {{ $header }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-neutral-100/80">
            {{ $slot }}
        </tbody>
    </table>
</div>
