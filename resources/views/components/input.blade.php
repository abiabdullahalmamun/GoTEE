@props(['label', 'name', 'type' => 'text', 'id' => null, 'value' => ''])

<div class="mb-4">
    <label for="{{ $id ?? $name }}" class="block text-gray-700 font-medium">{{ $label }}</label>
    <input type="{{ $type }}" name="{{ $name }}" id="{{ $id ?? $name }}" value="{{ $value }}"
           class="w-full px-3 py-2 border rounded focus:ring focus:ring-blue-300">
</div>
