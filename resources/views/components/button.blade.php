@props([
    'type' => 'button',
    'variant' => 'primary',
])

@php
    $baseStyles = 'inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150';
    
    $variants = [
        'primary' => 'text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500',
        'secondary' => 'text-gray-700 bg-white border-gray-300 hover:bg-gray-50 focus:ring-indigo-500',
        'danger' => 'text-white bg-red-600 hover:bg-red-700 focus:ring-red-500',
    ];

    $classes = $baseStyles . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>
