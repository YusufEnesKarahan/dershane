@props(['action', 'method' => 'POST'])
<form action="{{ $action }}" method="{{ $method === 'GET' ? 'GET' : 'POST' }}" {{ $attributes->merge(['class' => 'space-y-6']) }}>
    @if($method !== 'GET')
        @csrf
        @if(!in_array(strtoupper($method), ['GET', 'POST']))
            @method($method)
        @endif
    @endif
    {{ $slot }}
</form>