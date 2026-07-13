@props([
    'steps' => [], // array of label => description
])

<div {{ $attributes->merge(['class' => 'relative border-l-2 border-primary/20 pl-6 ml-4 space-y-8 font-sans']) }}>
    @foreach ($steps as $title => $desc)
        <div class="relative">
            <!-- Timeline dot -->
            <div class="absolute -left-[31px] top-1.5 bg-primary border-4 border-white w-4 h-4 rounded-full shadow-premium-sm transition-transform duration-300 hover:scale-125"></div>
            
            <h4 class="text-sm font-semibold text-neutral tracking-tight">
                {{ $title }}
            </h4>
            @if ($desc)
                <p class="text-xs text-neutral/60 mt-1 leading-relaxed">
                    {{ $desc }}
                </p>
            @endif
        </div>
    @endforeach
</div>
