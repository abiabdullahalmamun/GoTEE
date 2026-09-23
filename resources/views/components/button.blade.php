@props(['type' => 'button', 'color' => 'gray'])

@php
    $colors = [
        'blue' => 'bg-blue-500 hover:bg-blue-600 text-white',
        'red' => 'bg-red-500 hover:bg-red-600 text-white',
        'yellow' => 'bg-yellow-500 hover:bg-yellow-600 text-white',
        'gray' => 'bg-gray-500 hover:bg-gray-600 text-white',
    ];
@endphp

<button type="{{ $type }}" class="px-4 py-2 rounded {{ $colors[$color] }}">
    {{ $slot }}
</button>
