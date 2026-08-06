@props(['label', 'id' => null, 'error' => null, 'required' => false, 'help' => null])
<div class="space-y-1.5">
    @if($label)
        <label {{ $id ? 'for='.$id : '' }} class="block text-sm font-medium text-slate-700 dark:text-slate-300">
            {{ $label }}
            @if($required)
                <span class="text-rose-500 ml-0.5" title="Zorunlu Alan">*</span>
            @endif
        </label>
    @endif
    
    <div class="relative">
        {{ $slot }}
    </div>

    @if($help && !$error)
        <p class="text-[13px] text-slate-500 dark:text-slate-400 flex items-start gap-1">
            <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            {{ $help }}
        </p>
    @endif

    @if($error)
        <p class="text-[13px] font-medium text-rose-500 flex items-start gap-1 mt-1">
            <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            {{ $error }}
        </p>
    @endif
</div>