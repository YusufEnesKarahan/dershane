@props(['label', 'id' => null, 'error' => null])
<div>
    <label {{ $id ? 'for='.$id : '' }} class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">
        {{ $label }}
    </label>
    {{ $slot }}
    @if($error)
        <p class="mt-1 text-sm text-red-500">{{ $error }}</p>
    @endif
</div>