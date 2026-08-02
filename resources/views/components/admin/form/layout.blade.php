@props(['action', 'method' => 'POST', 'enctype' => null])
<form 
    x-data="{ submitting: false }" 
    x-on:submit="if(submitting) { $event.preventDefault(); return false; } submitting = true;"
    action="{{ $action }}" 
    method="{{ $method === 'GET' ? 'GET' : 'POST' }}" 
    {{ $enctype ? 'enctype='.$enctype : '' }} 
    {{ $attributes->merge(['class' => 'space-y-6 bg-white dark:bg-neutral-900']) }}
>
    @if($method !== 'GET')
        @csrf
        @if(!in_array(strtoupper($method), ['GET', 'POST']))
            @method($method)
        @endif
    @endif
    
    <div class="space-y-8" :class="{ 'opacity-50 pointer-events-none': submitting }">
        {{ $slot }}
    </div>
</form>