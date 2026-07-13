@props([
    'items' => [], // array of question => answer
])

<div {{ $attributes->merge(['class' => 'space-y-4 max-w-3xl mx-auto font-sans']) }} x-data="{ active: null }">
    @foreach ($items as $question => $answer)
        @php $id = 'faq_' . $loop->index; @endphp
        <div class="border border-neutral-100 rounded-premium-lg overflow-hidden bg-white shadow-premium-sm transition-all duration-300">
            <!-- Accordion Button -->
            <button type="button" 
                    class="w-full text-left px-6 py-4 flex items-center justify-between text-sm font-semibold text-neutral hover:bg-neutral-50/50 transition duration-150 cursor-pointer focus:outline-none"
                    @click="active = (active === '{{ $id }}' ? null : '{{ $id }}')"
                    aria-expanded="active === '{{ $id }}'"
                    aria-controls="{{ $id }}">
                <span>{{ $question }}</span>
                <svg class="h-4 w-4 text-neutral/40 transition-transform duration-300" 
                     :class="active === '{{ $id }}' ? 'rotate-180 text-primary' : ''" 
                     fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <!-- Accordion Content -->
            <div id="{{ $id }}" 
                 class="px-6 py-4 text-xs text-neutral/70 border-t border-neutral-50 leading-relaxed bg-white"
                 x-show="active === '{{ $id }}'" 
                 x-collapse
                 style="display: none;"
                 role="region"
                 aria-labelledby="btn_{{ $id }}">
                {{ $answer }}
            </div>
        </div>
    @endforeach
</div>
