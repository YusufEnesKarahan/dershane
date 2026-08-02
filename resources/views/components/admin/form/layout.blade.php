@props(['action', 'method' => 'POST', 'enctype' => null])
<form action="{{ $action }}" method="{{ $method === 'GET' ? 'GET' : 'POST' }}" {{ $enctype ? 'enctype='.$enctype : '' }} {{ $attributes->merge(['class' => 'space-y-6 bg-white dark:bg-neutral-900']) }}>
    @if($method !== 'GET')
        @csrf
        @if(!in_array(strtoupper($method), ['GET', 'POST']))
            @method($method)
        @endif
    @endif
    
    <div class="space-y-8">
        {{ $slot }}
    </div>
</form>