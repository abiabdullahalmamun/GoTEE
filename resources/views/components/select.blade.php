@props(['label', 'name', 'id' => null])

<div class="mb-4">
    <label for="{{ $id ?? $name }}" class="block text-gray-700 font-medium">{{ $label }}</label>
    <select name="{{ $name }}" id="{{ $id ?? $name }}"
            class="w-full px-3 py-2 border rounded focus:ring focus:ring-blue-300">
        {{ $slot }}
    </select>
</div>
