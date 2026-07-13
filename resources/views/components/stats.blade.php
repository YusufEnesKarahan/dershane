@props([
    'items' => [], // array of key => value (value can be number, key can be text)
])

<div {{ $attributes->merge(['class' => 'grid grid-cols-2 md:grid-cols-4 gap-6 text-center py-8 bg-white border border-neutral-100 rounded-premium-2xl shadow-premium-md overflow-hidden']) }}>
    @foreach ($items as $label => $val)
        <div class="space-y-1 py-4 border-r last:border-r-0 border-neutral-100/80">
            <span class="block text-3xl sm:text-4xl font-display font-extrabold text-primary tracking-tight">
                {{ $val }}
            </span>
            <span class="block text-xs font-semibold text-neutral/50 uppercase tracking-wider">
                {{ $label }}
            </span>
        </div>
    @endforeach
</div>
